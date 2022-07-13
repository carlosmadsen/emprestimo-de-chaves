<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Curso;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EmprestimosListar  implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
    //private $repositorioDeCursos;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        //$this->repositorioDeCursos = $this->entityManager->getRepository(Curso::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //$cursos = $this->repositorioDeCursos->findAll();
        $html = $this->renderizaHtml('emprestimos/listar.php', [
            /*'cursos' => $cursos,*/
            'titulo' => 'Empréstimos'
        ]); 
        return new Response(200, [], $html);
    }
}