<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Pessoa;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

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

class PessoaListar implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
	use FlashDataTrait;
	use SessionUserTrait;
	use RequestTrait;
	use SessionFilterTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
		$this->clearFlashData();
		$this->defineSessionFilterKey('pessoas');	
		$newFilter = $this->requestGETInteger('filtrar', $request) == 1;
		$cleanFilterSession = $this->requestGETInteger('limparFiltro', $request) == 1;
		if ($cleanFilterSession) {
			$this->clearFilterSession();
		}
		try {
			$dadosUsuario = $this->getSessionUser();
			if (empty($dadosUsuario)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
			$this->userVerifyAdmin();
			$idInstituicao = $this->getSessionUserIdInstituicao();
			if (empty($idInstituicao)) {
				throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}
			$nome = $this->requestPOSTString('nome', $request) ? : (!$newFilter ? $this->getFilterSession('nome') : null);
			$identificacao = $this->requestPOSTString('identificacao', $request) ? : (!$newFilter ? $this->getFilterSession('identificacao'): null);
			$documento = $this->requestPOSTString('documento', $request) ? : (!$newFilter ? $this->getFilterSession('documento'): null);
			$temPesquisa = (!empty($nome) or !empty($identificacao) or !empty($documento));
			$dados = [
				'titulo' => 'Pessoas',
				'temPesquisa' => $temPesquisa,
				'labelIdentificacao' => $this->getSessionUserLabelIdentificacaoPessoa(),
				'labelDocumento' => $this->getSessionUserLabelDocumentoPessoa()
			];
			if ($temPesquisa) {
				$this->defineFilterSesssion([
					'nome' => $nome,
					'identificacao' => $identificacao,
					'documento' => $documento
				]);
			
				$dql = 'SELECT 
					pessoa 
				FROM '.Pessoa::class." pessoa 
				JOIN pessoa.instituicao instituicao
				WHERE 
					instituicao.id = ".$idInstituicao.' ';
				if (!empty($nome)) {
					$dql .= " AND pessoa.nome like '%".trim(str_replace(' ', '%', $nome))."%' ";
				}
				if (!empty($identificacao)) {
					$dql .= " AND pessoa.nrIdentificacao = '".trim($identificacao)."' ";
				}
				if (!empty($documento)) {
					$dql .= " AND pessoa.nrDocumento = '".trim($documento)."' ";
				}
				$dql .= '	
				ORDER BY 
					pessoa.nome ';
				$query = $this->entityManager->createQuery($dql);
				$pessoas = $query->getResult();
				$dados = array_merge($dados, [
					'pessoas' => $pessoas,					
					'nome' => $nome,
					'identificacao' => $identificacao,
					'documento' => $documento
				]);
			}
			else {
				$this->clearFilterSession();
			}
			$html = $this->renderizaHtml('pessoa/listar.php', $dados);
			return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}
