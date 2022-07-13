<?php

use Emprestimo\Chaves\Controller\{
    EmprestimosListar,
    LoginFormulario,
    LoginRealizar
};

$rotas = [
    '/' => EmprestimosListar::class,
    '/emprestimos' => EmprestimosListar::class,
    '/login' => LoginFormulario::class,
    '/realiza-login' => LoginRealizar::class,
];

return $rotas;