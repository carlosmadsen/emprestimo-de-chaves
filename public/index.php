<?php

require __DIR__ . '/../vendor/autoload.php';

use Nyholm\Psr7Server\ServerRequestCreator;
use Nyholm\Psr7\Factory\Psr17Factory;

$caminho = $_SERVER['PATH_INFO'];
$rotas = require __DIR__ . '/../config/routes.php';

if (!array_key_exists($caminho, $rotas)) {
    http_response_code(404);
    exit();
}

session_start();

$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UrlFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory // StreamFactory
);
$request = $creator->fromGlobals();

if ($caminho === '/login') {
    session_destroy();
}
if ((strpos($caminho, 'login') === false) and is_null($_SESSION['usuario'])) {
    $caminho = '/login';
}

$_SESSION['js'] = null;
if (!empty($caminho)) {
    $arqJs = '/js'.$caminho.'.js';
    if (file_exists(__DIR__ .$arqJs)) {
        $_SESSION['js'] = $arqJs;
    }
}

$classeControladora = $rotas[$caminho];
$container = require __DIR__ . '/../config/dependencies.php';
$controlador = $container->get($classeControladora);
$resposta = $controlador->handle($request);

foreach ($resposta->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $resposta->getBody();
