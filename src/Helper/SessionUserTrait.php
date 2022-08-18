<?php

namespace Emprestimo\Chaves\Helper;

use Emprestimo\Chaves\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
trait SessionUserTrait
{
	public function defineSessionUser(Usuario $usuario): void
    {
        $instituicao = $usuario->getInstituicao();
        $_SESSION['usuario'] = [
            'id' => $usuario->getId(),
            'login' =>  $usuario->getLogin(),
            'nome' =>  $usuario->getNome(),
            'email' =>  $usuario->getEmail(),
            'adm' => $usuario->ehAdm(),
            'instituicao' => [
                'id' => $instituicao->getId(),
                'nome' =>  $instituicao->getNome(),
                'label_documento_pessoa' => $instituicao->getLabelDocumentoPessoa(),
                'label_identificacao_pessoa' => $instituicao->getLabelIdentificacaoPessoa()
            ]
        ];       
    }

    public function getSessionUserLabelIdentificacaoPessoa(): string
    {
        return array_key_exists('usuario', $_SESSION) ? $_SESSION['usuario']['instituicao']['label_identificacao_pessoa'] : '';
    }

    public function getSessionUserLabelDocumentoPessoa(): string
    {
        return array_key_exists('usuario', $_SESSION) ? $_SESSION['usuario']['instituicao']['label_documento_pessoa'] : '';
    }

    public function getSessionUserIdInstituicao(): int
    {
        return array_key_exists('usuario', $_SESSION) ? $_SESSION['usuario']['instituicao']['id'] : 0;
    }

    public function getSessionUserNomeInstituicao(): int
    {
        return array_key_exists('usuario', $_SESSION) ? $_SESSION['usuario']['instituicao']['nome'] : 0;
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
        $usuario = $this->entityManager->find(Usuario::class, $dadosUsuario['id']);
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