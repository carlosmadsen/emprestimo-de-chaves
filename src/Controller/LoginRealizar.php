<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

use Emprestimo\Chaves\Entity\Usuario;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginRealizar implements RequestHandlerInterface
{
	use FlashMessageTrait;
	use SessionUserTrait;
	use RequestTrait;

    private $repositorioUsuarios;
	private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
		$this->entityManager = $entityManager;
        $this->repositorioUsuarios = $this->entityManager->getRepository(Usuario::class);
    }

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		try {
			$login = $this->requestPOSTString('login', $request);
			$senha = $this->requestPOSTString('senha', $request);
					
			if (is_null($login) || $login === false) {
				throw new Exception("Login ou senha inválido.", 1);
			}

			$usuario = $this->repositorioUsuarios->findOneBy(['login' => $login]);
			if (is_null($usuario) or !$usuario->senhaEstaCorreta($senha)) {
				throw new Exception("Login ou senha inválido.", 1);
			}
			if (!$usuario->estaAtivo()) {
				throw new Exception("Este usuário não está mais ativo.", 1);
			}

			$this->defineSessionUser($usuario);
			return new Response(302, ['Location' => '/emprestimos'], null);
		}
		catch (Exception $e) {
       		$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
     	}
	} 
}