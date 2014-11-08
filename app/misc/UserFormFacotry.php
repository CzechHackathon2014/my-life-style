<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace Aprila\Forms;

use Nette,
	Nette\Application\UI\Form,
	Nette\Security\User,
	Aprila\Model\UserManager;


class UserFormFactory extends Nette\Object
{
	/** @var User */
	protected $user;

	/** @var UserManager  */
	protected $userManager;


	public function __construct(User $user, UserManager $userManager)
	{
		$this->user = $user;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function createRegistrationForm()
	{
		$form = new Form;

		$form->addText('email', 'Username:');

		$form->addText('liame', 'Email:')
			->setType('email')
			->setRequired('Please enter e-mail.')
			->addRule(Form::EMAIL, 'This is not valid e-mail.');

		$form->addText('name', 'Name:')
			->setRequired('Please enter your name.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter your password.')
			->addRule(Form::MIN_LENGTH, 'Password min length is %d chars', 6); // TODO do something like Form::PASSWORD validation ?


		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = array($this, 'registrationFormSucceeded');
		return $form;
	}


	public function registrationFormSucceeded($form, $values)
	{
		$this->user->setExpiration('14 days', FALSE);

		if ($values->email != ''){
			$form->addError('spam protection activated');
			return;
		}

		try {
			$this->userManager->addUser($values->liame, $values->liame, $values->password, 'user', $values->name);
		} catch (\Aprila\DuplicateEntryException $e){
			$form['liame']->addError('User with this email is registred. Please sign in');

			return true;
		}

		try {
			$this->user->login($values->liame, $values->password);
		} catch (Nette\Security\AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}
}