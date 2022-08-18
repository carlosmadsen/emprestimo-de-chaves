<?php

namespace Emprestimo\Chaves\Controller;

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

class PredioFormulario implements RequestHandlerInterface
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
        $titulo = (empty($id) ? 'Novo prédio' : 'Alterar prédio');
        $dados = $this->getFlashData();
        $this->clearFlashData();
        try {
            $this->userVerifyAdmin();
            if (empty($dados) and !empty($id)) {
                $predio = $this->entityManager->find(Predio::class, $id);
                if (is_null($predio)) {
                    throw new \Exception("Não foi possível identificar o prédio.", 1);
                }
                if ($predio->getInstituicao()->getId() != $idInstituicao) {
                    throw new \Exception("O prédio selecionado não é da mesma instituição do usuário atual.", 1);
                }
                $dados = [
                    'id' => $id,
                    'nome' => $predio->getNome(),
                    'ativo' => ($predio->estaAtivo() ? 'S' : 'N')
                ];
            }
            $html = $this->renderizaHtml('predio/formulario.php', array_merge([
                'titulo' => $titulo
            ], $dados));
            return new Response(200, [], $html);
        } catch (\Exception $e) {
            $this->defineFlashMessage('danger', $e->getMessage());
            return new Response(302, ['Location' => '/predios'], null);
        }
    }
}
