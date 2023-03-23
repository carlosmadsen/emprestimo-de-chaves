<?php

namespace Emprestimo\Chaves\Entity;

use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(
 *  name="chaves",
 *  schema="chaves",
 *  options={"comment":"Chaves das salas dos prédios."},
 *  uniqueConstraints={@UniqueConstraint(name="numero_idx", columns={"numero", "predio_id"})}
 * )
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
     * @Column(type="string", name="numero", unique=false, length=6, nullable=false, options={"comment":"Número da chave."})
     */
    private $numero;
    /**
     * @Column(type="string", name="descricao", unique=false, length=255, nullable=true, options={"comment":"Descrição da salda desta chave."})
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
    /**
    * @OneToOne(targetEntity="Emprestimo", mappedBy="chave")
    */
    private $emprestimo;


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

    public function getPredio(): Predio
    {
        return $this->predio;
    }

    public function setPredio(Predio $predio): void
    {
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

    public function getEmprestimo(): ?Emprestimo
    {
        return $this->emprestimo;
    }

    public function setEmprestimo(Emprestimo $emprestimo): void
    {
        $this->emprestimo = $emprestimo;
    }
}
