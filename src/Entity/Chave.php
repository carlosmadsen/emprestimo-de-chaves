<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Emprestimo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="chaves", schema="chaves", options={"comment":"Chaves das salas dos prédios."})
 */
class Chave
 {
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
 	/** 
     * @Column(type="string", name="numero", unique=true, length=6, nullable=false, options={"comment":"Número da chave."})
     */
    private $numero;
	/** 
     * @Column(type="string", name="descricao", unique=true, length=255, nullable=true, options={"comment":"Descrição da salda desta chave."})
     */
    private $descricao;
	/** 
     * @Column(type="string", name="fl_ativo", columnDefinition="CHAR(1) NOT NULL", options={"comment":"FLag que define se a chave ainda é usada."})
     */
    private $flAtivo;
    /**
     * @ManyToOne(targetEntity="Predio", inversedBy="chaves")
     */
	private $predio;
 	

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
	public function setNumero(string $v): void
    {
        $this->numero = $v;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

	public function setDescricao(string $v): void
    {
        $this->descricao = $v;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

	public function getPredio(): Predio {
		return $this->predio;
	}

	public function setPredio(Predio $predio): void {
		$this->predio = $predio;
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
}
