<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace app\FrontModule\presenters;

use Nette,
	Nette\Application\UI\Form,
	Nette\DateTime;

class ContactPresenter extends BasePresenter
{

	/**
	 * @var \Nette\Mail\IMailer @inject
	 */
	public $mailer;


	public function renderDefault()
	{

	}


	public function createComponentContactForm()
	{
		$form = new Form();
		// $form->addProtection('CSRF Protection');
		$control = $form->addHidden('what');
		$control->setHtmlId('quick-contact-what');

		// email fake
		$form->addText('email', '');
		// Name
		$form->addText('name', 'Jméno nebo přezdívka:');
		// Email
		$form->addText('liame', 'Email nebo telefon:')
			->setRequired('Please enter Your email or phone number');
		// Message
		$form->addTextArea('note', 'Vaše zpráva:');
		//Button
		$form->addSubmit('actionSend', 'Odeslat');

		$form->onSuccess[] = callback($this, 'saveContactForm');

		return $form;
	}


	public function saveContactForm(Form $form)
	{
		$values = $form->getValues();
		$httpRequest = $this->context->getService('httpRequest');
		$ip = $httpRequest->getRemoteAddress();

		if ($values['email'] != '') {
			return FALSE;
		}

		$data = array(
			'name' => $values['name'],
			'what' => $values['what'],
			'email' => $values['liame'],
			'note' => $values['note'],
			'ip' => $ip,
			'time' => new DateTime('now')
		);

		// TODO: move to service
		$mail = new Nette\Mail\Message();
		$mail->setFrom('info@test.aprila.cz')
			->addTo('iamchemix@gmail.com')
			->setSubject('New contact message from website')
			->setHtmlBody('
				<h1>Hello</h1>
				<p>
				<br>name: <strong>' . $data['name'] . '</strong>
				<br>email/phone: <strong>' . $data['email'] . '</strong>
				<br>time: ' . $data['time'] . '
				<br>IP: ' . $data['ip'] . '
				<br>message:
				<br>
				' . $data['note'] . '
				</p>
			');
		try {
			$this->mailer->send($mail);
			$this->flashMessage('Thanks, messasge was sent.');
			$form->setValues(array(), TRUE);
		} catch (Exception $e) {
			$this->flashMessage('Ups, some error with message sending. Try again later.', 'error');
		}

		$this->redirect('default');

	}
} 
