<?php

namespace App\Factories;

use Nette\Application\UI\Form;

class ForgottenPasswordFormFactory {

	public static function create(callable $onSuccess) {

		$f = new Form();

		$f->addText('email', 'E-mail, se kterým jste se registrovali.')
			->setRequired('E-mail je třeba vyplnit')
			->addRule($f::EMAIL, 'E-mail je třeba vyplnit správně');

		$f->addSubmit('ok', 'Resetovat heslo');

		$f->onSubmit[] = $onSuccess;

		return $f;

	}

}