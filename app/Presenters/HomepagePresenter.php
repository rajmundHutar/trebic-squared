<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Factories\GuessFormFactory;
use App\Factories\LoginFormFactory;
use App\Factories\PrefillQuestionFormFactory;
use App\Factories\RegisterFormFactory;
use App\Models\GuessModel;
use App\Models\QuestionModel;
use App\Models\UserModel;
use Nette;

final class HomepagePresenter extends BasePresenter {

	const WIDTH = 50;
	const HEIGHT = 30;

	/** @var UserModel */
	protected $userModel;

	/** @var QuestionModel */
	protected $questionModel;

	/** @var GuessModel */
	protected $guessModel;

	public function __construct(
		UserModel $userModel,
		QuestionModel $questionModel,
		GuessModel $guessModel
	) {

		$this->userModel = $userModel;
		$this->questionModel = $questionModel;
		$this->guessModel = $guessModel;
	}

	public function actionLogout() {

		$this->user->logout(true);
		$this->redirect('default');

	}

	public function createComponentRegisterForm() {

		return RegisterFormFactory::create(
			function(Nette\Application\UI\Form $form) {

				$values = $form->getValues(true);

				try {

					unset($values['passwordCheck']);

					$this->userModel->save($values);

					$this->user->login($values['email'], $values['password']);

					$this->flashMessage('Regsitrace úspěšná!', \Flash::SUCCESS);

				} catch (Nette\Security\AuthenticationException $e) {
					$this->flashMessage('Chybý e-mail nebo heslo', \Flash::ERROR);
				} catch (Nette\Database\UniqueConstraintViolationException $e) {
					$this->flashMessage('Tento e-mail je již registrován. Zkuste se přihlásit.', \Flash::ERROR);
				}

				$this->redirect('default');

			}
		);

	}

	public function createComponentLoginForm() {

		return LoginFormFactory::create(
			function(Nette\Application\UI\Form $form) {

				$values = $form->getValues(true);

				try {

					$this->user->login($values['email'], $values['password']);
					$this->flashMessage('Přihlášen!', \Flash::SUCCESS);

				} catch (Nette\Security\AuthenticationException $e) {
					$this->flashMessage('Chybný e-mail nebo heslo', \Flash::ERROR);
				}

				$this->redirect('default');

			}
		);

	}

	public function createComponentGuessForm() {

		return GuessFormFactory::create(
			function(Nette\Application\UI\Form $form) {

				$hour = date('H');
				if (QuestionModel::isGameOn()) {

					$values = $form->getValues(true);
					$question = $this->questionModel->fetchByDate(new \DateTime());

					$values['user_id'] = $this->user->getId();
					$values['question_id'] = $question['id'];

					$this->guessModel->saveGuess($values);

					$this->flashMessage('Váš tip byl uložen.');

				} else if (QuestionModel::isBeforeGame()) {
					$this->flashMessage(sprintf('Velitelský čas je %s. Je tedy před %s hodinou.', date('H:i:s'), QuestionModel::START));
				} else if (QuestionModel::isAfterGame()) {
					$this->flashMessage(sprintf('Je nám líto, velitelský čas je %s. Je už tedy po %s hodině.', date('H:i:s'), QuestionModel::END));
				}

				$this->redirect('default');
			}
		);

	}

	public function createComponentPrefillQuestionForm() {

		return PrefillQuestionFormFactory::create(function(Nette\Application\UI\Form $form) {

			$values = $form->getValues(true);
			$this->redirect('Admin:question', ['prefill' => $values['prefill'] ?? null]);

		});

	}

	public function renderDefault($highlightCorrect = null, $highlightGuess = null) {

		$this->template->question = $this->questionModel->fetchByDate(new \DateTime());

		$this->template->showQuestion = QuestionModel::isGameOn();

		$highlightCorrect = mb_strtoupper($highlightCorrect ?: '');
		$highlightGuess = mb_strtoupper($highlightGuess ?: '');

		$this->template->highlightCorrect = $highlightCorrect;
		$this->template->highlightGuess = $highlightCorrect != $highlightGuess ? $highlightGuess : null;

		$this->template->height = self::HEIGHT;
		$this->template->width = self::WIDTH;

	}

	public function renderPlayedGames() {

		$questions = $this->questionModel->fetchAll();

		$this->template->userGuesses = $this->user->isLoggedIn() ? $this->guessModel->fetchUserGuesses($this->user->getId()) : [];

		$this->template->questions = $questions;

		[$total, $score] = $this->questionModel->fetchScore();

		$this->template->total = $total;
		$this->template->score = $score;

	}

}
