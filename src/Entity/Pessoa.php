<?php

namespace Emprestimo\Chaves\Entity;

/**
 * @Entity
 * @Table(name="pessoas", schema="chaves", options={"comment":"Pessoas que pegam chaves emprestadas."})
 */
class Pessoa
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id_pessoa", options={"comment":"Identificador da pessoa."})
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


}