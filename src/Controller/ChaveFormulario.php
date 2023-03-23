<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

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

class ChaveFormulario implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
    use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dadosUsuario = $this->getSessionUser();
        $idInstituicao = $this->getSessionUserIdInstituicao();
        $id = $this->requestGETInteger('id', $request);
        $titulo = (empty($id) ? 'Nova chave' : 'Alterar chave');
        $dados = $this->getFlashData();
        $this->clearFlashData();
        try {
            $this->userVerifyAdmin();
            if (empty($dados) and !empty($id)) {
                $chave = $this->entityManager->find(Chave::class, $id);
                if (is_null($chave)) {
                    throw new Exception("Não foi possível identificar a chave.", 1);
                }
                $predio = $chave->getPredio();
                if ($predio->getInstituicao()->getId() != $idInstituicao) {
                    throw new Exception("O prédio selecionado não é da mesma instituição do usuário atual.", 1);
                }
                $dados = [
                    'id' => $id,
                    'idPredio' => $predio->getId(),
                    'numero' => $chave->getNumero(),
                    'descricao' => $chave->getDescricao(),
                    'ativo' => ($chave->estaAtivo() ? 'S' : 'N')
                ];
            }
            $predios = $this->getPredios($idInstituicao);
            $dados['predios'] = $predios;
            $html = $this->renderizaHtml('chave/formulario.php', array_merge([
                'titulo' => $titulo
            ], $dados));
            return new Response(200, [], $html);
        } catch (Exception $e) {
            $this->defineFlashMessage('danger', $e->getMessage());
            return new Response(302, ['Location' => '/chaves'], null);
        }
    }

    private function getPredios($idInstituicao)
    {
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
