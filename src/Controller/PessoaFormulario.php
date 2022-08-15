<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Pessoa;
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

class PessoaFormulario implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
	use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
    use RequestTrait;

    private $repositorioDePessoas;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repositorioDePessoas = $this->entityManager->getRepository(Pessoa::class);
    }

    private function getPrediosInstituicao()
    {

    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {        
        $id = $this->requestGETInteger('id', $request);
        $titulo = ( empty($id) ? 'Nova pessoa' : 'Alterar pessoa');
        $dados = $this->getFlashData();
        $this->clearFlashData();
        try {
            $usuarioAtual = $this->getLoggedUser($this->entityManager);	
			if (is_null($usuarioAtual)) {
				throw new \Exception("Não foi possível identificar o usuário.", 1);
			}
            $this->userVerifyAdmin();
          	$instituicao = $usuarioAtual->getInstituicao();			
           	if (empty($dados) and !empty($id)) {   
              
                $pessoa = $this->repositorioDePessoas->findOneBy(['id' => $id]);
                if (is_null($pessoa)) {
                    throw new \Exception("Não foi possível identificar a pessoa.", 1);
                }               
                if ($pessoa->getInstituicao()->getId() != $instituicao->getId()) {
                    throw new \Exception("A pessoa selecionada não é da mesma instituição do usuário atual.", 1);
                }   
                $dados = [
                    'id' => $id,
                    'nome' => $pessoa->getNome(),
                    'identificacao' => $pessoa->getNrIdentificacao(),
                    'documento' => $pessoa->getNrDocumento()
		        ];                                    
            }
            $dados = array_merge($dados, [
				'titulo' => $titulo,				
				'labelIdentificacao' => $this->getSessionUserLabelIdentificacaoPessoa(),
				'labelDocumento' => $this->getSessionUserLabelDocumentoPessoa()
			]);			
			$html = $this->renderizaHtml('pessoa/formulario.php',  $dados);
            return new Response(200, [], $html);
		}
		catch (\Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/pessoas'], null);
		}
    }
}