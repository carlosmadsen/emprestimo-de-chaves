<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Sala;
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

	private $repositorioPredios;
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
		$this->repositorioPredios = $this->entityManager->getRepository(Predio::class);
    }

	private function verificaTemSalas($idPredio) {
		$dql = 'SELECT 
            sala 
        FROM '.Sala::class.' sala 
        JOIN sala.predio predio
        WHERE 
            predio.id = '.(int)$idPredio;
        $query = $this->entityManager->createQuery($dql);
        $salas = $query->getResult();
		$nrSalas = count($salas);
        if ($nrSalas > 0) {
            throw new \Exception('Não é permitido remover o prédio, pois ele está relacionado a '.$nrSalas.' salas.', 1);
        }
	}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		try {
			$dadosUsuario = $this->getSessionUser();
			$this->userVerifyAdmin();
			$id = $this->requestGETInteger('id', $request);
			if (is_null($id) || $id === false) {
				throw new \Exception("Identificação de prédio inválida.", 1);
			}
			if (empty($dadosUsuario['id_instituicao'])) {
				throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}			
			$predio = $this->repositorioPredios->findOneBy(['id' => $id]);
 			if ($predio->getInstituicao()->getId() != $dadosUsuario['id_instituicao']) {
				throw new \Exception("O prédio selecionado não é da mesma instituição do usuário atual.", 1);
            }
			$this->entityManager->remove($predio);
			$this->entityManager->flush();
			$this->defineFlashMessage('success', 'Prédio removido com sucesso.');
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/predios'], null);
    }
}