<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioFormularioNovo implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;

    private $repositorioUsuarios;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioUsuarios = $this->entityManager->getRepository(Usuario::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface 
    {
        $dadosUsuario = $_SESSION['usuario'];
        $dados = (array)$_SESSION['dados'];
        var_dump($dados);
        unset($_SESSION['dados']);
		try { 
			$html = $this->renderizaHtml('usuarios/formulario.php', [
          	  'titulo' => 'Novo usuário'
        	]);
        return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineMensagem('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}