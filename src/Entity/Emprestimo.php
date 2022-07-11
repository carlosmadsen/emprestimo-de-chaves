<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Pessoa;
use Emprestimo\Chaves\Entity\Usuario;

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
	 * @ManyToOne(targetEntity="Pessoa",fetch="LAZY")
     * @Column(type="integer", name="id_pessoa", nullable=false, options={"comment":"Identificador da pessoa."})
	 */
	private $pessoa;
    /**
	 * @ManyToOne(targetEntity="Chave",fetch="LAZY")
     * @Column(type="integer", name="id_chave", nullable=false, options={"comment":"Identificador da chave."})
	 */
	private $chave;
    /**
	 * @ManyToOne(targetEntity="Usuario",fetch="LAZY")
     * @Column(type="integer", name="id_usuario_emprestimo", nullable=false, options={"comment":"Usuário que lançou o empréstimo."})
	 */
	private $usuarioEmprestimo;
    /**
	 * @ManyToOne(targetEntity="Usuario",fetch="LAZY")
     * @Column(type="integer", name="id_usuario_devolucao", nullable=true, options={"comment":"Usuário que lançou a devolução."})
	 */
	private $usuarioDevolucao;
    /** 
     * @Column(type="string", name="dt_emprestimo", columnDefinition="TIMESTAMP NOT NULL", options={"comment":"Data e hora do empréstimo."})
     */
    private $dtEmprestimo;
    /** 
     * @Column(type="string", name="dt_devolucao", columnDefinition="TIMESTAMP NOT NULL", options={"comment":"Data e hora da devolução."})
     */
    private $dtDevolucao;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function getPessoa(): Pessoa {
		return $this->pessoa;
	}

	public function setPessoa(Pessoa $pessoa): void {
		$this->pessoa = $pessoa;
	}

    public function getChave(): Chave {
		return $this->chave;
	}

	public function setChave(Chave $chave): void {
		$this->chave = $chave;
	}

    public function getUsuarioEmprestimo(): Usuario {
		return $this->usuarioEmprestimo;
	}

	public function setUsuarioEmprestimo(Usuario $usuario): void {
		$this->usuarioEmprestimo = $usuario;
	}

    public function getUsuarioDevolucao(): Usuario {
		return $this->usuarioDevolucao;
	}

	public function setUsuarioDevolucao(Usuario $usuario): void {
		$this->usuarioDevolucao = $usuario;
	}

    public function setDtEmprestimo(string $v): void
    {
        $this->dtEmprestimo = $v;
    }

    public function getDtEmprestimo(): string
    {
        return $this->dtEmprestimo;
    }

    public function setDtDevolucao(string $v): void
    {
        $this->dtDevolucao = $v;
    }

    public function getDtDevolucao(): string
    {
        return $this->dtDevolucao;
    }
}