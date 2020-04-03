<?php

namespace App\Factories;

use Nette\Application\UI\Form;

class GuessFormFactory {

	public static function create($onSuccess): Form {

		$f = new Form();

		$f->addText('guess', 'Můj tip')
			->setRequired('Musíš si tipnout')
			->addRule($f::PATTERN, 'Tip musí být 1-2 písmena a pak 1-2 čísla, např. D6 nebo AA22', '^[A-Z]{1,2}[0-9]{1,2}$');

		$f->addSubmit('ok', 'Tipnout si');

		$f->onSuccess[] = $onSuccess;

		return $f;
	}

}