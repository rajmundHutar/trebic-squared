<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Factories\LoginFormFactory;
use App\Factories\RegisterFormFactory;
use App\Models\UserModel;
use Nette;


final class HomepagePresenter extends Nette\Application\UI\Presenter {

	/** @var UserModel */
	protected $userModel;

	public function __construct(UserModel $userModel) {
		$this->userModel = $userModel;
	}

	public function actionLogout() {

		$this->user->logout(true);
		$this->redirect('default');

	}

	public function createComponentRegisterForm() {

		return RegisterFormFactory::create(function (Nette\Application\UI\Form $form){

			$values = $form->getValues(true);

			try {

				unset($values['passwordCheck']);

				$this->userModel->save($values);

				$this->user->login($values['email'], $values['password']);

				$this->flashMessage( 'Regsitrace úspěšná!', \Flash::SUCCESS);

			} catch (Nette\Security\AuthenticationException $e) {
				$this->flashMessage('Chybý e-mail nebo heslo', \Flash::ERROR);
			} catch (Nette\Database\UniqueConstraintViolationException $e) {
				$this->flashMessage('Tento e-mail je již registrován. Zkuste se přihlásit.', \Flash::ERROR);
			}


			$this->redirect('default');

		});

	}

	public function createComponentLoginForm() {

		return LoginFormFactory::create(function (Nette\Application\UI\Form $form){

			$values = $form->getValues(true);

			try {

				$this->user->login($values['email'], $values['password']);
				$this->flashMessage('Přihlášen!', \Flash::SUCCESS);

			} catch (Nette\Security\AuthenticationException $e) {
				$this->flashMessage('Chybý e-mail nebo heslo', \Flash::ERROR);
			}

			$this->redirect('default');


		}) ;



	}

}
