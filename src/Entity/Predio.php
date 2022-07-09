<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Usuario;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="predios", schema="chaves", options={"comment":"Prédios que terão as chaves das salas emprestadas."})
 */
class Predio
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id_predio", options={"comment":"Identificador do prédio."})
     */
    private $id;
 	/** 
     * @Column(type="string", name="nome", unique=true, length=255, nullable=false, options={"comment":"Nome do prédio."})
     */
    private $nome;
	/**
 	 * @OneToMany(targetEntity="Chave", mappedBy="chaves")
 	 */
	private $chaves;
	/** 
     * @Column(type="string", name="fl_ativo", columnDefinition="CHAR(1) NOT NULL", options={"comment":"FLag que define se o prédio ainda é usado."})
     */
    private $flAtivo;
	/**
     * @ManyToMany(targetEntity="Usuario", mappedBy="predios", cascade={"persist"})
	 * @JoinTable(name="usuarios_predios", schema="chaves")
     */
    private $usuarios;

	public function __construct() {
		$this->chaves = new ArrayCollection();
		$this->usuarios = new ArrayCollection();
	}

	public function setNome(string $v): void
    {
        $this->nome = $v;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

	public function addChave(Chave $chave) {
		$chave->setPredio($this);
		$this->chaves->add($chave);		
	}

	public function getChaves(): Collection {
		return $this->chaves;
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

	public function addUsuario(Usuario $usuario):  void {
	    if (!$this->usuario->contains($predio)) {
    		$this->usuarios->add($usuario);
    		$usuario->addPredio($this);
		}
	}

	public function getUsuarios(): Collection {
    	return $this->usuarios;
	}
}