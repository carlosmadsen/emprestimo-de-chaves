<?php

namespace Emprestimo\Chaves\Helper;

trait SessionFilterTrait
{
	private $sessionFilterKey = '';

	public function defineSessionFilterKey($codigo): void 
	{
		$this->$sessionFilterKey = $codigo;
	} 

	public function defineFilterSesssion($dados = []): void
    {
		if (!array_key_exists('filtro', $_SESSION)) {
			$_SESSION['filtro'] = [];
		}
		if (!array_key_exists($this->$sessionFilterKey, $_SESSION['filtro'])) {
			$_SESSION['filtro'][$this->$sessionFilterKey] = [];
		}
        $_SESSION['filtro'][$this->$sessionFilterKey] = $dados;
    }

	public function getFilterSession($campo)
    {
		if (!array_key_exists('filtro', $_SESSION)) {
			return null;
		}
		if (!array_key_exists($this->$sessionFilterKey, $_SESSION['filtro'])) {
			return null;
		}
        return array_key_exists($campo,  $_SESSION['filtro'][$this->$sessionFilterKey]) ? $_SESSION['filtro'][$this->$sessionFilterKey][$campo] : null;
    }

	public function clearFilterSession(): void
    {
		if (array_key_exists('filtro', $_SESSION)) {
			if (array_key_exists($this->$sessionFilterKey, $_SESSION['filtro'])) {
				unset($_SESSION['filtro'][$this->$sessionFilterKey]);		
			}
		}       
    }

	
} 