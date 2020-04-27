<?php

namespace App\Models;

use Nette\Database\Context;

class GuessModel {

	const LOW_HEATMAP_COLOR = [
		65, 169, 76
	];

	const HIGH_HEATMAP_COLOR = [
		255, 40, 0
	];

	/** @var Context  */
	protected $db;

	public function __construct(Context $db) {
		$this->db = $db;
	}

	public function fetchByQuestion(int $questionId, int $userId) {

		return $this->db
			->table(\Table::GUESS)
			->where('question_id', $questionId)
			->where('user_id', $userId)
			->fetch();

	}

	public function saveGuess(array $data) {

		$guess = $this->fetchByQuestion((int) $data['question_id'], (int) $data['user_id']);
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

	public function fetchHeatMap(): array {

		$rows = $this->db
			->table(\Table::GUESS)
			->select('COUNT(*) AS total, x, y')
			->group('x, y')
			->fetchAll();

		$res = [];
		foreach($rows as $row) {
			$key = QuestionModel::cartesianToAlphaNumber($row['x'], $row['y']);
			$res[$key] = $row['total'];
		}

		return $res;

	}

	public static function getHeatMapColor(int $number, int $max) {

		$percent = $number / $max;
		$rgb = [];
		for ($i = 0; $i < 3 ; $i++) {
			$rgb[$i] = round(self::LOW_HEATMAP_COLOR[$i] + $percent * (self::HIGH_HEATMAP_COLOR[$i] - self::LOW_HEATMAP_COLOR[$i]));
		}

		return sprintf('rgba(%d, %d, %d, .5)', $rgb[0], $rgb[1], $rgb[2]);

	}

}
