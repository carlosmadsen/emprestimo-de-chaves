<?php

namespace Emprestimo\Chaves\Entity;

use Emprestimo\Chaves\Entity\Chave;
use Emprestimo\Chaves\Entity\Pessoa;

/**
 * @Entity
 * @Table(name="emprestimos", schema="chaves", options={"comment":"Chaves emprestadas."})
 */
class Emprestimo
{
    /**
     * @Id
     * @GeneratedValue
     * @Column(type="integer", name="id_emprestimo", options={"comment":"Identificador do emprestimo."})
     */
    private $id;

}