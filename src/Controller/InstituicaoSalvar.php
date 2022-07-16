<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Doctrine\ORM\EntityManagerInterface;

class InstituicaoSalvar  implements RequestHandlerInterface
{
	use FlashMessageTrait;
	use SessionUserTrait;
	use RequestTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

	private function verificaDuplicacaoSigla($sigla, $idInstituicao = null) {
        $dql = 'SELECT 
            i 
        FROM '.Instituicao::class." i 
        WHERE 
            i.sigla = '".trim($sigla)."' ";
        if (!empty($idInstituicao)) {
            $dql .= ' AND i.id != '.(int)$idInstituicao;
        }
        $query = $this->entityManager->createQuery($dql);
        $instituicoes = $query->getResult();
        if (count($instituicoes)>0) {
            throw new \Exception('A sigla "'.$sigla.'" já está sendo utilizado pela instituição "'.$instituicoes[0]->getNome().'".', 1);
        }
    }

	private function verificaDuplicacaoNome($nome, $idInstituicao = null) {
        $dql = 'SELECT 
            i 
        FROM '.Instituicao::class." i 
        WHERE 
            i.nome = '".trim($nome)."' ";
        if (!empty($idInstituicao)) {
            $dql .= ' AND i.id != '.(int)$idInstituicao;
        }
        $query = $this->entityManager->createQuery($dql);
        $instituicoes = $query->getResult();
        if (count($instituicoes)>0) {
            throw new \Exception('O nome "'.$nome.'" já está sendo utilizado pela instituição de sigla "'.$instituicoes[0]->getSigla().'".', 1);
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface 
    {
		try {
			$this->userVerifyAdmin();
			$sigla = $this->getPOSTString('sigla', $request);
			if (empty($sigla)) {
				throw new \Exception("Sigla não informada.", 1);
			}
			$nome = $this->getPOSTString('nome', $request);
			if (empty($nome)) {
				throw new \Exception("Nome não informado.", 1);
			}

			$usuario = $this->getLoggedUser($this->entityManager);
			$instituicao = $usuario->getInstituicao();
			$this->verificaDuplicacaoSigla($sigla, $instituicao->getId());
			$this->verificaDuplicacaoNome($nome, $instituicao->getId());

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