<?php


namespace App\Factories;


use Nette\Application\UI\Control;

class AdminMenu extends Control {

	const MENU_ITEMS = [
		'default' => [
			'title' => 'Fotky',
			'link' => 'Admin:default',
		],
		'users' => [
			'title' => 'Uživatelé',
			'link' => 'Admin:users',
		],
	];

	/** @var string */
	protected $currentView;

	public function __construct(string $currentView) {
		$this->currentView = $currentView;
	}

	public function render() {

		$this->template->menuItems = self::MENU_ITEMS;

		$this->template->currentView = $this->currentView;

		$this->template->setFile(__DIR__ . '/AdminMenu.latte');
		$this->template->render();


	}

}