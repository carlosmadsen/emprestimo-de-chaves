<?php

namespace Emprestimo\Chaves\Entity;

use DateTime;
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
    private int $id;
    /**
     * @OneToOne(targetEntity="Pessoa", inversedBy="emprestimo")
     */
    private Pessoa $pessoa;
    /**
     * @OneToOne(targetEntity="Chave", inversedBy="emprestimo")
     */
    private Chave $chave;
    /**
     * @ManyToOne(targetEntity="Usuario", inversedBy="emprestimos")
     */
    private Usuario $usuario;
    /**
     * @Column(type="datetime", 
     * name="dt_emprestimo", 
     * unique=false, 
     * length=20, 
     * nullable=false, 
     * columnDefinition="TIMESTAMP DEFAULT CURRENT_TIMESTAMP"), 
     * options={"comment":"Data e hora em que esse empréstimo foi lançado."})
    */
    private DateTime $dtEmprestimo;       

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

    public function setDtEmprestimo(DateTime $v): void
    {
        $this->dtEmprestimo = $v;
    }

    public function getDtEmprestimo(): DateTime
    {
        return $this->dtEmprestimo;
    }   
}
