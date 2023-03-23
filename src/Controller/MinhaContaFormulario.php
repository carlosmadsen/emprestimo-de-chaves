<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class MinhaContaFormulario implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
   
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dadosUsuario = $this->getSessionUser();             
        $flashData = $this->getFlashData();
		$dados = (!empty($flashData) ? $flashData : $dadosUsuario);
        $this->clearFlashData();
        try {    
			if (empty($dadosUsuario)) {
				throw new Exception('Não foi possível identificar o usuário atual.', 1);
			}
			$html = $this->renderizaHtml('minha_conta/formulario.php', array_merge([
          	  'titulo' => 'Minha conta'
            ], $dados));
            return new Response(200, [], $html);
		}
		catch (Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}