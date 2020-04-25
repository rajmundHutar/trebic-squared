<?php

namespace App\Models;

use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class MailModel {

	/** @var string */
	protected $host;

	/** @var string */
	protected $username;

	/** @var string */
	protected $password;

	/** @var LinkGenerator */
	protected $linkGenerator;

	/** @var ITemplateFactory */
	protected $templateFactory;

	public function __construct(
		string $host,
		string $username,
		string $password,
		LinkGenerator $linkGenerator,
		ITemplateFactory $templateFactory
	) {

		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->linkGenerator = $linkGenerator;
		$this->templateFactory = $templateFactory;

	}

	public function sendForgottenPasswordMail(string $email, string $hash) {

		$template = $this->createTemplate();

		$template->email = $email;
		$template->hash = $hash;
		$template->setFile(__DIR__ . '/emails/forgottenPasswordMail.latte');

		$mail = new Message();
		$mail
			->setFrom('trebicsqaured@gmail.com', 'Třebíč²')
			->addTo($email)
			->setSubject('Obovení hesla')
			->setHtmlBody($template);

		$this->sendMail($mail);

	}



	protected function sendMail(Message $mail) {

		$this->createMailer()->send($mail);

	}


	protected function createMailer(): IMailer {

		return new SmtpMailer([
			'host' => $this->host,
			'username' => $this->username,
			'password' => $this->password,
			'secure' => 'ssl',
			'port' => 465,
		]);

	}

	protected function createTemplate(): ITemplate {
		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);

		return $template;
	}

}