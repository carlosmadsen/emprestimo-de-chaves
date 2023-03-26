<?php

namespace Emprestimo\Chaves\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="historicos",
 * schema="chaves",
 * options={"comment":"Histórico de empréstimso realizados."}
 * )
 */
class Historico
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;    
    /**
     * @ManyToOne(targetEntity="Instituicao", inversedBy="historicos")
     */
    private $instituicao;    
	/**
     * @Column(type="string", name="login_usuario_emprestimo", unique=false, length=255, nullable=false, options={"comment":"Login do usuário que lançou o empréstimo."})
     */
    private $loginUsuarioEmprestimo;
	/**
     * @Column(type="string", name="nome_usuario_emprestimo", unique=false, length=255, nullable=false, options={"comment":"Nome do usuário que lançou o empréstimo."})
     */
    private $nomeUsuarioEmprestimo;
	/**
     * @Column(type="string", name="login_usuario_devolucao", unique=false, length=255, nullable=true, options={"comment":"Login do usuário que lançou a devolução."})
    */    
	private $loginUsuarioDevolucao;
	/**
     * @Column(type="string", name="nome_usuario_devolucao", unique=false, length=255, nullable=true, options={"comment":"Nome do usuário que lançou a devolução."})
    */    
	private $nomeUsuarioDevolucao;
	/**
     * @Column(type="datetime", name="dt_emprestimo", unique=false, length=20, nullable=false, columnDefinition="TIMESTAMP"), options={"comment":"Data e hora em o empréstimo foi lançado."})
    */
    private DateTime $dtEmprestimo; 
	/**
     * @Column(type="datetime", name="dt_devolucao", unique=false, length=20, nullable=true, columnDefinition="TIMESTAMP"), options={"comment":"Data e hora em a devolução foi lançada."})
    */
    private DateTime $dtDevolucao; 
	/**
     * @Column(type="string", name="nome_pessoa", unique=false, length=255, nullable=false, options={"comment":"Nome da pessoa que pegou a chave emprestada."})
    */ 
	private $nomePessoa;
	/**
     * @Column(type="string", name="numero_chave", unique=false, length=6, nullable=false, options={"comment":"Número da chave que foi emprestada."})
     */
    private $numeroChave;
	/**
     * @Column(type="string", name="nome_predio", length=255, nullable=false, options={"comment":"Nome do prédio."})
    */
    private $nomePredio;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }    

    public function getInstituicao(): Instituicao
    {
        return $this->instituicao;
    }

    public function setInstituicao(Instituicao $instituicao): void
    {
        $this->instituicao = $instituicao;
    }    

	public function setLoginUsuarioEmprestimo(string $v): void
    {
        $this->loginUsuarioEmprestimo = $v;
    }

    public function getLoginUsuarioEmprestimo(): string
    {
        return $this->loginUsuarioEmprestimo;
    }

	public function setNomeUsuarioEmprestimo(string $v): void
    {
        $this->nomeUsuarioEmprestimo = $v;
    }

    public function getNomeUsuarioEmprestimo(): string
    {
        return $this->nomeUsuarioEmprestimo;
    }

	public function setLoginUsuarioDevolucao(string $v): void
    {
        $this->loginUsuarioDevolucao = $v;
    }

    public function getLoginUsuarioDevolucao(): ?string
    {
        return $this->loginUsuarioDevolucao;
    }

	public function setNomeUsuarioDevolucao(string $v): void
    {
        $this->nomeUsuarioDevolucao = $v;
    }

    public function getNomeUsuarioDevolucao(): ?string
    {
        return $this->nomeUsuarioDevolucao;
    }

	public function setDtEmprestimo(DateTime $v): void
    {
        $this->dtEmprestimo = $v;
    }

    public function getDtEmprestimo(): DateTime
    {
        return $this->dtEmprestimo;
    }   

	public function setDtDevolucao(DateTime $v): void
    {
        $this->dtDevolucao = $v;
    }

    public function getDtDevolucao(): ?DateTime
    {
        return $this->dtDevolucao;
    } 
	
	public function setNomePessoa(string $v): void
    {
        $this->nomePessoa = $v;
    }

    public function getNomePessoa(): string
    {
        return $this->nomePessoa;
    } 

	public function setNumeroChave(string $v): void
    {
        $this->numeroChave = $v;
    }

    public function getNumeroChave(): string
    {
        return $this->numeroChave;
    } 

	public function setNomePredio(string $v): void
    {
        $this->nomePredio = $v;
    }

    public function getNomePredio(): string
    {
        return $this->nomePredio;
    } 

    public function foiDevolvida() {
        return !is_null($this->nomeUsuarioDevolucao);
    }
}
