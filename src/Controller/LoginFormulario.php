<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginFormulario implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		$html = $this->renderizaHtml('login/formulario.php', [
            'titulo' => 'Login'
        ]);
        return new Response(200, [], $html);
    } 
}