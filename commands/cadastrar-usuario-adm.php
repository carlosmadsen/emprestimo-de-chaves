<?php

require __DIR__ . '/../vendor/autoload.php';

use Emprestimo\Chaves\Entity\Instituicao;
use Emprestimo\Chaves\Entity\Usuario;
use Emprestimo\Chaves\Infra\EntityManagerCreator;

try {
	$login = $argv[1];
	if (empty($login)) {
		throw new Exception("No primeiro parâmetro deve ser informado o login do usuário.", 1);
	}
	$senha = $argv[2];
	if (empty($senha)) {
		throw new Exception("No segundo parâmetro deve ser informado a senha do usuário.", 1);
	}
	$email = $argv[3];
	if (empty($email)) {
		throw new Exception("No terceiro parâmetro deve ser informado o e-mail do usuário.", 1);
	}
	$nome = $argv[4];
	if (empty($nome)) {
		throw new Exception("No quarto parâmetro deve ser informado o nome do usuário.", 1);
	}
	$sigla = $argv[5];
	if (empty($sigla)) {
		throw new Exception("No quinto parâmetro deve ser informado a sigla da instituição.", 1);
	}

	$entityManagerCreator  = new EntityManagerCreator();
	$entityManager = $entityManagerCreator->getEntityManager();
	
	$repositorioDeInstituicoes = $entityManager->getRepository(Instituicao::class);
	$instituicao = $repositorioDeInstituicoes->findOneBy([
		'sigla' => $sigla
	]);
	if (is_null($instituicao)) {
		throw new Exception('Não foi possível localizar a instituição pela sigla "'.$sigla.'".', 1);
	}

	$usuario = new Usuario();
	$usuario->setEmail($email);
	$usuario->setLogin($login);
	$usuario->setSenha($senha);
	$usuario->setNome($nome);
	$usuario->setAdm(true);
	$usuario->setAtivo(true);
	$usuario->setInstituicao($instituicao);
	
	$entityManager->persist($usuario);
	$entityManager->flush();

	echo "Usuário administrador cadastrado com sucesso.\n\n";
}
catch (\Exception $e) {
	echo $e->getMessage()."\n\n";
}
