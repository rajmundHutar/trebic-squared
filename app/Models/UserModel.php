<?php


namespace App\Models;


use Nette\Database\Context;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class UserModel implements IAuthenticator {

	/** @var Context  */
	protected $db;

	/** @var Passwords  */
	protected $passwords;

	public function __construct(Context $db, Passwords $passwords) {

		$this->db = $db;
		$this->passwords = $passwords;

	}

	public function save(array $data) {

		$data['password'] = $this->passwords->hash($data['password']);

		if ($data['id'] ?? null) {

			$this->db
				->table(\Table::USER)
				->wherePrimary($data['id'])
				->update($data);

		} else {

			$data['role'] = 'user';
			$this->db
				->table(\Table::USER)
				->insert($data);

		}

	}

	public function authenticate(array $credentials): IIdentity {

		[$email, $password] = $credentials;

		$row = $this->db
			->table(\Table::USER)
			->where('email', $email)
			->fetch();


		if (!$row) {
			throw new AuthenticationException('User not found.');
		}

		elseif (!$this->passwords->verify($password, $row['password'])) {

			throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);

		} elseif ($this->passwords->needsRehash($row['password'])) {

			$row->update([
				'password' => $this->passwords->hash($password),
			]);

		}

		return new Identity($row['id'], $row['role'], [
			'email' => $row['email'],
		]);

	}
}
