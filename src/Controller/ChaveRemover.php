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

class ChaveRemover implements RequestHandlerInterface
{
	use FlashMessageTrait;
	use SessionUserTrait;
	use RequestTrait;

	private $repositorioDeChaves;
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
		$this->repositorioDeChaves = $this->entityManager->getRepository(Chave::class);
    }

	private function verificaTemEmprestimos($idChave) {
		$dql = 'SELECT 
            COUNT(emprestimo) 
        FROM '.Emprestimo::class.' emprestimo 
        JOIN emprestimo.chave chave
        WHERE 
            chave.id = '.(int)$idChave;
        $query = $this->entityManager->createQuery($dql);
		$nrEmprestimos = $query->getSingleScalarResult();  
        if ($nrEmprestimos > 0) {
            throw new \Exception('Não é permitido remover a chave, pois ela está relacionada a '.$nrEmprestimos.' empréstimos.', 1);
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
				throw new \Exception("Identificação de chave inválida.", 1);
			}
			if (empty($idInstituicao)) {
				throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}			
			$chave = $this->repositorioDeChaves->findOneBy(['id' => $id]);
 			if ($chave->getPredio()->getInstituicao()->getId() != $idInstituicao) {
				throw new \Exception("A chave selecionada é de um prédio que não é da mesma instituição do usuário atual.", 1);
            }
			$this->entityManager->remove($chave);
			$this->entityManager->flush();
			$this->defineFlashMessage('success', 'Chave removida com sucesso.');
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/chaves'], null);
    }
}