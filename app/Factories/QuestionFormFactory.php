<?php


namespace App\Factories;


use Nette\Application\UI\Form;

class QuestionFormFactory {

	public static function create(callable $onSuccess): Form {

		$f = new Form();

		$f->addText('name', 'Název')
			->setRequired('Název musí byt vyplněn');

		$f->addText('description', 'Popisek (zobrazí se s otázkou)')
			->setRequired('Popisek musí byt vyplněn');

		$f->addUpload('image', 'Vyber jeden obrázek k otázce (MUSÍ být JPG)')
			->setRequired('Obrázek je povinný');

		$f->addText('answer_description', 'Popisek (zobrazí se s odpovědí)');

		$f->addMultiUpload('answer_images', 'Vyber až 3 obrázky k odpovědi (MUSÍ být JPG)');

		$f->addText('date', 'Datum')
			->setRequired('Datum musí být vyplněno')
			->setHtmlAttribute('placeholder', 'dd.mm.yyyy')
			->addRule($f::PATTERN, 'Datum musí být ve formátu dd.mm.yyyy', '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}');

		$f->addText('correctAnswer', 'Správná odpověď')
			->setRequired('Správná odpověď musí být vyplněna')
			->addRule($f::PATTERN, 'Správná odpověď musí být 1-2 písmena a pak 1-2 čísla, např. D6 nebo AA22', '^[A-Z]{1,2}[0-9]{1,2}$');

		$f->addSubmit('ok', 'Uložit');

		$f->onSuccess[] = $onSuccess;

		return $f;

	}

}