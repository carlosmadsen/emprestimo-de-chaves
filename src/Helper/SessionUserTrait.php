<?php

namespace Emprestimo\Chaves\Helper;

use Emprestimo\Chaves\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
trait SessionUserTrait
{
	public function defineSessionUser(Usuario $usuario): void
    {
        $_SESSION['usuario'] = [
            'id' => $usuario->getId(),
            'login' =>  $usuario->getLogin(),
            'nome' =>  $usuario->getNome(),
            'email' =>  $usuario->getEmail(),
            'id_instituicao' =>  $usuario->getInstituicao()->getId(),
            'adm' => $usuario->ehAdm()
        ];
        $_SESSION['rodape'] = $usuario->getInstituicao()->getNome();
    }

    public function getSessionUser(): array
    {
        return array_key_exists('usuario', $_SESSION) ? $_SESSION['usuario'] : [];
    }

    public function getLoggedUser(EntityManagerInterface $entityManager): Usuario {
        $dadosUsuario = $this->getSessionUser();
        if (empty($dadosUsuario)) {
            throw new \Exception("Não foi possível identificar o usuário atual.", 1);
        }
        $repositorioUsuarios = $entityManager->getRepository(Usuario::class);
        $usuario = $repositorioUsuarios->findOneBy(['id' => $dadosUsuario['id']]);
		if (is_null($usuario)) {
			throw new \Exception("Não foi possível identificar o usuário atual.", 1);
		}
        return $usuario;
    }

    public function userVerifyAdmin() {
        $dadosUsuario = $this->getSessionUser();
        if (!$dadosUsuario['adm']) {
            throw new \Exception("Somente usuários administradores podem acessar essa operação.", 1);
        }
    }
} 