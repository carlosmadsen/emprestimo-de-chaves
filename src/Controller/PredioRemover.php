<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PredioRemover implements RequestHandlerInterface
{
    use FlashMessageTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function verificaTemChaves($idPredio)
    {
        $dql = 'SELECT 
            COUNT(chave) 
        FROM '.Chave::class.' chave 
        JOIN chave.predio predio
        WHERE 
            predio.id = '.(int)$idPredio;
        $query = $this->entityManager->createQuery($dql);
        $nrChaves = $query->getSingleScalarResult();
        if ($nrChaves > 0) {
            throw new \Exception('Não é permitido remover o prédio, pois ele está relacionado a '.$nrChaves.' chaves.', 1);
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
                throw new \Exception("Identificação de prédio inválida.", 1);
            }
            if (empty($idInstituicao)) {
                throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
            }
            $this->verificaTemChaves($id);
            $predio = $this->entityManager->find(Predio::class, $id);
            if ($predio->getInstituicao()->getId() != $idInstituicao) {
                throw new \Exception("O prédio selecionado não é da mesma instituição do usuário atual.", 1);
            }
            $this->entityManager->remove($predio);
            $this->entityManager->flush();
            $this->defineFlashMessage('success', 'Prédio removido com sucesso.');
        } catch (\Exception $e) {
            $this->defineFlashMessage('danger', $e->getMessage());
        }
        return new Response(302, ['Location' => '/predios'], null);
    }
}
