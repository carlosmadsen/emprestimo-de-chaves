<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

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
	
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;		
    }	

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		try {
			$dadosUsuario = $this->getSessionUser();
			$idInstituicao = $this->getSessionUserIdInstituicao();
			$this->userVerifyAdmin();
			$id = $this->requestGETInteger('id', $request);
			if (is_null($id) || $id === false) {
				throw new Exception("Identificação de chave inválida.", 1);
			}
			if (empty($idInstituicao)) {
				throw new Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}			
			$chave = $this->entityManager->find(Chave::class, $id);
			if (is_null($chave)) {
				throw new Exception("Não foi possível localizar a chave.", 1);
			}
 			if ($chave->getPredio()->getInstituicao()->getId() != $idInstituicao) {
				throw new Exception("A chave selecionada é de um prédio que não é da mesma instituição do usuário atual.", 1);
            }
			$emprestimo = $chave->getEmprestimo();
			if (!is_null($emprestimo)) {
				throw new Exception('Não é permitido remover uma chave que está emprestada.', 1);
			} 
			$this->entityManager->remove($chave);
			$this->entityManager->flush();
			$this->defineFlashMessage('success', 'Chave removida com sucesso.');
		}
		catch (Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/chaves'], null);
    }
}