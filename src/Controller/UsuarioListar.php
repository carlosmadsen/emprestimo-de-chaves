<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;

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

class UsuarioListar implements RequestHandlerInterface
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
		$this->defineSessionFilterKey('usuarios');	
		$cleanFilterSession = $this->requestGETInteger('limparFiltro', $request) == 1;
		if ($cleanFilterSession) {
			$this->clearFilterSession();
		}
		try { 
			$usuarioAtual = $this->getLoggedUser($this->entityManager);
			if (is_null($usuarioAtual)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
			$instituicao = $usuarioAtual->getInstituicao();
			$idInstituicao = $instituicao->getId();
			$login = $this->requestPOSTString('login', $request) ? : $this->getFilterSession('login');
			$nome = $this->requestPOSTString('nome', $request) ? : $this->getFilterSession('nome');
			$ativo = $this->requestPOSTString('ativo', $request) ? : $this->getFilterSession('ativo');
			$administrador = $this->requestPOSTString('administrador', $request) ? : $this->getFilterSession('administrador');
			$idPredio = $this->requestPOSTInteger('predio', $request) ? : $this->getFilterSession('predio');	
			$temPesquisa = (!empty($login) or !empty($nome) or !empty($ativo) or !empty($administrador) or !empty($idPredio));
			$this->userVerifyAdmin();		
			if (empty($idInstituicao)) {
				throw new \Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}
			if ($temPesquisa) {
				$this->defineFilterSesssion([
					'login' => $login,
					'nome' => $nome,
					'ativo' => $ativo,
					'administrador' => $administrador,
					'predio' => $idPredio
				]);
			}
			$prediosAtivos = [];
            $predios = $instituicao->getPredios();
            foreach ($predios as $predio) {
                if ($predio->estaAtivo()) {
                    $prediosAtivos[] = $predio;
                }
            }
			$dql = 'SELECT 
				usuario 
			FROM '.Usuario::class." usuario 
			JOIN usuario.instituicao instituicao
			LEFT JOIN usuario.predios predios 
			WHERE 
				instituicao.id = ".$idInstituicao.' ';
			if (!empty($login)) {
				$dql .= " AND usuario.login = '".trim($login)."' ";
			}
			if (!empty($nome)) {
				$dql .= " AND usuario.nome like '%".trim(str_replace(' ', '%', $nome))."%' ";
			}
			if (!empty($ativo)) {
				$dql .= " AND usuario.flAtivo = '".trim($ativo)."' ";
			}
			if (!empty($administrador)) {
				$dql .= " AND usuario.flAdm = '".trim($administrador)."' ";
			}
			if (!empty($idPredio)) {
				$dql .= " AND predios.id = ".(int)$idPredio." ";
			}
			$dql .= '	
			ORDER BY 
				usuario.nome ';
			$query = $this->entityManager->createQuery($dql);
			$usuarios = $query->getResult();
			$html = $this->renderizaHtml('usuario/listar.php', [
				'usuarios' => $usuarios,
				'titulo' => 'Usuários',
				'login' => $login,
				'nome' => $nome,
				'ativo' => $ativo,
				'administrador' => $administrador,
				'predios' => $prediosAtivos,
				'idPredio' => $idPredio,
				'temPesquisa' => $temPesquisa
			]);
			return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}
    }
}