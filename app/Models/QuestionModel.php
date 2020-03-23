<?php


namespace App\Models;

use Nette\Database\Context;
use Nette\Http\FileUpload;

class QuestionModel {

	/** @var Context */
	protected $db;

	/** @var string */
	protected $wwwDir;

	public function __construct(string $wwwDir, Context $db) {
		$this->db = $db;
		$this->wwwDir = $wwwDir;
	}

	public function save(array $data) {

		$imagePath = $this->moveImage($data['image']);
		$data['image'] = $imagePath;

		$data['date'] = new \DateTime($data['date']);

		$cartesian = self::alphaNumberToCartesian($data['correctAnswer']);
		unset($data['correctAnswer']);
		$data['x'] = $cartesian['x'];
		$data['y'] = $cartesian['y'];

		if ($data['id'] ?? null) {

		} else {
			$this->db
				->table(\Table::QUESTION)
				->insert($data);
		}

	}

	public function fetchAll() {

		return $this->db
			->table(\Table::QUESTION)
			->order('date DESC')
			->fetchAll();

	}

	public function fetch(int $id) {

		return $this->db
			->table(\Table::QUESTION)
			->wherePrimary($id)
			->fetch();

	}

	public function delete(int $id) {

		return $this->db
			->table(\Table::QUESTION)
			->wherePrimary($id)
			->delete();

	}

	public static function alphaNumberToCartesian(string $coordinates): array {

		preg_match('~^([A-Z]+)([0-9]+)$~i', $coordinates, $matches);
		return [
			'x' => ord($matches[1]) - 65, // 65 is A,
			'y' => (int)$matches[2],
		];

	}

	public static function cartesianToAlphaNumber(int $x, int $y): string {

		$x = chr($x + 65); // 65 is A
		return "{$x}{$y}";

	}

	protected function moveImage(FileUpload $image): string {

		$newName = '/images/questions/' . $image->getSanitizedName();

		move_uploaded_file($image->getTemporaryFile(), $this->wwwDir . $newName);

		return $newName;

	}

}