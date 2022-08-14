<?php

namespace Emprestimo\Chaves\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Entity
 * @Table(name="pessoas",
 * schema="chaves",
 * options={"comment":"Pessoas que pegam chaves emprestadas."},
 * uniqueConstraints={
 *            @UniqueConstraint(name="nome_instituicao", columns={"nome", "instituicao_id"}),
 *            @UniqueConstraint(name="documento_instituicao", columns={"nr_documento", "instituicao_id"})
 *      }
 * )
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
     * @Column(type="string", name="nr_identificacao", unique=false, length=30, nullable=true, options={"comment":"Número de identificação."})
     */
    private $nrIdentificacao;
    /**
     * @Column(type="string", name="nr_documento", unique=false, length=30, nullable=false, options={"comment":"Número de documento."})
     */
    private $nrDocumento;
    /**
     * @Column(type="string", name="nome", unique=false, length=255, nullable=false, options={"comment":"Nome da pessoa."})
     */
    private $nome;
    /**
     * @Column(type="string", name="observacao", length=255, nullable=true, options={"comment":"Observações acerca desta pessoa."})
     */
    private $observacao;
    /**
     * @ManyToOne(targetEntity="Instituicao", inversedBy="pessoas")
     */
    private $instituicao;
    /**
    * @OneToOne(targetEntity="Emprestimo", mappedBy="pessoa")
    */
    private $emprestimo;


    public function __construct()
    {
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

    public function setNrDocumento(string $v): void
    {
        $this->nrDocumento = $v;
    }

    public function getNrDocumento(): string
    {
        return $this->nrDocumento;
    }

    public function setObservacao(string $v): void
    {
        $this->observacao = $v;
    }

    public function getObservacao(): string
    {
        return $this->observacao;
    }

    public function setNrIdentificacao(string $v): void
    {
        $this->nrIdentificacao = $v;
    }

    public function getNrIdentificacao(): string
    {
        return $this->nrIdentificacao;
    }

    public function getInstituicao(): Instituicao
    {
        return $this->instituicao;
    }

    public function setInstituicao(Instituicao $instituicao): void
    {
        $this->instituicao = $instituicao;
    }

    public function getEmprestimo(): Emprestimo
    {
        return $this->emprestimo;
    }

    public function setEmprestimo(Emprestimo $emprestimo): void
    {
        $this->Emprestimo = $emprestimo;
    }
}
