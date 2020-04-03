<?php


namespace App\Factories;


use Nette\Application\UI\Form;

class PrefillQuestionFormFactory {

	public static function create(callable $onSuccess): Form {

		$f = new Form();

		$f->addText('prefill');

		$f->addSubmit('ok', 'Vytvořit novou otázku na tomto poli.');

		$f->onSuccess[] = $onSuccess;

		return $f;

	}

}