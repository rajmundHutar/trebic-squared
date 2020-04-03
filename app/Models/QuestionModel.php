<?php


namespace App\Models;

use Nette\Database\Context;
use Nette\Http\FileUpload;

class QuestionModel {

	const MAX_POINTS = 10;
	const MAX_BONUS_POINTS = 5;
	const START = 10; // Hours >= 19
	const END = 22; // Hours < 22

	/** @var Context */
	protected $db;

	/** @var string */
	protected $wwwDir;

	public function __construct(string $wwwDir, Context $db) {
		$this->db = $db;
		$this->wwwDir = $wwwDir;
	}

	public function save(array $data) {

		$data['image'] = implode(';', $this->moveImages([$data['image']], 'questions'));
		$data['answer_images'] = implode(';', $this->moveImages($data['answer_images'], 'answers'));

		$data['date'] = new \DateTime($data['date']);

		$cartesian = self::alphaNumberToCartesian($data['correctAnswer']);
		unset($data['correctAnswer']);
		$data['x'] = $cartesian['x'];
		$data['y'] = $cartesian['y'];

		if ($data['id'] ?? null) {
			$this->db
				->table(\Table::QUESTION)
				->wherePrimary($data['id'])
				->update($data);
		} else {
			$this->db
				->table(\Table::QUESTION)
				->insert($data);
		}

	}

	public function fetchAll(bool $past = true) {

		$query = $this->db
			->table(\Table::QUESTION)
			->order('date DESC');

		if ($past) {
			$d = new \DateTime();
			$query->where('date <= ?', $d->format('Y-m-d'));
		}

		return $query->fetchAll();

	}

	public function fetch(int $id) {

		return $this->db
			->table(\Table::QUESTION)
			->wherePrimary($id)
			->fetch();

	}

	public function fetchByDate(\DateTime $date) {

		return $this->db
			->table(\Table::QUESTION)
			->where('date', $date->format('Y-m-d'))
			->fetch();

	}

	public function fetchScore() {
		$score = [];
		$total = [];

		$questions = $this->fetchAll();

		foreach ($questions as $question) {
			$score[$question['id']] = [];
			$guesses = $question->related('guess');
			$bonusPoints = $this->fetchBonusPoints($question['id']);
			foreach ($guesses as $guess) {

				$points = self::countPoints($question['x'], $question['y'], $guess['x'], $guess['y']);

				$userBonusPoints = $bonusPoints[$guess['user_id']] ?? 0;

				$score[$question['id']][] = [
					'user' => $guess->user,
					'date' => $guess['date'],
					'points' => $points,
					'bonusPoints' => $userBonusPoints,
					'guess' => self::cartesianToAlphaNumber($guess['x'], $guess['y']),
				];

				if (isset($total[$guess->user_id])) {
					$total[$guess->user_id]['totalPoints'] += $points + $userBonusPoints;
				} else {
					$total[$guess->user_id] = [
						'user' => $guess->user,
						'guess' => $guess,
						'totalPoints' => $points + $userBonusPoints,
					];
				}
			}

			usort($score[$question['id']], function ($a, $b) {
				return $b['points'] <=> $a['points'];
			});
		}

		usort($total, function ($a, $b) {
			return $b['totalPoints'] <=> $a['totalPoints'];
		});

		return [$total, $score];

	}

	public function fetchBonusPoints(int $questionId): array {

		$rows = $this->db
			->query('SELECT `user_id`, `a`.`date`, `q`.`name`
				FROM `guess` AS `a`
				LEFT JOIN `question` AS `q` ON `q`.`id` = `a`.`question_id`
				WHERE `a`.`x` = `q`.`x` AND `a`.`y` = `q`.`y`
				AND `a`.`question_id` = ?
				ORDER BY `a`.`date`
				LIMIT ?
			', $questionId, self::MAX_BONUS_POINTS);

		$res = [];
		foreach ($rows as $key => $row) {

			$res[$row['user_id']] = self::MAX_BONUS_POINTS - $key;

		}

		return $res;

	}

	public function delete(int $id) {

		return $this->db
			->table(\Table::QUESTION)
			->wherePrimary($id)
			->delete();

	}


	public static function alphaNumberToCartesian(string $coordinates): array {

		preg_match('~^([A-Z]+)([0-9]+)$~i', $coordinates, $matches);
		if (count($matches) != 3) {
			throw new \InvalidArgumentException('Invalid coordinates: ' . $coordinates);
		}
		return [
			'x' => self::alphaToInt($matches[1]),
			'y' => $matches[2] - 1,
		];

	}

	public static function cartesianToAlphaNumber(int $x, int $y): string {

		$x = self::intToAlpha($x);
		return "{$x}" . ($y + 1);

	}

	/** Input int si 0 based */
	public static function intToAlpha(int $int): string {

		$int += 1;
		$res = [];

		do {
			$q = ($int - 1) % 26;
			array_unshift($res, chr($q + 65));
			$int = floor(($int - 1) / 26);
		} while ($int > 0);

		return implode('', $res);

	}

	/**
	 * Numbers are zero based
	 * A => 0
	 * B => 1
	 * Z => 25
	 * AZ => 51
	 * BA => 52
	 * CV => 99
	 */
	public static function alphaToInt(string $alpha): int {

		$res = 0;
		$array = array_reverse(str_split($alpha));
		$multi = 0;
		foreach ($array as $char) {
			$res += (ord($char) - 64) * pow(26, $multi); // 65 is A
			$multi++;
		}
		return $res - 1;
	}

	public static function countPoints($qx, $qy, $ax, $ay) {

		$distance = max(abs($qx - $ax), abs($qy - $ay));

		return max(self::MAX_POINTS - $distance, 0);

	}

	protected function moveImages(array $images, string $folder): array {

		$newPaths = [];
		foreach ($images as $image) {
			$ext = '.jpg';
			/** @var FileUpload $image */
			$name = bin2hex(random_bytes(10));
			$newPath = '/images/' . $folder . '/' . $name . $ext;
			move_uploaded_file($image->getTemporaryFile(), $this->wwwDir . $newPath);
			$newPaths[] = $newPath;
		}

		return $newPaths;

	}

	public static function isGameOn() {

		$hour = date('H');
		return $hour >= self::START && $hour < self::END;

	}

	public static function isBeforeGame() {

		$hour = date('H');
		return $hour < self::START;

	}

	public static function isAfterGame() {

		$hour = date('H');
		return $hour > self::END;

	}

}