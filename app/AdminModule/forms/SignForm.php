<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\AdminModule\Forms;

use Nette\Application\UI\Control,
	Nette\Application\UI\Form,
	Nette\Security\AuthenticationException,
	Nette\Security\User,
	Nette\Http\Session;

class SignForm extends Control
{

	/** @var array */
	public $onSuccess;

	/** @var User */
	private $user;

	/** @var Session  */
	private $session;

	/** @var int */
	public $loginCounter = 0;


	/**
	 * @param User $user
	 * @param Session $session
	 */
	public function __construct(User $user, Session $session)
	{
		$this->user = $user;
		$this->session = $session;
	}


	public function render()
	{
		echo $this['form'];
	}


	/**
	 * @param Form $form
	 */
	public function processForm(Form $form)
	{
		$values = $form->getValues();
		$login = $this->session->getSection('login');

		if (isset($login->counter) && $login->counter > 5) {
			sleep(2);
		}

		if ($values->remember) {
			$this->user->setExpiration('14 days', FALSE);
		} else {
			$this->user->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->user->login($values->username, $values->password);
			$login->counter = 0;
			$this->onSuccess();

		} catch (AuthenticationException $e) {
			$login->counter++;
			$form->addError($e->getMessage());
		}


	}

	/**
	 * @return Form
	 */
	protected function createComponentForm()
	{
		$form = new Form;
		$form->addText('username', 'Username:')
			->setRequired('Please enter your username.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.');

		// TODO : simple captcha system?
		// if ($login->counter > 5)
		// $form->addText('captcha', 'Are You human?');

		$form->addCheckbox('remember', 'Keep me signed in');

		$form->addSubmit('send', 'Log in');

		$form->onSuccess[] = $this->processForm;

		return $form;
	}

}