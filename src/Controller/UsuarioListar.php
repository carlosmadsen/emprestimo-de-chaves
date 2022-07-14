<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioListar implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;

    private $repositorioDeUsuarios;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioDeUsuarios = $this->entityManager->getRepository(Usuario::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		$idInstituicao = (int)$_SESSION['usuario']['id_instituicao'];
		$dados = (array)$request->getParsedBody();
		$login = array_key_exists('login', $dados) ? filter_var($dados['login'], FILTER_SANITIZE_STRING) : '';
		$nome = array_key_exists('nome', $dados) ? filter_var($dados['nome'], FILTER_SANITIZE_STRING) : '';
		$ativo = array_key_exists('ativo', $dados) ? filter_var($dados['ativo'], FILTER_SANITIZE_STRING) : '';
		$administrador = array_key_exists('administrador', $dados) ? filter_var($dados['administrador'], FILTER_SANITIZE_STRING) : '';
		$temPesquisa = (!empty($login) or !empty($nome) or !empty($ativo) or !empty($administrador));
		try { 
			if (empty($idInstituicao)) {
				throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}
			$dql = 'SELECT 
				usuario 
			FROM '.Usuario::class.' usuario 
			JOIN usuario.instituicao instituicao
			WHERE 
				instituicao.id = '.$idInstituicao.' ';
			if (!empty($login)) {
				$dql .= " AND usuario.login = '".trim($login)."' ";
			}
			if (!empty($nome)) {
				$dql .= " AND usuario.nome like '%".trim(str_replace(' ', '%', $nome))."%' ";
			}
			if (!empty($ativo)) {
				$dql .= " AND usuario.flAtivo = '".trim($ativo)."' ";
			}
			if (!empty($administrador)) {
				$dql .= " AND usuario.flAdm = '".trim($administrador)."' ";
			}
			$dql .= '	
			ORDER BY 
				usuario.nome ';
			$query = $this->entityManager->createQuery($dql);
			$usuarios = $query->getResult();
			$html = $this->renderizaHtml('usuarios/listar.php', [
				'usuarios' => $usuarios,
				'titulo' => 'Usuários',
				'login' => $login,
				'nome' => $nome,
				'ativo' => $ativo,
				'administrador' => $administrador,
				'temPesquisa' => $temPesquisa
			]);
			return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineMensagem('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}