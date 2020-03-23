<?php


namespace App\Factories;


use Nette\Application\UI\Form;

class QuestionFormFactory {

	public static function create(callable $onSuccess): Form {

		$f = new Form();

		$f->addText('name', 'Název')
			->setRequired('Název musí byt vyplněn');

		$f->addUpload('image', 'Obrázek')
			->setRequired('Obrázek je povinný');

		$f->addText('date', 'Datum')
			->setRequired('Datum musí být vyplněno');

		$f->addText('correctAnswer', 'Správná odpověď')
			->setRequired('Správná odpověď musí být vyplněna')
			->addRule($f::PATTERN, 'Správná odpověď musí být 1-2 písmena a pak 1-2 čísla, např. D6 nebo AA22', '^[A-Z]{1,2}[0-9]{1,2}$');

		$f->addSubmit('ok', 'Uložit');

		$f->onSuccess[] = $onSuccess;

		return $f;

	}

}