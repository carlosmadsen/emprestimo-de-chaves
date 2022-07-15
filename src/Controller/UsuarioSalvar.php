<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioSalvar  implements RequestHandlerInterface
{
	use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;

    private $repositorioUsuarios;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioUsuarios = $this->entityManager->getRepository(Usuario::class);
    }

    private function verificaDuplicacaoLogin($login, $idInstituicao, $idUsuario = null) {
        $dql = 'SELECT 
            usuario 
        FROM '.Usuario::class.' usuario 
        JOIN usuario.instituicao instituicao
        WHERE 
            instituicao.id = '.(int)$idInstituicao." AND 
            usuario.login = '".trim($login)."' ";
        if (!empty($idUsuario)) {
            $dql .= ' AND usuario.id != '.(int)$idUsuario;
        }
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)>0) {
            throw new \Exception('O login "'.$usuarios[0]->getLogin().'" já está sendo utilizado pelo usuário "'.$usuarios[0]->getNome().'".', 1);
        }
    }

    private function verificaUltimoAdm($idUsuario, $idInstituicao) {
		$dql = 'SELECT 
            usuario 
        FROM '.Usuario::class.' usuario 
        JOIN usuario.instituicao instituicao
        WHERE 
            instituicao.id = '.(int)$idInstituicao." AND 
            usuario.flAdm = 'S' AND 
			usuario.id != ".(int)$idUsuario;
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)<1) {
            throw new \Exception('Não é permitido remover o último usuário administrativo.', 1);
        }
	}

    private function verificaUltimoAtivo($idUsuario, $idInstituicao) {
		$dql = 'SELECT 
            usuario 
        FROM '.Usuario::class.' usuario 
        JOIN usuario.instituicao instituicao
        WHERE 
            instituicao.id = '.(int)$idInstituicao." AND 
            usuario.flAtivo = 'S' AND 
			usuario.id != ".(int)$idUsuario;
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)<1) {
            throw new \Exception('Não é permitido remover o último usuário ativo.', 1);
        }
	}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		$dadosUsuario = $this->getSessionUser();
        $dados = $request->getParsedBody();
        $id = array_key_exists('id', $request->getQueryParams()) ? filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT) : null;
        $login = array_key_exists('login', $dados) ? filter_var($dados['login'], FILTER_SANITIZE_STRING) : '';
        $nome = array_key_exists('nome', $dados) ? filter_var($dados['nome'], FILTER_SANITIZE_STRING) : '';
        $senha = array_key_exists('senha', $dados) ? filter_var($dados['senha'], FILTER_SANITIZE_STRING) : '';
        $email = array_key_exists('email', $dados) ? filter_var($dados['email'], FILTER_SANITIZE_STRING) : '';
        $observacao = array_key_exists('observacao', $dados) ? filter_var($dados['observacao'], FILTER_SANITIZE_STRING) : '';
	    $administrador = array_key_exists('administrador', $dados) ? filter_var($dados['administrador'], FILTER_SANITIZE_STRING) : 'N';
	    $ativo = array_key_exists('ativo', $dados) ? filter_var($dados['ativo'], FILTER_SANITIZE_STRING) : 'S';
		try {
            if (!$dadosUsuario['adm']) {
                throw new \Exception("Somente usuários administradores podem acessar essa operação.", 1);
            }
            if (empty($login)) {
                throw new \Exception("Login não informado.", 1);
            }
		    if (empty($nome)) {
                throw new \Exception("Nome não informado.", 1);
            }
		    if (empty($email)) {
                throw new \Exception("E-mail não informado.", 1);
            }
			$usuarioAtual = $this->repositorioUsuarios->findOneBy(['id' => $dadosUsuario['id']]);
			if (is_null($usuarioAtual)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
            $instituicao = $usuarioAtual->getInstituicao();
            $this->verificaDuplicacaoLogin($login, $instituicao->getId(), $id);
            $usuario = new Usuario();
            $usuario->setInstituicao($instituicao);
            $usuario->setLogin($login);
            $usuario->setNome($nome);
		    $usuario->setEmail($email);
			$usuario->setObservacao($observacao);
		    $usuario->setAdm($administrador == 'S');
		    $usuario->setAtivo($ativo == 'S');
            if (!is_null($id) && $id !== false) { //atualizar 
                if (empty($senha)) {
                    $usuarioOriginal = $this->repositorioUsuarios->findOneBy(['id' => $id]);
                    $usuario->setSenha($usuarioOriginal->getSenha(), false);
                }
                else {
                    $usuario->setSenha($senha);
                }
                if (!$usuario->ehAdm()) {
                    $this->verificaUltimoAdm($id, $instituicao->getId());
                }
                if (!$usuario->estaAtivo()) {
                    $this->verificaUltimoAtivo($id, $instituicao->getId());
                }
                $usuario->setId($id);
                $this->entityManager->merge($usuario);
                $this->defineFlashMessage('success', 'Usuário alterado com sucesso.');
                if ($dadosUsuario['id'] == $id) {
                    $this->defineSessionUser($usuario);
                }
            } else { //inserir 
                if (empty($senha)) {
                    throw new \Exception("Senha não informada.", 1);
                }
                $usuario->setSenha($senha);
                $this->entityManager->persist($usuario);
                $this->defineFlashMessage('success', 'Usuário cadastrado com sucesso.');
            }
            $this->entityManager->flush();
            $rota = '/usuarios';
            $this->clearFlashData();
		}
		catch (\Exception $e) {
            $this->defineFlashData([
                'id' => $id,
                'login' => $login,
                'nome' => $nome,
                'email' => $email,
                'observacao' => $observacao,
                'administrador' => $administrador,
                'ativo' => $ativo
            ]);
            if (!empty($id)) {
                $rota = '/alterar-usuario?id='.$id;
            }
            else {
                $rota = '/novo-usuario';
            }
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => $rota], null);
    }
}