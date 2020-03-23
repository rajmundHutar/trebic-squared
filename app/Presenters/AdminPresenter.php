<?php


namespace App\Presenters;

use App\Factories\QuestionFormFactory;
use App\Models\QuestionModel;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class AdminPresenter extends Presenter {

	/** @var QuestionModel */
	protected $questionModel;

	public function __construct(QuestionModel $questionModel) {
		$this->questionModel = $questionModel;
	}

	public function handleDelete(int $id) {
		$this->questionModel->delete($id);
		$this->flashMessage('Smazáno', \Flash::SUCCESS);
		$this->redirect('default');
	}

	public function actionQuestion(int $id = null) {

		if ($id) {
			$question = $this->questionModel->fetch($id);
			$this['questionForm']->setDefaults([
				'name' => $question['name'],
				'image' => $question['image'],
				'date' => $question['date']->format('d.n.Y'),
				'correctAnswer' => QuestionModel::cartesianToAlphaNumber((int) $question['x'], (int) $question['y']),
			]);
		}

	}


	public function createComponentQuestionForm() {

		return QuestionFormFactory::create(function (Form $form) {

			$values = $form->getValues(true);
			$id = $this->getParameter('id');
			if ($id) {
				$values['id'] = $id;
			}

			$this->questionModel->save($values);

			$this->flashMessage('Uloženo', \Flash::SUCCESS);
			$this->redirect('default');

		});

	}

	public function renderDefault() {

		$this->template->questions = $this->questionModel->fetchAll();

	}

}