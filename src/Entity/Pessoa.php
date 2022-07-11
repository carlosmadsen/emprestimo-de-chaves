<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Emprestimo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="pessoas", schema="chaves", options={"comment":"Pessoas que pegam chaves emprestadas."})
 */
class Pessoa
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
	/**
     * @Column(type="string", name="nr_documento", unique=true, length=30, nullable=false, options={"comment":"Número de documento."})
     */
    private $documento;
    /** 
     * @Column(type="string", name="nome", unique=true, length=255, nullable=false, options={"comment":"Nome da pessoa."})
     */
    private $nome;
    /** 
     * @Column(type="string", name="observacao", length=255, nullable=true, options={"comment":"Observações acerca desta pessoa."})
     */
    private $observacao;
    /**
 	 * @OneToMany(targetEntity="Emprestimo", mappedBy="emprestimos")
 	 */
	private $emprestimos;

    public function __construct() {
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
    
    public function setNome(string $v): void
    {
        $this->nome = $v;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setDocumento(string $v): void
    {
        $this->documento = $v;
    }

    public function getDocumento(): string
    {
        return $this->documento;
    }

    public function setObservacao(string $v): void
    {
        $this->observacao = $v;
    }

    public function getObservacao(): string
    {
        return $this->observacao;
    }

    public function addEmprestimo(Emprestimo $emprestimo):  void {
	    if (!$this->emprestimos->contains($emprestimo)) {
    		$this->emprestimos->add($emprestimo);
    		$emprestimo->setPessoa($this);
		}
	}

	public function getEmprestimos(): Collection {
    	return $this->emprestimos;
	}
}