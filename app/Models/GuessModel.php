<?php

namespace App\Models;

use Nette\Database\Context;

class GuessModel {

	/** @var Context  */
	protected $db;

	public function __construct(Context $db) {
		$this->db = $db;
	}

	public function fetchByQuestion(int $questionId, $userId) {

		return $this->db
			->table(\Table::GUESS)
			->where('question_id', $questionId)
			->where('user_id', $userId)
			->fetch();

	}

	public function saveGuess(array $data) {

		$guess = $this->fetchByQuestion($data['question_id'], $data['user_id']);
		if ($guess) {
			$data['id'] = $guess['id'];
		}

		$data['date'] = new \DateTime();

		$coords =QuestionModel::alphaNumberToCartesian($data['guess']);
		$data['x'] = $coords['x'];
		$data['y'] = $coords['y'];
		unset($data['guess']);

		if ($data['id'] ?? null) {
			$this->db
				->table(\Table::GUESS)
				->wherePrimary($data['id'])
				->update($data);
		} else {
			$this->db
				->table(\Table::GUESS)
				->insert($data);
		}

	}

	public function fetchUserGuesses(int $userId) {

		return $this->db
			->table(\Table::GUESS)
			->where('user_id', $userId)
			->fetchAssoc('question_id');

	}

}