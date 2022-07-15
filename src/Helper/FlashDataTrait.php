<?php

namespace Emprestimo\Chaves\Helper;

trait FlashDataTrait
{
	public function defineFlashData($dados = []): void
    {
        $_SESSION['dados'] = $dados;
    }

	public function getFlashData(): array
    {
        return array_key_exists('dados', $_SESSION) ? $_SESSION['dados'] : [];
    }

	public function clearFlashData(): void
    {
        unset($_SESSION['dados']);
    }
} 