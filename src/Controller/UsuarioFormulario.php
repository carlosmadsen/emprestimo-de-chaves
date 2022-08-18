<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
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

class UsuarioFormulario implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
    use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $repositorioUsuarios;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function getPrediosInstituicao()
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $this->requestGETInteger('id', $request);
        $titulo = (empty($id) ? 'Novo usuário' : 'Alterar usuário');
        $dados = $this->getFlashData();
        $this->clearFlashData();
        try {
            $this->userVerifyAdmin();
            $usuarioAtual = $this->getLoggedUser($this->entityManager);
            if (is_null($usuarioAtual)) {
                throw new \Exception("Não foi possível identificar o usuário.", 1);
            }
            $instituicao = $usuarioAtual->getInstituicao();
            $prediosSelecionados = [];
            if (empty($dados) and !empty($id)) {
                $usuario = $this->entityManager->find(Usuario::class, $id);
                if (is_null($usuario)) {
                    throw new \Exception("Não foi possível identificar o usuário.", 1);
                }
                if ($usuario->getInstituicao()->getId() != $instituicao->getId()) {
                    throw new \Exception("O usuário selecionado não é da mesma instituição do usuário atual.", 1);
                }
                $prediosUsuario = $usuario->getPredios();
                foreach ($prediosUsuario as $predio) {
                    $prediosSelecionados[] = $predio->getId();
                }
                $dados = [
                    'id' => $id,
                    'login' => $usuario->getLogin(),
                    'nome' => $usuario->getNome(),
                    'email' => $usuario->getEmail(),
                    'observacao' => $usuario->getObservacao(),
                    'administrador' => ($usuario->ehAdm() ? 'S' : 'N'),
                    'ativo' => ($usuario->estaAtivo() ? 'S' : 'N')
                ];
            }
            $prediosAtivos = [];
            $predios = $instituicao->getPredios();
            foreach ($predios as $predio) {
                if ($predio->estaAtivo()) {
                    $prediosAtivos[] = $predio;
                }
            }
            $dados['predios'] = $prediosAtivos;
            $dados['predios_selecionados'] = $prediosSelecionados;
            $html = $this->renderizaHtml('usuario/formulario.php', array_merge([
                'titulo' => $titulo
            ], $dados));
            return new Response(200, [], $html);
        } catch (\Exception $e) {
            $this->defineFlashMessage('danger', $e->getMessage());
            return new Response(302, ['Location' => '/usuarios'], null);
        }
    }
}
