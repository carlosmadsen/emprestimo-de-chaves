<?php

require __DIR__ . '/../vendor/autoload.php';

use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Infra\EntityManagerCreator;

try {
	$sigla = $argv[1];
	if (empty($sigla)) {
		throw new Exception("No primeiro parâmetro deve ser informado a sigla da instituição.", 1);
	}
	$nome = $argv[2];
	if (empty($nome)) {
		throw new Exception("No segundo parâmetro deve ser informado o nome da instituição.", 1);
	}

	$entityManagerCreator  = new EntityManagerCreator();
	$entityManager = $entityManagerCreator->getEntityManager();
		
	$instituicao = new Instituicao();
	$instituicao->setSigla($sigla);
	$instituicao->setNome($nome);
	$entityManager->persist($instituicao);
	$entityManager->flush();

	echo "Instituição cadastrada com sucesso.\n";
	echo "Sigla: {$instituicao->getSigla()}\n";
	echo "Nome: {$instituicao->getNome()}\n\n";
	
}
catch (\Exception $e) {
	echo $e->getMessage()."\n\n";
}
