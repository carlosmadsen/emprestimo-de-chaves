<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

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

    private $repositorioUsuarios;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioUsuarios = $this->entityManager->getRepository(Usuario::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $dadosUsuario = $this->getSessionUser();
        $id = array_key_exists('id', $request->getQueryParams()) ? filter_var($request->getQueryParams()['id'], FILTER_VALIDATE_INT) : null;
        $titulo = ( empty($id) ? 'Novo usuário' : 'Alterar usuário');
        $dados = $this->getFlashData();
        $this->clearFlashData();
        try {
            if (!$dadosUsuario['adm']) {
                throw new \Exception("Somente usuários administradores podem acessar essa operação.", 1);
            }
            if (empty($dados) and !empty($id)) {
                $usuario = $this->repositorioUsuarios->findOneBy(['id' => $id]);
                if (is_null($usuario)) {
				    throw new \Exception("Não foi possível identificar o usuário.", 1);
			    }
                if ($usuario->getInstituicao()->getId() != $dadosUsuario['id_instituicao']) {
                    throw new \Exception("O usuário selecionado não é da mesma instituição do usuário atual.", 1);
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
			$html = $this->renderizaHtml('usuarios/formulario.php', array_merge([
          	  'titulo' => $titulo
            ], $dados));
            return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/usuarios'], null);
		}
    }
}