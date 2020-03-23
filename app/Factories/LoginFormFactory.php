<?php


namespace App\Factories;


use Nette\Application\UI\Form;

class LoginFormFactory {

	public static function create(callable $onSuccess): Form {

		$f = new Form();

		$f->addText('email', 'E-mail')
			->setRequired('Zadejte e-mail');;

		$f->addPassword('password', 'Heslo')
			->setRequired('Zadejte heslo');

		$f->addSubmit('ok', 'Přihlásit');

		$f->onSuccess[] = $onSuccess;

		return $f;

	}

}