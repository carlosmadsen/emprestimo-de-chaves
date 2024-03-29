<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

use Emprestimo\Chaves\Entity\Predio;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;
use Emprestimo\Chaves\Helper\SessionFilterTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PredioListar implements RequestHandlerInterface {
	use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
	use FlashDataTrait;
	use SessionUserTrait;
	use RequestTrait;
	use SessionFilterTrait;

	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface {
		$dadosUsuario = $this->getSessionUser();
		$this->clearFlashData();
		$this->defineSessionFilterKey('predios');	
		$newFilter = $this->requestGETInteger('filtrar', $request) == 1;	
		$cleanFilterSession = $this->requestGETInteger('limparFiltro', $request) == 1;
		if ($cleanFilterSession) {
			$this->clearFilterSession();
		}
		$idInstituicao = $this->getSessionUserIdInstituicao();
		$nome = $this->requestPOSTString('nome', $request) ? : (!$newFilter ? $this->getFilterSession('nome') : null);
		$ativo = $this->requestPOSTString('ativo', $request) ? : (!$newFilter ? $this->getFilterSession('ativo') : null);
		$temPesquisa = (!empty($nome) or !empty($ativo));
		if ($temPesquisa) {			
			$this->defineFilterSesssion([
				'nome' => $nome,
				'ativo' => $ativo
			]);
		}
		else {
			$this->clearFilterSession();
		}
		try {
			$this->userVerifyAdmin();
			if (empty($idInstituicao)) {
				throw new Exception('Não foi possível identificar a instituição do usuário atual.', 1);
			}
			$dql = 'SELECT 
				predio 
			FROM ' . Predio::class . ' predio 
			JOIN predio.instituicao instituicao
			LEFT JOIN predio.usuarios usuarios 
			WHERE 
				instituicao.id = ' . $idInstituicao . ' ';
			if (!empty($nome)) {
				$dql .= " AND predio.nome like '%" . trim(str_replace(' ', '%', $nome)) . "%' ";
			}
			if (!empty($ativo)) {
				$dql .= " AND predio.flAtivo = '" . trim($ativo) . "' ";
			}
			$dql .= '	
			ORDER BY 
				predio.nome ';
			$query = $this->entityManager->createQuery($dql);
			$predios = $query->getResult();
			$html = $this->renderizaHtml('predio/listar.php', [
				'predios' => $predios,
				'titulo' => 'Prédios',
				'nome' => $nome,
				'ativo' => $ativo,
				'temPesquisa' => $temPesquisa,
			]);
			return new Response(200, [], $html);
		} catch (Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
	}
}
