<?php

namespace Emprestimo\Chaves\Controller;

use Emprestimo\Chaves\Entity\Emprestimo;
use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Instituicao;

use Emprestimo\Chaves\Infra\EntityManagerCreator;

use Emprestimo\Chaves\Helper\RenderizadorDeHtmlTrait;
use Emprestimo\Chaves\Helper\FlashMessageTrait;
use Emprestimo\Chaves\Helper\FlashDataTrait;
use Emprestimo\Chaves\Helper\SessionUserTrait;
use Emprestimo\Chaves\Helper\RequestTrait;
use Emprestimo\Chaves\Helper\SessionFilterTrait;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class EmprestimoFormulario implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait;
    use FlashMessageTrait;
    use FlashDataTrait;
    use SessionUserTrait;
    use RequestTrait;
	use SessionFilterTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {       
        $dados = $this->getFlashData();       
        $this->clearFlashData();
		$this->defineSessionFilterKey('emprestimos');
        try {
            $usuarioAtual = $this->getLoggedUser($this->entityManager);    
            $predios = $usuarioAtual->getPrediosAtivos();
			$idPredio = $this->getFilterSession('predio');
			if (empty($idPredio) and (count($predios) == 1)) {
                $idPredio = $predios[0]->getId();
            }           
            $html = $this->renderizaHtml('emprestimo/formulario.php', array_merge([
                'titulo' => 'Novo empréstimo',
				'predios' => $predios,
				'idPredio' => $idPredio,
                'labelIdentificacao' => $this->getSessionUserLabelIdentificacaoPessoa(),
                'labelDocumento' => $this->getSessionUserLabelDocumentoPessoa()
            ], $dados));
            return new Response(200, [], $html);
        } catch (\Exception $e) {
            $this->defineFlashMessage('danger', $e->getMessage());
            return new Response(302, ['Location' => '/emprestimos'], null);
        }
    }    
}
