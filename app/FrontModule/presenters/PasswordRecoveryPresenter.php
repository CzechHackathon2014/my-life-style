<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\UI;
use Nette\Utils\Strings;

/**
 * Password recovery presenter.
 */
class PasswordRecoveryPresenter extends BasePresenter
{

	/**
	 * @var \Aprila\Model\UserManager
	 * @inject
	 */
	public $users;

	/**
	 * @var \Nette\Mail\IMailer
	 * @inject
	 */
	public $mailer;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $token;


	public function renderRecovery()
	{

	}


	public function renderRecoveryStepTwo($email = NULL, $token = NULL)
	{
		$this->email = $email;
		$this->token = $token;
	}


	/**
	 * Recovery form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentRecoveryForm()
	{
		$form = new UI\Form;
		$form->addText('email', 'E-mail:')
			->setRequired('Please enter your e-mail.')
			->addRule(UI\Form::EMAIL);

		$form->addSubmit('send', 'Password reset');

		$form->onSuccess[] = $this->recoveryFormSucceeded;

		return $form;
	}


	/**
	 * @param UI\Form $form
	 */
	public function recoveryFormSucceeded(UI\Form $form)
	{
		$values = $form->getValues();
		$user = $this->users->getBy(array('email' => $values->email));
		if (!empty($user)) {

			$token = $this->users->generateRecoveryToken($user->id);

			$template = $this->createTemplate();
			$template->setFile(__DIR__ . "/../templates/PasswordRecovery/emails/SendResetPassword.latte");
			$template->token = $token;
			$template->email = $user->email;

			// send email
			$mail = new \Nette\Mail\Message;
			$mail->setFrom($this->context->parameters['email']['from'])
				->addTo($user->email)
				->setHtmlBody($template);

			try {
				$this->mailer->send($mail);
				$this->template->emailSent = TRUE;
			} catch (\Exception $e) {
				$this->flashMessage('Unexpected error. Please try again later.', 'error');
			}

		} else {
			$this->flashMessage('E-mail not found.');
		}
	}


	/**
	 * Recovery Step Two form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentRecoveryStepTwoForm()
	{
		$form = new UI\Form;
		$form->addText('email', 'E-mail:')
			->setRequired('Please enter your e-mail.')
			->addRule(UI\Form::EMAIL);

		$form->addText('token', 'Code:')
			->setRequired('Please enter the code from e-mail message');

		$form->addSubmit('send', 'Confirm');
		$form->onSuccess[] = $this->recoveryStepTwoFormSucceeded;

		$form->setDefaults(array(
			'email' => $this->email,
			'token' => $this->token
		));

		return $form;
	}


	/**
	 * @param UI\Form $form
	 */
	public function recoveryStepTwoFormSucceeded(UI\Form $form)
	{
		$values = $form->getValues();
		$user = $this->users->getBy(array('email' => $values->email));
		$verifed = $this->users->verifyToken($values->token, $user->id);

		if ($verifed) {
			$newPassword = Strings::random(22);
			$this->users->setUserPassword($newPassword, $user->id);
			$this->getUser()->login($user->username, $newPassword);
			$this->flashMessage('Set your new password.');
			$session = $this->session->getSection('forgotPassword');
			$session->password = $newPassword;
			$this->redirect('UserSettings:password');
		} else {
			$this->flashMessage('Problem with security code. Try again.', 'error');
		}
	}

}
