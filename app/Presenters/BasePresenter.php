<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter {

	protected function startup() {
		parent::startup();

		$this->template->version = md5_file(__DIR__ . '/../../www/css/main.css');

	}

}