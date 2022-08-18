<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Instituicao;

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

class ChaveSalvar  implements RequestHandlerInterface
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

    private function verificaDuplicacaoNumero($numero, $idPredio, $idChave = null) {
        $dql = 'SELECT 
            chave 
        FROM '.Chave::class.' chave 
        JOIN chave.predio predio
        WHERE 
            predio.id = '.(int)$idPredio." AND 
            chave.numero = '".trim($numero)."' ";
        if (!empty($idChave)) {
            $dql .= ' AND chave.id != '.(int)$idChave;
        }
        $query = $this->entityManager->createQuery($dql);
        $chaves = $query->getResult();
        if (count($chaves)>0) {
            throw new \Exception('Já existe uma chave cadastrada com o número "'.$chaves[0]->getNumero().'" no prédio "'.$chaves[0]->getPredio()->getNome().'".', 1);
        }
    }    

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $id = $this->requestGETInteger('id', $request);
		$numero = $this->requestPOSTString('numero', $request);
		$idPredio = $this->requestPOSTInteger('predio', $request);
		$descricao = $this->requestPOSTString('descricao', $request);
		$ativo = $this->requestPOSTString('ativo', $request);
        if (empty($ativo)) {
            $ativo = 'S';
        }
		try {
            $this->userVerifyAdmin();	            
		    if (empty($numero)) {
                throw new \Exception("Número não informado.", 1);
            }
			if (empty($idPredio)) {
                throw new \Exception("Prédio não informado.", 1);
            }
            $predio = $this->entityManager->find(Predio::class, $idPredio);
			if (is_null($predio)) {
				throw new \Exception("Não foi possível identificar o prédio.", 1);
			}
			$usuarioAtual = $this->getLoggedUser($this->entityManager);		
			if (is_null($usuarioAtual)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
			if ($usuarioAtual->getInstituicao()->getId() != $predio->getInstituicao()->getId()) {
				throw new \Exception("O prédio selecionado não é da institulção do usuário atual.", 1);
			}
            $chave = new Chave();
            $chave->setPredio($predio);
            $chave->setNumero($numero);
		    $chave->setDescricao($descricao);
		    $chave->setAtivo($ativo == 'S');
            if (!is_null($id) && $id !== false) { //alterar          
                $chave->setId($id);
                $this->entityManager->merge($chave);
                $this->defineFlashMessage('success', 'Chave alterada com sucesso.');               
            } else { //inserir        
                $this->entityManager->persist($chave);
                $this->defineFlashMessage('success', 'chave cadastrada com sucesso.');
            }
            $this->entityManager->flush();
            $rota = '/chaves';
            $this->clearFlashData();
		}
		catch (\Exception $e) {
            $this->defineFlashData([
                'id' => $id,                
                'numero' => $numero,                
                'descricao' => $descricao,                
                'idPredio' => $idPredio,                
                'ativo' => $ativo
            ]);
            if (!empty($id)) {
                $rota = '/alterar-chave?id='.$id;
            }
            else {
                $rota = '/nova-chave';
            }
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => $rota], null);
    }
}