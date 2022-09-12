<?php

namespace Emprestimo\Chaves\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="usuarios", schema="chaves", options={"comment":"Usuários da aplicação."})
 */
class Usuario
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
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
     * @Column(type="string", name="nome", unique=false, length=255, nullable=false, options={"comment":"Nome do usuário."})
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
     * @Column(type="string", name="fl_ativo", columnDefinition="CHAR(1) NOT NULL", options={"comment":"FLag que define se o usuário ainda está ativo."})
     */
    private $flAtivo;
    /**
     * @Column(type="string", name="email", nullable=false, options={"comment":"E-mail do usuário."})
     */
    private $email;
    /**
     * @ManyToOne(targetEntity="Instituicao", inversedBy="usuarios")
     */
    private $instituicao;
    /**
     * @ManyToMany(targetEntity="Predio", mappedBy="usuarios", cascade={"persist"})
     */
    private $predios;
    /**
     * @OneToMany(targetEntity="Emprestimo", mappedBy="usuario")
     */
    private $emprestimos;


    public function __construct()
    {
        $this->predios = new ArrayCollection();
        $this->emprestimos = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setLogin(string $v): void
    {
        $this->login = $v;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setSenha(string $v, $flCriptografar = true): void
    {
        $this->senha = ($flCriptografar ? password_hash($v, PASSWORD_DEFAULT) : $v);
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
        return !is_null($this->observacao) ? $this->observacao : '';
    }

    public function setEmail(string $v): void
    {
        $this->email = $v;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function addPredio(Predio $predio)
    {
        if (!$this->predios->contains($predio)) {
            $this->predios->add($predio);
            $predio->addUsuario($this);
        }
    }

    public function getPredios()
    {
        return $this->predios;
    }

    public function getPrediosAtivos()
    {
        $predios = new ArrayCollection();
        foreach ($this->predios as $p) {
            if ($p->estaAtivo()) {
                $predios->add($p);
            }
        }
        return $predios;
    }

    public function removePredio(Predio $predio)
    {
        if ($this->predios->contains($predio)) {
            $this->predios->removeElement($predio);
            $predio->removeUsuario($this);
        }
    }

    public function addEmprestimo(Emprestimo $emprestimo)
    {        
        $this->emprestimos->add($emprestimo);
        $emprestimo->setUsuario($this);       
    }

    public function getEmprestimos()
    {
        return $this->emprestimos;
    }

    public function getInstituicao(): Instituicao
    {
        return $this->instituicao;
    }

    public function setInstituicao(Instituicao $instituicao): void
    {
        $this->instituicao = $instituicao;
    }
}
