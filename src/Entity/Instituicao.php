<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Predio;
use Emprestimo\Chaves\Entity\Usuario;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="instituicoes", schema="chaves", options={"comment":"Instituição que tem os prédios."})
 */
class Instituicao
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
    /**
     * @Column(type="string", name="sigla", unique=true, length=30, nullable=false, options={"comment":"Sigla da instituição."})
     */
    private $sigla;
    /** 
     * @Column(type="string", name="nome", unique=true, length=255, nullable=false, options={"comment":"Nome da instituição."})
     */
	private $nome;
    /**
 	 * @OneToMany(targetEntity="Predio", mappedBy="predios")
 	 */
	private $predios;
    /**
 	 * @OneToMany(targetEntity="Usuario", mappedBy="instituicao")
 	 */
	private $usuarios;

    public function __construct() {
	    $this->predios = new ArrayCollection();
   		$this->usuarios = new ArrayCollection();
	}

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function setSigla(string $v): void
    {
        $this->sigla = $v;
    }

    public function getSigla(): string
    {
        return $this->sigla;
    }

    public function setNome(string $v): void
    {
        $this->nome = $v;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function addPredio(Predio $predio) {
		$predio->setInstituicao($this);
		$this->predios->add($predio);
	}

	public function getPredios(): Collection {
		return $this->predios;
	}

    public function addUsuario(Usuario $usuario) {
		$usuario->setInstituicao($this);
		$this->usuarios->add($usuario);
	}

	public function getUsuarios(): Collection {
		return $this->usuarios;
	}

}