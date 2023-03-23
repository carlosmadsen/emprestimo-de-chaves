<?php

namespace Emprestimo\Chaves\Controller;

use Exception;

use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Entity\Emprestimo;
use Emprestimo\Chaves\Entity\Chave;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class PredioSalvar implements RequestHandlerInterface
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

    private function verificaDuplicacaoNome($nome, $idInstituicao, $idPredio = null)
    {
        $dql = 'SELECT 
            predio 
        FROM '.Predio::class.' predio 
        JOIN predio.instituicao instituicao
        WHERE 
            instituicao.id = '.(int)$idInstituicao." AND 
            predio.nome = '".trim($nome)."' ";
        if (!empty($idPredio)) {
            $dql .= ' AND predio.id != '.(int)$idPredio;
        }
        $query = $this->entityManager->createQuery($dql);
        $predios = $query->getResult();
        if (count($predios)>0) {
            throw new Exception('Já existe um prédio cadastrado com o nome "'.$predios[0]->getNome().'".', 1);
        }
    }

    private function verificaEmprestimosEmAberto(Predio $predio): void
    {       
        $dql = 'SELECT 
            COUNT(emprestimo.id) 
        FROM  '. Emprestimo::class . ' emprestimo
        JOIN emprestimo.chave chave 
        JOIN chave.predio predio
        WHERE
            predio.id =  '.$predio->getId().'
        ';
        $query = $this->entityManager->createQuery($dql);
        $nrEmprestimos = $query->getSingleScalarResult();
        if ($nrEmprestimos > 0) {
            throw new Exception('Não é permitido modificar um prédio que tem empréstimos de chave em aberto.');
        }
    } 

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $this->requestGETInteger('id', $request);
        $nome = $this->requestPOSTString('nome', $request);
        $ativo = $this->requestPOSTString('ativo', $request);
        if (empty($ativo)) {
            $ativo = 'S';
        }
        try {
            $this->userVerifyAdmin();
            if (empty($nome)) {
                throw new Exception("Nome não informado.", 1);
            }
            $usuarioAtual = $this->getLoggedUser($this->entityManager);
            if (is_null($usuarioAtual)) {
                throw new Exception("Não foi possível identificar o usuário.", 1);
            }
            $instituicao = $usuarioAtual->getInstituicao();
            $this->verificaDuplicacaoNome($nome, $instituicao->getId(), $id);
            $predio = new Predio();
            $predio->setInstituicao($instituicao);
            $predio->setNome($nome);
            $predio->setAtivo($ativo == 'S');
            if (!is_null($id) && $id !== false) { //atualizar
                $predio->setId($id);
                $this->verificaEmprestimosEmAberto($predio);
                 $this->entityManager->merge($predio);
                $this->defineFlashMessage('success', 'Prédio alterado com sucesso.');
            } else { //inserir
                $this->entityManager->persist($predio);
                $this->defineFlashMessage('success', 'Prédio cadastrado com sucesso.');
            }
            $this->entityManager->flush();
            $rota = '/predios';
            $this->clearFlashData();
        } catch (Exception $e) {
            $this->defineFlashData([
                'id' => $id,
                'nome' => $nome,
                'ativo' => $ativo
            ]);
            if (!empty($id)) {
                $rota = '/alterar-predio?id='.$id;
            } else {
                $rota = '/novo-predio';
            }
            $this->defineFlashMessage('danger', $e->getMessage());
        }
        return new Response(302, ['Location' => $rota], null);
    }
}
