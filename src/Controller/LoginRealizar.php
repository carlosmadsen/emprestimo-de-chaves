<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\FlashMessageTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginRealizar implements RequestHandlerInterface
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
		try {
			$login = filter_var($request->getParsedBody()['login'], FILTER_SANITIZE_STRING);
			$senha = filter_var($request->getParsedBody()['senha'], FILTER_SANITIZE_STRING);
		
			if (is_null($login) || $login === false) {
				throw new \Exception("Login ou senha inválido.", 1);
			}

			$usuario = $this->repositorioUsuarios->findOneBy(['login' => $login]);
			if (is_null($usuario) or !$usuario->senhaEstaCorreta($senha)) {
				throw new \Exception("Login ou senha inválido.", 1);
			}

			$_SESSION['usuario'] = [
				'id' => $usuario->getId(),
				'login' =>  $usuario->getLogin(),
				'nome' =>  $usuario->getNome(),
				'id_instituicao' =>  $usuario->getInstituicao()->getId(),
				'adm' => $usuario->ehAdm()
			];
			$_SESSION['rodape'] = $usuario->getInstituicao()->getNome();

			return new Response(302, ['Location' => '/emprestimos'], null);
		}
		catch (\Exception $e) {
       		$this->defineMensagem('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
     	}
	} 
}