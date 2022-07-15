<?php

use Emprestimo\Chaves\Controller\{
    EmprestimosListar,
    Deslogar,
    LoginFormulario,
    LoginRealizar,
    InstituicaoFormulario,
    InstituicaoSalvar,
    UsuarioListar,
    UsuarioFormulario,
    UsuarioSalvar,
    UsuarioRemover
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
    '/novo-usuario' => UsuarioFormulario::class,
    '/alterar-usuario' => UsuarioFormulario::class,
    '/salvar-usuario' => UsuarioSalvar::class,
    '/remover-usuario' => UsuarioRemover::class,
];

return $rotas;