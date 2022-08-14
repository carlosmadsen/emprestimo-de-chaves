<?php

require __DIR__ . '/../vendor/autoload.php';

use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;

try {
	$sigla = array_key_exists(1, $argv) ? $argv[1] : null;
	if (empty($sigla)) {
		throw new Exception("No primeiro parâmetro deve ser informado a sigla da instituição.", 1);
	}
	$nome = array_key_exists(2, $argv) ? $argv[2] : null;
	if (empty($nome)) {
		throw new Exception("No segundo parâmetro deve ser informado o nome da instituição.", 1);
	}

	$entityManagerCreator  = new EntityManagerCreator();
	$entityManager = $entityManagerCreator->getEntityManager();
		
	$repositorioInstituicoes = $entityManager->getRepository(Instituicao::class);
	$instituicaoSigla = $repositorioInstituicoes->findOneBy(['sigla' => $sigla]);
	if (!is_null($instituicaoSigla)) {
		throw new \Exception("Já existe uma instituição com a sigla ".$sigla.".", 1);
	}

	$instituicaoNome = $repositorioInstituicoes->findOneBy(['nome' => $nome]);
	if (!is_null($instituicaoNome)) {
		throw new \Exception("Já existe uma instituição com o nome ".$nome.".", 1);
	}

	$instituicao = new Instituicao();
	$instituicao->setSigla($sigla);
	$instituicao->setNome($nome);
	$instituicao->setLabelDocumentoPessoa('Nº documento');
	$instituicao->setLabelIdentificacaoPessoa('Nº identificação');
	$entityManager->persist($instituicao);
	$entityManager->flush();

	echo "Instituição cadastrada com sucesso.\n";
	echo "Sigla: {$instituicao->getSigla()}\n";
	echo "Nome: {$instituicao->getNome()}\n\n";
	
}
catch (\Exception $e) {
	echo $e->getMessage()."\n\n";
}
