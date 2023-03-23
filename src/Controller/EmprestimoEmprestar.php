<?php

namespace Emprestimo\Chaves\Controller;

use DateTime;
use Exception;

use Emprestimo\Chaves\Entity\Historico;
use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Pessoa;
use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Emprestimo;
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

class EmprestimoEmprestar  implements RequestHandlerInterface
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
   
    private function verificaPermissaoPredio(Usuario $usuarioAtual, $idPredio): void 
    {
        $predios = $usuarioAtual->getPrediosAtivos();
        $predioPermitido = false;
        foreach ($predios as $predio) {
            if ($predio->getId() == $idPredio) {
                $predioPermitido = true;
                break;
            }
        }
        if (!$predioPermitido) {
            throw new Exception("O seu usuário não tem permissão para emprestar chaves para este prédio.", 1);
        }
    }

    private function verificaUsarioPredioInstituicao(Usuario $usuarioAtual, Predio $predio): void
    {
        if ($usuarioAtual->getInstituicao()->getId() != $predio->getInstituicao()->getId()) {
			throw new Exception("O prédio selecionado não é da instituição do usuário atual.", 1);
		}
    }

    private function getChave($numeroChave, Predio $predio): Chave
    {
        $dql = 'SELECT 
            chave 
        FROM '.Chave::class." chave       
		INNER JOIN chave.predio as predio  
        WHERE 
            chave.numero = '".trim($numeroChave)."' AND 
			predio.id = ".(int)$predio->getId();
        $query = $this->entityManager->createQuery($dql);
        $chaves = $query->getResult();
        if (count($chaves)<1) {
            throw new Exception('Não foi possível localizar a chave de número "'.$numeroChave.'" no prédio "'.$predio->getNome().'".', 1);
        }
        return $chaves[0];
    }    

    private function getPessoa($identificacao, $documento, $idInstituicao): Pessoa
    {
        $dql = 'SELECT 
            pessoa 
        FROM '.Pessoa::class." pessoa 
        JOIN pessoa.instituicao instituicao
        WHERE 
            instituicao.id = ".$idInstituicao.' ';      
        if (!empty($identificacao)) {
            $dql .= " AND pessoa.nrIdentificacao = '".trim($identificacao)."' ";
        }
        if (!empty($documento)) {
            $dql .= " AND pessoa.nrDocumento = '".trim($documento)."' ";
        }
        $dql .= '	
        ORDER BY 
            pessoa.nome ';
        $query = $this->entityManager->createQuery($dql);
		$pessoas = $query->getResult();
        $nrPessoaResultado = count($pessoas);
        if ($nrPessoaResultado < 1) {
            throw new Exception('Não foi possível localizar a pessoa.', 1);
        } elseif ($nrPessoaResultado > 1) {
            throw new Exception('Ocorreu um erro inesperado com essas informações foram localizadas '.$nrPessoaResultado.' pessoas.', 1); 
        }
        return $pessoas[0];
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {        
        $idInstituicao = $this->getSessionUserIdInstituicao();
		$usuarioAtual = $this->getLoggedUser($this->entityManager);  
        $labelIdentificacaoPessoa = $this->getSessionUserLabelIdentificacaoPessoa();
        $labelDocumentoPessoa = $this->getSessionUserLabelDocumentoPessoa();
		$idPredio = $this->requestPOSTInteger('predio', $request);
		$numeroChave = $this->requestPOSTString('numero_chave', $request);
		$identificacaoPessoa = $this->requestPOSTString('identificacao', $request);
		$documentoPessoa = $this->requestPOSTString('documento', $request);		
		try {
        	if (empty($idInstituicao)) {
				throw new Exception("Não foi possível identificar a instituição do usuário atual.", 1);
			}
            if (is_null($usuarioAtual)) {
				throw new Exception("Não foi possível identificar o usuário atual.", 1);
			}
			if (empty($idPredio)) {
                throw new Exception("Prédio não informado.", 1);
            }
            if (empty($numeroChave)) {
                throw new Exception("Número da chave não informado.", 1);
            }
            if (!empty($labelIdentificacaoPessoa) && empty($identificacaoPessoa)) {
                throw new Exception( $labelIdentificacaoPessoa. " não informado.", 1);
            }
            if (empty($documentoPessoa)) {
                throw new Exception( $labelDocumentoPessoa. " não informado.", 1);
            }
            $this->verificaPermissaoPredio($usuarioAtual, $idPredio);
            $predio = $this->entityManager->find(Predio::class, $idPredio);
            if (!$predio->estaAtivo()) {
                throw new Exception('Prédio "'.$predio->getNome().'" não está mais ativo.', 1);
            }
            $this->verificaUsarioPredioInstituicao($usuarioAtual, $predio);
            $chave = $this->getChave($numeroChave,  $predio);
            if (!$chave->estaAtivo()) {
                 throw new Exception('A chave de número "'.$chave->getNumero().'" não está mais ativa.', 1);
            }
            $emprestimoAtualDaChave = $chave->getEmprestimo();
            if (!is_null($emprestimoAtualDaChave)) {
                throw new Exception("A chave de número ".$chave->getNumero()." do prédio ".$predio->getNome()." já está emprestada para ".$emprestimoAtualDaChave->getPessoa()->getNome().".", 1);
            }
            $pessoa = $this->getPessoa($identificacaoPessoa, $documentoPessoa, $idInstituicao);
            $emprestimoAtualDaPessoa = $pessoa->getEmprestimo();
            if (!is_null($emprestimoAtualDaPessoa)) {
                $chaveEmprestada = $emprestimoAtualDaPessoa->getChave();
                throw new Exception("A pessoa ".$pessoa->getNome()." já está com a chave de número ".$chaveEmprestada->getNumero()." do prédio ".$chaveEmprestada->getPredio()->getNome()." emprestada.", 1);
            }

			$emprestimo = new Emprestimo();
            $emprestimo->setPessoa($pessoa);
            $emprestimo->setChave($chave);
            $emprestimo->setUsuario($usuarioAtual);
            $emprestimo->setDtEmprestimo(new DateTime("now"));
            $this->entityManager->persist($emprestimo);

            $historico = new Historico();
            $historico->setInstituicao($usuarioAtual->getInstituicao());
            $historico->setLoginUsuarioEmprestimo($usuarioAtual->getLogin());
            $historico->setNomeUsuarioEmprestimo($usuarioAtual->getNome());
            $historico->setDtEmprestimo($emprestimo->getDtEmprestimo());
            $historico->setNomePessoa($pessoa->getNome());
            $historico->setNumeroChave($chave->getNumero());
            $historico->setNomePredio($predio->getNome());
            $this->entityManager->persist($historico);

            $this->entityManager->flush();

            $this->defineFlashMessage('success', 'A chave número '.$chave->getNumero().' do prédio '.$predio->getNome().' foi emprestada com sucesso para '.$pessoa->getNome().'.');            
            $rota = '/emprestimos';
            $this->clearFlashData();           
		}
		catch (Exception $e) {
            $this->defineFlashData([
                'idPredio' => $idPredio,                
				'numeroChave' => $numeroChave,  
                'identificacao' => $identificacaoPessoa,
                'documento' => $documentoPessoa              
            ]);          
            $rota = '/novo-emprestimo';            
			$this->defineFlashMessage('danger', $e->getMessage());
		}
		return new Response(302, ['Location' => $rota], null);
    }
}