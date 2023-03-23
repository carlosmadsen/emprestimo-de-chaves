<?php

namespace Emprestimo\Chaves\Controller;

use DateTime;
use Exception;

use Emprestimo\Chaves\Entity\Historico;
use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Pessoa;
use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Emprestimo;
use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EmprestimoDevolver implements RequestHandlerInterface
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
			$usuarioAtual = $this->getLoggedUser($this->entityManager); 			
			$idInstituicao = $this->getSessionUserIdInstituicao();
			$this->userVerifyAdmin();
			$id = $this->requestGETInteger('id', $request);
			if (is_null($id) || $id === false) {
				throw new Exception("Identificação de empréstimo inválida.", 1);
			}
			if (empty($idInstituicao)) {
				throw new Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}			
			$emprestimo = $this->entityManager->find(Emprestimo::class, $id);
			if (is_null($emprestimo)) {
				throw new Exception("Não foi possível localizar o empréstimo.", 1);
        	}
			$chave = $emprestimo->getChave();
 			if ($chave->getPredio()->getInstituicao()->getId() != $idInstituicao) {
				throw new Exception("O empréstimo selecionado é de um prédio que não é da mesma instituição do usuário atual.", 1);
            }
			$historicoAberto = $this->getHistoricoAberto($chave, $usuarioAtual);
			if (!is_null($historicoAberto)) {
				$historicoAberto->setLoginUsuarioDevolucao($usuarioAtual->getLogin());
				$historicoAberto->setNomeUsuarioDevolucao($usuarioAtual->getNome());				
				$historicoAberto->setDtDevolucao(new DateTime("now")); 		
				$this->entityManager->merge($historicoAberto);		
			}
			$this->entityManager->remove($emprestimo);
			$this->entityManager->flush();
			$this->defineFlashMessage('success', 'Empréstimo devolvido com sucesso.');
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/emprestimos'], null);
    }

	private function getHistoricoAberto(Chave $chave, Usuario $usuarioAtual): ?Historico {
		$predio = $chave->getPredio();
		$instituicao = $predio->getInstituicao();
		$dql = 'SELECT 
            historico 
        FROM '.Historico::class." historico 
        JOIN historico.instituicao instituicao
        WHERE 
            instituicao.id = ".$instituicao->getId()." 
			AND historico.loginUsuarioEmprestimo = '".$usuarioAtual->getLogin()."'
			AND historico.loginUsuarioDevolucao IS NULL 
			AND historico.dtDevolucao IS NULL 
			AND historico.numeroChave = '".$chave->getNumero()."'
			AND historico.nomePredio = '".$predio->getNome()."'
		";       
		$query = $this->entityManager->createQuery($dql);
		$historicos = $query->getResult();
		$nrResultados = count($historicos);
		return $nrResultados == 1 ? $historicos[0] : null;
	}
}