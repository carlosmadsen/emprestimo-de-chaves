<?php

namespace Emprestimo\Chaves\Helper;

use Emprestimo\Chaves\Entity\Usuario;

trait SessionUserTrait
{
	public function defineSessionUser(Usuario $usuario): void
    {
        $_SESSION['usuario'] = [
            'id' => $usuario->getId(),
            'login' =>  $usuario->getLogin(),
            'nome' =>  $usuario->getNome(),
            'id_instituicao' =>  $usuario->getInstituicao()->getId(),
            'adm' => $usuario->ehAdm()
        ];
        $_SESSION['rodape'] = $usuario->getInstituicao()->getNome();
    }

    public function getSessionUser(): array
    {
        return $_SESSION['usuario'];
    }
} 