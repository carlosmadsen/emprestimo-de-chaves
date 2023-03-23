<?php

namespace Emprestimo\Chaves\Controller;

use Exception;
use DateTime;

use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Emprestimo;
use Emprestimo\Chaves\Entity\Chave;

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

class EmprestimosListar  implements RequestHandlerInterface
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
        try {
            $this->clearFlashData();
            $this->defineSessionFilterKey('emprestimos');
            $newFilter = $this->requestGETInteger('filtrar', $request) == 1;
            $cleanFilterSession = $this->requestGETInteger('limparFiltro', $request) == 1;
            if ($cleanFilterSession) {
                $this->clearFilterSession();
            }
            $idInstituicao = $this->getSessionUserIdInstituicao();
            if (empty($idInstituicao)) {
				throw new Exception('Não foi possível identificar a instituição do usuário atual.', 1);
			}
            $usuario = $this->getLoggedUser($this->entityManager);
            $predios = $usuario->getPrediosAtivos();
            $nrPredios = count($predios);            
            $idPredio = $this->requestPOSTInteger('predio', $request) ? : (!$newFilter ? $this->getFilterSession('predio') : null);
            if (empty($idPredio) and ($nrPredios == 1)) {
                $idPredio = $predios[0]->getId();
            }         
            $temPesquisa = (!empty($idPredio));
            if ($temPesquisa) {
                $this->defineFilterSesssion([				
                    'predio' => $idPredio
                ]);
            }
            else {
                $this->clearFilterSession();
            }
            $emprestimos = $this->getEmprestimos($idInstituicao, $idPredio);
            $html = $this->renderizaHtml('emprestimo/listar.php', [
                'titulo' => 'Empréstimos: ',
                'predios' => $predios,
				'idPredio' => $idPredio,
                'emprestimos' => $emprestimos 
            ]); 
            return new Response(200, [], $html);
        } catch (Exception $e) {
			$this->defineFlashMessage('danger', $e->getMessage());
			return new Response(302, ['Location' => '/login'], null);
		}    
    }

    private function getEmprestimos($idInstituicao, $idPredio) {
		if (empty($idPredio)) {
			return [];
		}
		$dql = 'SELECT 
            emprestimo 
        FROM ' . Emprestimo::class . ' emprestimo 
        JOIN emprestimo.chave chave 		
        JOIN chave.predio predio
        JOIN predio.instituicao instituicao		 		
        WHERE 
            predio.id = '.$idPredio.' AND 
            instituicao.id = ' . $idInstituicao . ' 		
        ORDER BY 
            chave.numero ';
		$query = $this->entityManager->createQuery($dql);
		return $query->getResult();
	}

}