<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter {

	protected function startup() {
		parent::startup();

		$css = md5_file(__DIR__ . '/../../www/css/main.css');
		$js = md5_file(__DIR__ . '/../../www/js/main.js');
		$this->template->version = md5($css . $js);

	}

}