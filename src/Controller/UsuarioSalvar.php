<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\FlashMessageTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioSalvar  implements RequestHandlerInterface
{
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
		$rota = '/novo-usuario';
        $dadosUsuario = $_SESSION['usuario'];
		try {
			throw new \Exception("Error Processing Request", 1);
			
		}
		catch (\Exception $e) {
            $_SESSION['dados'] = [
                'login' => $login,

            ];
			$this->defineMensagem('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => $rota], null);
    }
}