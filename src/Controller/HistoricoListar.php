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
		
		$temPesquisa = (!empty($numeroChave));
		if ($temPesquisa) {
			$this->defineFilterSesssion([
				'numeroChave' => $numeroChave,
			]);
		}
		else {
			$this->clearFilterSession();
		}
		try {			
			if (empty($idInstituicao)) {
				throw new Exception('Não foi possível identificar a instituição do usuário atual.', 1);
			}
			$historicos = $this->getHistoricos($idInstituicao, $numeroChave);			
			$html = $this->renderizaHtml('historico/listar.php', [
				'titulo' => 'Históricos',
				'historicos' => $historicos,	
				'temPesquisa' => $temPesquisa,			
				'numeroChave' => $numeroChave,				
			]);
			return new Response(200, [], $html);
		} catch (Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
	}

	private function getHistoricos($idInstituicao, $numeroChave) {
		if (empty($idInstituicao)) {
			return [];
		}
		$dql = 'SELECT 
			historico 
		FROM ' . Historico::class . ' historico 
		JOIN historico.instituicao instituicao						
		WHERE 				
			instituicao.id = ' . $idInstituicao . ' ';
		if (!empty($numeroChave)) {
			$dql .= " AND historico.numeroChave = '" .  $numeroChave . "' ";
		}		
		$dql .= '	
		ORDER BY 
			historico.dtEmprestimo DESC';
		$query = $this->entityManager->createQuery($dql);
		return $query->getResult();
	}	
}
