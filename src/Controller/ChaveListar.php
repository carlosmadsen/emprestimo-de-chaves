<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Chave;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class ChaveListar implements RequestHandlerInterface {
	use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
	use FlashDataTrait;
	use SessionUserTrait;
	use RequestTrait;
	
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	public function handle(ServerRequestInterface $request): ResponseInterface {
		$dadosUsuario = $this->getSessionUser();
		$this->clearFlashData();
		$idInstituicao = (int) $dadosUsuario['id_instituicao'];
		$idPredio = $this->requestPOSTInteger('predio', $request);
		$numero = $this->requestPOSTString('numero', $request);
		$descricao = $this->requestPOSTString('descricao', $request);
		$ativo = $this->requestPOSTString('ativo', $request);
		$temPesquisa = (!empty($idPredio) or !empty($numero) or !empty($ativo));
		try {
			$this->userVerifyAdmin();
			if (empty($idInstituicao)) {
				throw new \Exception('Não foi possível identificar a instituição do usuário atual.', 1);
			}
			$predios = $this->getPredios($idInstituicao);
			$chaves = $this->getChaves($idInstituicao, $idPredio, $numero, $descricao, $ativo);
			$html = $this->renderizaHtml('chaves/listar.php', [
				'titulo' => 'Chaves',
				'chaves' => $chaves,
				'predios' => $predios,
				'idPredio' => $idPredio,
				'numero' => $numero,
				'descricao' => $descricao,
				'ativo' => $ativo,
				'temPesquisa' => $temPesquisa,
			]);
			return new Response(200, [], $html);
		} catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
	}

	private function getChaves($idInstituicao, $idPredio, $numero, $descricao, $ativo) {
		if (empty($idPredio)) {
			return [];
		}
		$dql = 'SELECT 
					chave 
				FROM ' . Chave::class . ' chave 
				JOIN chave.predio predio 
				JOIN predio.instituicao instituicao						
				WHERE 
					predio.id = '.$idPredio.' AND 
					instituicao.id = ' . $idInstituicao . ' ';
		if (!empty($numero)) {
			$dql .= " AND chave.numero = '" .  $numero . "' ";
		}
		if (!empty($descricao)) {
			$dql .= " AND chave.descricao like '%" . trim(str_replace(' ', '%', $descricao)) . "%' ";
		}
		if (!empty($ativo)) {
			$dql .= " AND chave.flAtivo = '" . trim($ativo) . "' ";
		}
		$dql .= '	
				ORDER BY 
					chave.numero ';
		$query = $this->entityManager->createQuery($dql);
		return $query->getResult();
	}

	private function getPredios($idInstituicao) {
		$dql = 'SELECT 
			predio 
		FROM ' . Predio::class . ' predio 
		JOIN predio.instituicao instituicao
		LEFT JOIN predio.usuarios usuarios 
		WHERE 
			instituicao.id = ' . $idInstituicao . ' 		
		ORDER BY 
			predio.nome ';
		$query = $this->entityManager->createQuery($dql);
		return  $query->getResult();
	}
}
