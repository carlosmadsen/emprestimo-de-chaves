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
    UsuarioRemover,
    MinhaContaFormulario,
    MinhaContaSalvar,
    PredioListar,
    PredioFormulario,
    PredioSalvar,
    PredioRemover,
    ChaveListar,
    ChaveFormulario,
    ChaveSalvar,
    ChaveRemover
};

$rotas = [
    '' => EmprestimosListar::class,
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
    '/minha-conta' => MinhaContaFormulario::class,
    '/salvar-minha-conta' => MinhaContaSalvar::class,
    '/predios' => PredioListar::class,
    '/novo-predio' => PredioFormulario::class,
    '/alterar-predio' => PredioFormulario::class,
    '/salvar-predio' => PredioSalvar::class,
    '/remover-predio' => PredioRemover::class,
    '/chaves' => ChaveListar::class,
    '/nova-chave' => ChaveFormulario::class,
    '/alterar-chave' => ChaveFormulario::class,
    '/salvar-chave' => ChaveSalvar::class,
    '/remover-chave' => ChaveRemover::class,
];

return $rotas;