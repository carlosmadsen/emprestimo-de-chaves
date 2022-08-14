<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EmprestimosListar  implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
    use SessionUserTrait;
 
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;      
    }

    public function handle(ServerRequestInterface $request): ResponseInterface 
    {
        $usuario = $this->getLoggedUser($this->entityManager);
             
        $html = $this->renderizaHtml('emprestimo/listar.php', [
            'titulo' => 'Empréstimos: '
        ]); 
        return new Response(200, [], $html);
    }
}