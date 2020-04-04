<?php


namespace App\Factories;


use Nette\Application\UI\Form;

class RegisterFormFactory {

	public static function create(callable $onSuccess): Form {

		$f = new Form();

		$f->addText('name', 'Název týmu')
			->setRequired('Zadejte název týmu');

		$f->addText('email', 'E-mail')
			->setRequired('Zadejte e-mail')
			->addRule($f::EMAIL, 'Zadjte validní e-mail');

		$f->addPassword('password', 'Heslo')
			->setRequired('Zadejte heslo');

		$f->addPassword('passwordCheck', 'Kontrola hesla')
			->setRequired('Zadejte prosím heslo ještě jednou pro kontrolu')
			->addRule(Form::EQUAL, 'Hesla se neshodují', $f['password']);

		$f->addSubmit('ok', 'Registrovat');

		$f->onSuccess[] = $onSuccess;

		return $f;

	}

}