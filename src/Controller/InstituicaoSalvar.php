<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class InstituicaoSalvar  implements RequestHandlerInterface
{
	use FlashMessageTrait;
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
		$dados = $request->getParsedBody();
		try {
			if (!$dadosUsuario['adm']) {
                throw new \Exception("Somente usuários administradores podem acessar essa operação.", 1);
            }
			$sigla = array_key_exists('sigla', $dados) ? filter_var($dados['sigla'], FILTER_SANITIZE_STRING) : '';
			if (empty($sigla)) {
				throw new \Exception("Sigla não informada.", 1);
			}
			$nome = array_key_exists('nome', $dados) ? filter_var($dados['nome'], FILTER_SANITIZE_STRING) : '';
			if (empty($nome)) {
				throw new \Exception("Nome não informado.", 1);
			}
			$usuario = $this->repositorioUsuarios->findOneBy(['id' => $dadosUsuario['id']]);
			if (is_null($usuario)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
			$instituicao = $usuario->getInstituicao();
			$instituicao->setSigla($sigla);
			$instituicao->setNome($nome);
 			$this->entityManager->merge($instituicao);
 			$this->entityManager->flush();
			$this->defineFlashMessage('success', 'Informações da instituição atualizadas com sucesso.');
			$_SESSION['rodape'] = $nome;
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => '/instituicao'], null);
    }
}