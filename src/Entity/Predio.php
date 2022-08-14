<?php

namespace Emprestimo\Chaves\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name = "predios",
 *      schema = "chaves",
 *      options = {"comment":"Prédios que terão as chaves das salas emprestadas."},
 *      uniqueConstraints={
 *            @UniqueConstraint(name="predio_instituicao", columns={"nome", "instituicao_id"})
 *      }
 * )
 */
class Predio
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer")
     */
    private $id;
    /**
     * @Column(type="string", name="nome", length=255, nullable=false, options={"comment":"Nome do prédio."})
     */
    private $nome;
    /**
     * @Column(type="string", name="fl_ativo", columnDefinition="CHAR(1) NOT NULL", options={"comment":"FLag que define se o prédio ainda é usado."})
     */
    private $flAtivo;
    /**
     * @ManyToOne(targetEntity="Instituicao", inversedBy="predios")
     */
    private $instituicao;
    /**
     * @ManyToMany(targetEntity="Usuario", inversedBy="predios", cascade={"persist"})
     */
    private $usuarios;
    /**
     * @OneToMany(targetEntity="Chave", mappedBy="predio")
     */
    private $chaves;


    public function __construct()
    {
        $this->chaves = new ArrayCollection();
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

    public function setNome(string $v): void
    {
        $this->nome = $v;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function addChave(Chave $chave)
    {
        if (!$this->chaves->contains($chave)) {
            $chave->setPredio($this);
            $this->chaves->add($chave);
        }
    }

    public function getChaves(): Collection
    {
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

    public function addUsuario(Usuario $usuario): void
    {
        if (!$this->usuarios->contains($usuario)) {
            $this->usuarios->add($usuario);
            $usuario->addPredio($this);
        }
    }

    public function removeUsuario(Usuario $usuario): void
    {
        if ($this->usuarios->contains($usuario)) {
            $this->usuarios->removeElement($usuario);
            $usuario->removePredio($this);
        }
    }

    public function getUsuarios(): Collection
    {
        return $this->usuarios;
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
