<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;


use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class MinhaContaSalvar  implements RequestHandlerInterface
{
	use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		$dadosUsuario = $this->getSessionUser();
        $login = $this->requestPOSTString('login', $request);
        $nome = $this->requestPOSTString('nome', $request);
        $email = $this->requestPOSTString('email', $request);        
	    $senhaAtual = $this->requestPOSTString('senha_atual', $request);        
	    $alterarSenha = $this->requestPOSTString('alterar_senha', $request) == 'S';       
        $novaSenha1 = $this->requestPOSTString('nova_senha1', $request);   
        $novaSenha2 = $this->requestPOSTString('nova_senha2', $request);    
		try {
            if (empty($dadosUsuario)) {
				throw new \Exception('Não foi possível identificar o usuário atual.', 1);
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
            if (empty($senhaAtual)) {
                throw new \Exception("Senha atual não informada.", 1);
            }
            $this->verificaDuplicacaoLogin($login, $dadosUsuario['id_instituicao'], $dadosUsuario['id_instituicao']);
            $usuario = $this->getLoggedUser($this->entityManager);
            if (!$usuario->senhaEstaCorreta($senhaAtual)) {
				throw new \Exception("Senha atual inválida.", 1);
			}
            if ($alterarSenha) {
                if (empty($novaSenha1) or empty($novaSenha2)) {
                    throw new \Exception("Uma das novas senhas não foi informada.", 1);
                }
                if ($novaSenha1 != $novaSenha2) {
                    throw new \Exception("As novas senhas não conferem.", 1);
                }
                $usuario->setSenha($novaSenha1);
            }
            $usuario->setLogin($login);
            $usuario->setNome($nome);
		    $usuario->setEmail($email);
            $this->entityManager->merge($usuario);
            $this->entityManager->flush();

            $this->defineSessionUser($usuario);
            $this->clearFlashData();
            $this->defineFlashMessage('success', 'Conta atualizada com sucesso.');            
		}
		catch (\Exception $e) {
            $this->defineFlashData([
                'login' => $login,
                'nome' => $nome,
                'email' => $email               
            ]);           
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/minha-conta'], null);
    }
}