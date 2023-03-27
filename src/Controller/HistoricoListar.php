<?php

namespace Emprestimo\Chaves\Controller;

use Exception;
use DateTime;

use Emprestimo\Chaves\Entity\Historico;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;
use Emprestimo\Chaves\Helper\SessionFilterTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class HistoricoListar implements RequestHandlerInterface {
	use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
	use SessionUserTrait;
	use RequestTrait;
	use SessionFilterTrait;

	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface {
		$dadosUsuario = $this->getSessionUser();
		$this->defineSessionFilterKey('historicos');
		$newFilter = $this->requestGETInteger('filtrar', $request) == 1;
		$cleanFilterSession = $this->requestGETInteger('limparFiltro', $request) == 1;
		if ($cleanFilterSession) {
			$this->clearFilterSession();
		}
		$idInstituicao = $this->getSessionUserIdInstituicao();
		$numeroChave = $this->requestPOSTString('numeroChave', $request) ? : (!$newFilter ? $this->getFilterSession('numeroChave') : null);
		$predio = $this->requestPOSTString('predio', $request) ? : (!$newFilter ? $this->getFilterSession('predio') : null);
		$pessoa = $this->requestPOSTString('pessoa', $request) ? : (!$newFilter ? $this->getFilterSession('pessoa') : null);
		$dataInicial = $this->requestPOSTString('data_inicial', $request) ? : (!$newFilter ? $this->getFilterSession('data_inicial') : null);
		$dataFinal = $this->requestPOSTString('data_final', $request) ? : (!$newFilter ? $this->getFilterSession('data_final') : null);
		
		$temPesquisa = (!empty($numeroChave) or !empty($predio) or !empty($pessoa) or !empty($dataInicial));
		if ($temPesquisa) {
			$this->defineFilterSesssion([
				'numeroChave' => $numeroChave,
				'predio' => $predio,
				'pessoa' => $pessoa,
				'data_inicial' => $dataInicial,
				'data_final' => $dataFinal,
			]);
		}
		else {
			$this->clearFilterSession();
		}
		try {			
			if (empty($idInstituicao)) {
				throw new Exception('Não foi possível identificar a instituição do usuário atual.', 1);
			}
			$historicos = $this->getHistoricos($idInstituicao, $numeroChave, $predio, $pessoa, $dataInicial, $dataFinal);		
			$html = $this->renderizaHtml('historico/listar.php', [
				'titulo' => 'Históricos',
				'historicos' => $historicos,	
				'temPesquisa' => $temPesquisa,			
				'numeroChave' => $numeroChave,				
				'predio' => $predio,				
				'pessoa' => $pessoa,				
				'dataInicial' => $dataInicial,				
				'dataFinal' => $dataFinal,				
			]);
			return new Response(200, [], $html);
		} catch (Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
	}

	private function getHistoricos($idInstituicao, $numeroChave, $predio, $pessoa, $dataInicial, $dataFinal) {
		if (empty($idInstituicao)) {
			return [];
		}
		$temParametro = false;
		$dql = 'SELECT 
			historico 
		FROM ' . Historico::class . ' historico 
		JOIN historico.instituicao instituicao						
		WHERE 				
			instituicao.id = ' . $idInstituicao . ' ';
		if (!empty($numeroChave)) {
			$temParametro = true;
			$dql .= " AND historico.numeroChave = '" .  $numeroChave . "' ";
		}
		if (!empty($predio)) {
			$temParametro = true;
			$dql .= " AND historico.nomePredio like '%" .  trim(str_replace(' ', '%', $predio)) . "%' ";
		}
		if (!empty($pessoa)) {
			$temParametro = true;
			$dql .= " AND historico.nomePessoa like '%" .  trim(str_replace(' ', '%', $pessoa)) . "%' ";
		}	
		if (!empty($dataInicial)) {
			$temParametro = true;
			$dql .= " AND historico.dtEmprestimo >= '".$dataInicial." 00:00:00'  ";
		}
		if (!empty($dataFinal)) {
			$temParametro = true;
			$dql .= " AND historico.dtEmprestimo <= '".$dataFinal." 23:59:59'  ";
		}			
		$dql .= '	
		ORDER BY 
			historico.dtEmprestimo DESC';
		if (!$temParametro) {
			return [];
		} else {
			$query = $this->entityManager->createQuery($dql);
			return $query->getResult();
		}
	}	
}
