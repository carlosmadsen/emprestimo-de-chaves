<?php

use Emprestimo\Chaves\Controller\{
    EmprestimosListar,
    Deslogar,
    LoginFormulario,
    LoginRealizar,
    InstituicaoFormulario,
    InstituicaoSalvar,
    UsuarioListar,
    UsuarioFormularioNovo,
    UsuarioSalvar,
};

$rotas = [
    '/' => EmprestimosListar::class,
    '/emprestimos' => EmprestimosListar::class,
    '/logout' => Deslogar::class,
    '/login' => LoginFormulario::class,
    '/realiza-login' => LoginRealizar::class,
    '/instituicao' => InstituicaoFormulario::class,
    '/salvar-instituicao' => InstituicaoSalvar::class,
    '/usuarios' => UsuarioListar::class,
    '/novo-usuario' => UsuarioFormularioNovo::class,
    '/salvar-usuario' => UsuarioSalvar::class,
];

return $rotas;