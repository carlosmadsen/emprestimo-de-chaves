<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class UsuarioRemover implements RequestHandlerInterface
{
    use FlashMessageTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function verificaRemocaoUltimoAdm($idUsuario, $idInstituicao)
    {
        $dql = 'SELECT 
            usuario 
        FROM '.Usuario::class.' usuario 
        JOIN usuario.instituicao instituicao
        WHERE 
            instituicao.id = '.(int)$idInstituicao." AND 
            usuario.flAdm = 'S' AND 
			usuario.id != ".(int)$idUsuario;
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)<1) {
            throw new \Exception('Não é permitido remover o último usuário administrativo.', 1);
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $dadosUsuario = $this->getSessionUser();
            $idInstituicao = $this->getSessionUserIdInstituicao();
            $this->userVerifyAdmin();
            $id = $this->requestGETInteger('id', $request);
            if (is_null($id) || $id === false) {
                throw new \Exception("Identificação de usuário inválida.", 1);
            }
            if (empty($idInstituicao)) {
                throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
            }
            if ($id == $dadosUsuario['id']) {
                throw new \Exception("Não é permitido remover o seu próprio usuário.", 1);
            }
            $this->verificaRemocaoUltimoAdm($id, $idInstituicao);
            $usuario = $this->entityManager->find(Usuario::class, $id);
            if ($usuario->getInstituicao()->getId() != $idInstituicao) {
                throw new \Exception("O usuário selecionado não é da mesma instituição do usuário atual.", 1);
            }
            $this->entityManager->remove($usuario);
            $this->entityManager->flush();
            $this->defineFlashMessage('success', 'Usuário removido com sucesso.');
        } catch (\Exception $e) {
            $this->defineFlashMessage('danger', $e->getMessage());
        }
        return new Response(302, ['Location' => '/usuarios'], null);
    }
}
