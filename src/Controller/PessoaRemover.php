<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Pessoa;
use Emprestimo\Chaves\Entity\Emrpestimo;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PessoaRemover implements RequestHandlerInterface
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
				throw new \Exception("Identificação de pessoa inválida.", 1);
			}
			if (empty($idInstituicao)) {
				throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}
			$pessoa = $this->entityManager->find(Pessoa::class, $id);
 			if ($pessoa->getInstituicao()->getId() != $idInstituicao) {
				throw new \Exception("A pessoa selecionada não é da mesma instituição do usuário atual.", 1);
            }
			$emprestimo = $pessoa->getEmprestimo();
			if (!is_null($emprestimo)) {
				throw new \Exception('Não é permitido remover uma pessoa que tem um empréstimo de chave.', 1);
			}
			$this->entityManager->remove($pessoa);
			$this->entityManager->flush();
			$this->defineFlashMessage('success', 'Pessoa removida com sucesso.');
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/pessoas'], null);
    }
}