<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class InstituicaoFormulario  implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
	use SessionUserTrait;

    private $repositorioUsuarios;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioUsuarios = $this->entityManager->getRepository(Usuario::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface 
    {
        $dadosUsuario = $this->getSessionUser();
		try { 
			if (!$dadosUsuario['adm']) {
                throw new \Exception("Somente usuários administradores podem acessar essa operação.", 1);
            }
			$usuario = $this->repositorioUsuarios->findOneBy(['id' => $dadosUsuario['id']]);
			if (is_null($usuario)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
			$instituicao = $usuario->getInstituicao();
			$html = $this->renderizaHtml('instituicao/formulario.php', [
				'titulo' => 'Instituição',
				'sigla' => $instituicao->getSigla(),
				'nome' => $instituicao->getNome(),
			]); 
			return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}