<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Factories\AdminMenu;
use App\Factories\QuestionFormFactory;
use App\Models\QuestionModel;
use App\Models\UserModel;
use Nette\Application\UI\Form;

final class AdminPresenter extends BasePresenter {

	/** @var QuestionModel */
	protected $questionModel;

	/** @var UserModel */
	protected $userModel;

	public function __construct(
		QuestionModel $questionModel,
		UserModel $userModel
	) {

		$this->questionModel = $questionModel;
		$this->userModel = $userModel;
	}

	public function startup() {

		parent::startup();

		if (!$this->user->isInRole('admin')) {
			$this->redirect('Homepage:');
		}

	}

	public function handleDelete(int $id) {

		$this->questionModel->delete($id);
		$this->flashMessage('Smazáno', \Flash::SUCCESS);
		$this->redirect('default');
	}

	public function actionQuestion(int $id = null, string $prefill = null) {

		if ($prefill) {
			$this['questionForm']->setDefaults(
				[
					'correctAnswer' => $prefill,
				]
			);
		}
		else {
			if ($id) {
				$question = $this->questionModel->fetch($id);
				$this->template->question = $question;
				$this['questionForm']->setDefaults(
					[
						'name' => $question['name'],
						'description' => $question['description'],
						'image' => $question['image'],
						'answer_description' => $question['answer_description'],
						'answer_images' => $question['answer_images'],
						'date' => $question['date']->format('d.n.Y'),
						'correctAnswer' => QuestionModel::cartesianToAlphaNumber((int) $question['x'], (int) $question['y']),
					]
				);
			}
		}

	}

	public function createComponentAdminMenu() {

		return new AdminMenu($this->view);

	}

	public function createComponentQuestionForm() {

		return QuestionFormFactory::create(
			function(Form $form) {

				$values = $form->getValues(true);

				$id = $this->getParameter('id');
				if ($id) {
					$values['id'] = $id;
				}

				$this->questionModel->save($values);

				$this->flashMessage('Uloženo', \Flash::SUCCESS);
				$this->redirect('default');

			}
		);

	}

	public function renderDefault() {

		$this->template->questions = $this->questionModel->fetchAll(false);

	}

	public function renderUsers() {

		$this->template->users = $this->userModel->fetchAll();

	}

}