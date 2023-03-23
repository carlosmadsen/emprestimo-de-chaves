<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

use Emprestimo\Chaves\Entity\Pessoa;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Entity\Emprestimo;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PessoaSalvar implements RequestHandlerInterface
{
    use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function verificaDuplicacaoNome($nome, $idInsituicao, $idPessoa = null)
    {
        $dql = 'SELECT 
            pessoa 
        FROM '.Pessoa::class." pessoa       
		INNER JOIN pessoa.instituicao as instituicao  
        WHERE 
            pessoa.nome = '".trim($nome)."' AND 
			instituicao.id = ".(int)$idInsituicao;
        if (!empty($idPessoa)) {
            $dql .= ' AND pessoa.id != '.(int)$idPessoa;
        }
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)>0) {
            throw new Exception('Já existe outra pessoa cadastrada com esse nome.', 1);
        }
    }    

    private function verificaDuplicacaoDocumento($documento, $idInsituicao, $label, $idPessoa = null)
    {
        $dql = 'SELECT 
            pessoa 
        FROM '.Pessoa::class." pessoa       
		INNER JOIN pessoa.instituicao as instituicao  
        WHERE 
            pessoa.nrDocumento = '".trim($documento)."' AND 
			instituicao.id = ".(int)$idInsituicao;

        if (!empty($idPessoa)) {
            $dql .= ' AND pessoa.id != '.(int)$idPessoa;
        }
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)>0) {
            throw new Exception('Já existe outra pessoa cadastrada com esse '.strtolower($label).'.', 1);
        }
    }

    private function verificaDuplicacaoIdentificacao($identificacao, $idInsituicao, $label, $idPessoa = null)
    {
        $dql = 'SELECT 
            pessoa 
        FROM '.Pessoa::class." pessoa       
		INNER JOIN pessoa.instituicao as instituicao  
        WHERE 
            pessoa.nrIdentificacao = '".trim($identificacao)."' AND 
			instituicao.id = ".(int)$idInsituicao;

        if (!empty($idPessoa)) {
            $dql .= ' AND pessoa.id != '.(int)$idPessoa;
        }
        $query = $this->entityManager->createQuery($dql);
        $usuarios = $query->getResult();
        if (count($usuarios)>0) {
            throw new Exception('Já existe outra pessoa cadastrada com esse '.strtolower($label).'.', 1);
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $this->requestGETInteger('id', $request);
        $nome = $this->requestPOSTString('nome', $request);
        $identificacao = $this->requestPOSTString('identificacao', $request);
        $documento = $this->requestPOSTString('documento', $request);
        try {
            $this->userVerifyAdmin();
            $idInstituicao = $this->getSessionUserIdInstituicao();
            $labelIdentificacao = $this->getSessionUserLabelIdentificacaoPessoa();
            $labelDocumento = $this->getSessionUserLabelDocumentoPessoa();
            if (empty($nome)) {
                throw new Exception('Nome não informado.', 1);
            }
            if (empty($documento)) {
                throw new Exception($labelDocumento . ' não informado.', 1);
            }
            if (!empty($labelIdentificacao) and empty($identificacao)) {
                throw new Exception($labelIdentificacao . ' não informado.', 1);
            }
            $this->verificaDuplicacaoNome($nome, $idInstituicao, $id);
            $this->verificaDuplicacaoDocumento($documento, $idInstituicao, $labelDocumento, $id);
            $this->verificaDuplicacaoIdentificacao($identificacao, $idInstituicao, $labelIdentificacao, $id);
            $flAlterar = (!is_null($id) && $id !== false);
            if ($flAlterar) {
                $pessoa = $this->entityManager->find(Pessoa::class, $id);
                if (is_null($pessoa)) {
                    throw new Exception('Não foi possível identificar a pessoa.', 1);
                }
                if ($pessoa->getInstituicao()->getId() != $idInstituicao) {
                    throw new Exception('A pessoa selecionada não é da mesma instituição do usuário atual.', 1);
                }
                $emprestimo = $pessoa->getEmprestimo();
                if (!is_null($emprestimo)) {
                    throw new Exception('Não é permitido alterar uma pessoa que tem um empréstimo de chave.', 1);
                }
            } else {
                $usuarioAtual = $this->getLoggedUser($this->entityManager);
                $pessoa = new Pessoa();
                $pessoa->setInstituicao($usuarioAtual->getInstituicao());
            }
            $pessoa->setNome($nome);
            $pessoa->setNrDocumento($documento);
            $pessoa->setNrIdentificacao($identificacao);
            if ($flAlterar) { //alterar
                $this->entityManager->merge($pessoa);
                $this->defineFlashMessage('success', 'Pessoa alterada com sucesso.');
            } else { //inserir
                $this->entityManager->persist($pessoa);
                $this->defineFlashMessage('success', 'Pessoa cadastrada com sucesso.');
            }
            $this->entityManager->flush();
            $rota = '/pessoas';
            $this->clearFlashData();
        } catch (Exception $e) {
            $this->defineFlashData([
                'id' => $id,
                'nome' => $nome,
                'documento' => $documento,
                'identificacao' => $identificacao
            ]);
            if (!empty($id)) {
                $rota = '/alterar-pessoa?id=' . $id;
            } else {
                $rota = '/nova-pessoa';
            }
            $this->defineFlashMessage('danger', $e->getMessage());
        }
        return new Response(302, ['Location' => $rota], null);
    }
}
