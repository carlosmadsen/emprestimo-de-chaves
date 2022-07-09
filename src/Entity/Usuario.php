<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Predio;

/**
 * @Entity
 * @Table(name="usuarios", schema="chaves", options={"comment":"Usuários da aplicação."})
 */
class Usuario
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id_usuario", options={"comment":"Identificador do usuário."})
     */
    private $id;
    /**
     * @Column(type="string", name="login", unique=true, length=255, nullable=false, options={"comment":"Login do usuário."})
     */
    private $login;
    /** 
     * @Column(type="string", name="senha", length=255, nullable=false, options={"comment":"Senha do usuário."})
     */
    private $senha;
    /** 
     * @Column(type="string", name="nome", unique=true, length=255, nullable=false, options={"comment":"Nome do usuário."})
     */
    private $nome;
    /** 
     * @Column(type="string", name="fl_adm", columnDefinition="CHAR(1) NOT NULL", options={"comment":"FLag que define se o usuário é administrador (valor igual a 'S') ou não (valor igual a 'N'). Se for adm ele pode cadastrar usuários e prédios senão ele só pode emprestar chaves."})
     */
    private $flAdm;
    /** 
     * @Column(type="string", name="observacao", nullable=true, options={"comment":"Observações referentes a este usuário."})
     */
    private $observacao;
    /** 
     * @Column(type="string", name="dt_validade", nullable=true, columnDefinition="DATE", options={"comment":"Data de validade deste usuário."})
     */
    private $validade;
    /** 
     * @Column(type="string", name="fl_ativo", columnDefinition="CHAR(1) NOT NULL", options={"comment":"FLag que define se o usuário ainda está ativo."})
     */
    private $flAtivo;
    /**
     * @ManyToMany(targetEntity="Predio", inversedBy="usuarios", cascade={"persist"})
     * @JoinTable(name="usuarios_predios", schema="chaves")
     */
	private $predios;

    public function __construct() {
        $this->predios = new ArrayCollection();
    }

    public function setLogin(string $v): void
    {
        $this->login = $v;
    }

    public function getLogin(): string
    {
        return $this->senha;
    }

    public function setSenha(string $v): void
    {
        $this->senha = password_hash($v, PASSWORD_DEFAULT);
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function senhaEstaCorreta(string $senhaAberta): bool
    {
        return password_verify($senhaAberta, $this->senha);
    }

    public function setNome(string $v): void
    {
        $this->nome = $v;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setAdm(bool $fl): void
    {
        $this->flAdm = ($fl ? 'S' : 'N');
    }

    public function getAdm(): bool
    {
       return $this->ehAdm();
    }

    public function ehAdm(): bool
    {
        return $this->flAdm == 'S';
    }

    public function setObservacao(string $v): void
    {
        $this->observacao = $v;
    }

    public function getObservacao(): string
    {
        return $this->observacao;
    }

    public function setValidade(string $v): void
    {
        $this->validade = $v;
    }

    public function getValidade(): string
    {
        return $this->validade;
    }

    public function setAtivo(bool $fl): void
    {
        $this->flAtivo = ($fl ? 'S' : 'N');
    }

    public function getAtivo(): bool
    {
       return $this->estaAtivo();
    }

    public function estaAtivo(): bool
    {
        return $this->flAtivo == 'S';
    }

    public function addPredio(Predio $predio) {
		if (!$this->predios->contains($predio)) {
            $this->predios->add($predio);
		    $predio->addUsuario($this);
        }
   }

	public function getPredios() {
    	return $this->predios;
	}
}
