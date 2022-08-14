<?php

namespace Emprestimo\Chaves\Entity;

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
     * @Column(type="string", name="label_identificacao_pessoa", unique=true, length=255, nullable=false, options={"comment":"Label que será usado para o número de identificação das pessoas."})
     */
    private $labelIdentificacaoPessoa;
    /**
     * @Column(type="string", name="label_documento_pessoa", unique=true, length=255, nullable=false, options={"comment":"Label que será usado para o número de documento das pessoas."})
     */
    private $labelDocumentoPessoa;
    /**
     * @OneToMany(targetEntity="Predio", mappedBy="instituicao")
     */
    private $predios;
    /**
     * @OneToMany(targetEntity="Usuario", mappedBy="instituicao")
     */
    private $usuarios;
    /**
     * @OneToMany(targetEntity="Pessoa", mappedBy="instituicao")
     */
    private $pessoas;
    /**
     * @OneToMany(targetEntity="Historico", mappedBy="instituicao")
     */
    private $gistoricos;

    public function __construct()
    {
        $this->predios = new ArrayCollection();
        $this->usuarios = new ArrayCollection();
        $this->pessoas = new ArrayCollection();
        $this->historicos = new ArrayCollection();
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

    public function addPredio(Predio $predio)
    {
        $predio->setInstituicao($this);
        $this->predios->add($predio);
    }

    public function getPredios(): Collection
    {
        return $this->predios;
    }

    public function addUsuario(Usuario $usuario)
    {
        $usuario->setInstituicao($this);
        $this->usuarios->add($usuario);
    }

    public function getUsuarios(): Collection
    {
        return $this->usuarios;
    }

    public function addPessoa(Pessoa $pessoa)
    {
        $pessoa->setInstituicao($this);
        $this->pessoas->add($pessoa);
    }

    public function getPessoas(): Collection
    {
        return $this->pessoa;
    }

    public function addHistorico(Historico $historico)
    {
        $historico->setInstituicao($this);
        $this->historicos->add($historico);
    }

    public function getHistoricos(): Collection
    {
        return $this->historicos;
    }
  
    public function setLabelIdentificacaoPessoa(string $v): void
    {
        $this->labelIdentificacaoPessoa = $v;
    }

    public function getLabelIdentificacaoPessoa(): string
    {
        return $this->labelIdentificacaoPessoa;
    }

    public function setLabelDocumentoPessoa(string $v): void
    {
        $this->labelDocumentoPessoa = $v;
    }

    public function getLabelDocumentoPessoa(): string
    {
        return $this->labelDocumentoPessoa;
    }
}
