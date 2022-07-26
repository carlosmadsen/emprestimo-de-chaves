<?php

namespace Emprestimo\Chaves\Helper;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait RequestTrait
{
	public function requestPOSTString(string $chave, ServerRequestInterface $request): string
	{
		$dados = (array)$request->getParsedBody();
		return array_key_exists($chave, $dados) ? filter_var($dados[$chave], FILTER_SANITIZE_STRING) : '';
	}

	public function requestGETInteger(string $chave, ServerRequestInterface $request)
	{
		$dados = (array)$request->getQueryParams();
		return array_key_exists($chave, $dados) ? filter_var($dados[$chave], FILTER_VALIDATE_INT) : null;
	}
}
