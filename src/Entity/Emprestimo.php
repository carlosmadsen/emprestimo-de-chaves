<?php

namespace Emprestimo\Chaves\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="emprestimos", schema="chaves", options={"comment":"Registro de chaves emprestadas."})
 */
class Emprestimo
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
    /**
     * @OneToOne(targetEntity="Pessoa", inversedBy="emprestimo")
     */
    private $pessoa;
    /**
     * @OneToOne(targetEntity="Chave", inversedBy="emprestimo")
     */
    private $chave;
    /**
     * @ManyToOne(targetEntity="Usuario", inversedBy="emprestimos")
     */
    private $usuario;
    /**
     * @Column(type="datetime", 
     * name="dt_emprestimo", 
     * unique=false, 
     * length=20, 
     * nullable=false, 
     * columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP"), 
     * options={"comment":"Data e hora em que esse empréstimo foi lançado."})
    */
    private $dtEmprestimo;       

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPessoa(): Pessoa
    {
        return $this->pessoa;
    }

    public function setPessoa(Pessoa $pessoa): void
    {
        $this->pessoa = $pessoa;
    }

    public function getChave(): Chave
    {
        return $this->chave;
    }

    public function setChave(Chave $chave): void
    {
        $this->chave = $chave;
    }

    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function setDtEmprestimo(string $v): void
    {
        $this->dtEmprestimo = $v;
    }

    public function getDtEmprestimo(): string
    {
        return $this->dtEmprestimo;
    }   
}
