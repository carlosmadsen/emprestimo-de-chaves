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
        $usuario = $this->repositorioUsuarios->findOneBy(['id' => $dadosUsuario['id']]);
        if (is_null($usuario)) {

        }
     
          
        $html = $this->renderizaHtml('emprestimos/listar.php', [
            'titulo' => 'Empréstimos'
        ]); 
        return new Response(200, [], $html);
    }
}