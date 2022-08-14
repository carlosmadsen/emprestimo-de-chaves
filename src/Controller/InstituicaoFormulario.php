<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Doctrine\ORM\EntityManagerInterface;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class InstituicaoFormulario  implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
	use SessionUserTrait;
  
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;        
    }

    public function handle(ServerRequestInterface $request): ResponseInterface 
    {       
		try { 
			$this->userVerifyAdmin();
			$usuario = $this->getLoggedUser($this->entityManager);			
			$instituicao = $usuario->getInstituicao();
			$html = $this->renderizaHtml('instituicao/formulario.php', [
				'titulo' => 'Instituição',
				'sigla' => $instituicao->getSigla(),
				'nome' => $instituicao->getNome(),
				'identificacao' => $instituicao->getLabelIdentificacaoPessoa(),
				'documento' => $instituicao->getLabelDocumentoPessoa(),
			]); 
			return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}