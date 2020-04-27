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

					$this->flashMessage('Registrace úspěšná!', \Flash::SUCCESS);

				} catch (Nette\Security\AuthenticationException $e) {
					$this->flashMessage('Chybý e-mail nebo heslo', \Flash::ERROR);
				} catch (Nette\Database\UniqueConstraintViolationException $e) {
					$this->flashMessage('Tento e-mail nebo jméno je již registrováno.', \Flash::ERROR);
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

					$this->flashMessage('Váše odpověď byla uložena.');

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

	public function renderDefault($highlightCorrect = null, $highlightGuess = null, $heatMap = null) {

		$question = $this->questionModel->fetchByDate(new \DateTime());
		$this->template->question = $question;
		$this->template->userGuess = $question ? $this->guessModel->fetchByQuestion($question['id'], (int) $this->user->getId()) : null;
		$this->template->showQuestion = QuestionModel::isGameOn();

		$highlightCorrect = mb_strtoupper($highlightCorrect ?: '');
		$highlightGuess = mb_strtoupper($highlightGuess ?: '');
		if ($highlightGuess) {
			$highlightGuess = explode(',', $highlightGuess);
		} else {
			$highlightGuess = [];
		}

		if ($heatMap) {
			$this->template->heatMap = $this->guessModel->fetchHeatMap();
		}

		$this->template->highlightCorrect = $highlightCorrect;
		$this->template->highlightGuess = !in_array($highlightCorrect, $highlightGuess) ? $highlightGuess : [];

		$this->template->height = self::HEIGHT;
		$this->template->width = self::WIDTH;

	}

	public function renderPlayedGames() {

		// Fetches today AFTER its done
		$this->template->questions = $this->questionModel->fetchAll();
		$this->template->userGuesses = $this->user->isLoggedIn() ? $this->guessModel->fetchUserGuesses($this->user->getId()) : [];

	}

	public function renderGameDetail(int $id) {

		$question = $this->questionModel->fetch($id);

		$d = new \DateTime();
		if (!$question || $question['date']->format('Y-m-d') > $d->format('Y-m-d')) {
			$this->flashMessage('Otázka nenalezena', \Flash::ERROR);
			$this->redirect('playedGames');
		}

		if ($question['date']->format('Y-m-d') == $d->format('Y-m-d') && !QuestionModel::isAfterGame()) {
			$this->flashMessage('Toto kolo se ještě hraje', \Flash::WARNING);
			$this->redirect('default');
		}

		$correctCoords = QuestionModel::cartesianToAlphaNumber( $question['x'], $question['y']);

		$score = $this->questionModel->fetchScore($id);
		$stats = $this->questionModel->fetchStats($id);
		$stats['userCount'] = $this->userModel->fetchCount();
		$stats['pointsDistribution'] = [];
		$stats['otherGuesses'] = [];

		foreach($score as $row) {
			if (isset($stats['pointsDistribution'][$row['points']])) {
				$stats['pointsDistribution'][$row['points']] += 1;
			} else {
				$stats['pointsDistribution'][$row['points']] = 1;
			}

			if ($row['guess'] != $correctCoords) {
				$stats['otherGuesses'][] = $row['guess'];
			}
		}

		$stats['otherGuesses'] = array_unique($stats['otherGuesses']);

		$this->template->question = $question;
		$this->template->score = $score;
		$this->template->stats = $stats;

	}

	public function renderScore() {

		$this->template->total = $this->questionModel->fetchTotalScore();

	}

}
