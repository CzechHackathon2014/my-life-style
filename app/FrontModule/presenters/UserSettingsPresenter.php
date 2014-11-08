<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Aprila\BaseFormRenderer,
	Nette\Forms\Form,
	Nette\Application\UI,
	Nette\Mail\Message,
	Nette\Utils\DateTime;


class UserSettingsPresenter extends \Aprila\BaseSecurePresenter
{

	/** @var \Aprila\Model\UserManager @inject */
	public $userManager;

	/** @var */
	public $person;


	/**
	 * @var \Nette\Mail\IMailer
	 * @inject
	 */
	public $mailer;


	public function actionDefault()
	{
		$this->person = $this->userManager->get($this->user->id);
	}


	public function renderDefault()
	{
		$this->template->person = $this->person;
	}


	public function renderPassword()
	{

	}


	public function renderEmail()
	{
		$person = $this->userManager->get($this->user->getId());
		$this->template->person = $person;

		$now = new DateTime();
		$diff = $now->diff($person->change_email_requested);

		if ($diff->h < 1 && $diff->days == 0) {
			$this->template->openStepTwo = TRUE;
		}

	}


	public function handleDeleteAvatar()
	{
		$status = $this->userManager->removeAvatar($this->user->getId());
		if ($status) {
			$this->user->identity->avatar = '';

			$this->flashMessage('Photo was removed');
		} else {
			$this->flashMessage('Problem with photo removing', 'error');
		}

		$this->template->person = $this->userManager->get($this->user->getId());
	}


	/**
	 * Change Password Form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentChangePasswordForm()
	{
		$session = $this->session->getSection('forgotPassword');

		$form = BaseFormRenderer::factory();
		if (isset($session->password) && !empty($session->password)) {
			$form->addHidden('old_password', $session->password)
				->setRequired();
			unset($session->password);
		} else {
			$form->addPassword('old_password', 'Current password:')
				->setRequired('Please enter your current password.');
		}

		$password = $form->addPassword('new_password', 'New password:')
			->setRequired('Please enter new password.')
			->addRule(Form::MIN_LENGTH, 'Password min length is %d chars', 6); // TODO do something like Form::PASSWORD validation ?

		$password->setAttribute('class', 'b-password-input--with-strength');

		$form->addSubmit('send', 'Save');

		$form->onSuccess[] = $this->changePasswordFormSucceeded;

		return $form;
	}


	/**
	 * @param UI\Form $form
	 */
	public function changePasswordFormSucceeded(UI\Form $form)
	{
		try {
			$values = $form->getValues();
			$this->userManager->changeUserPassword($values->old_password, $values->new_password, $this->user->identity->id);
			$form->setValues(array(), TRUE);
			$this->flashMessage('Your password change was successful.');
		} catch (\Aprila\InvalidArgumentException $e) {
			$this->flashMessage('Fill your current password correctly.', 'error');
		}
	}

	/**
	 * User Form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentUserForm()
	{
		$form = BaseFormRenderer::factory();

		$form->addText('name', 'Name:');

		if ($this->userManager->useFiles) {
			$form->addUpload('avatar', 'Photo:')
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, 'Please select image file');
		}

		$form->addSubmit('send', 'Update profile');

		$form->onSuccess[] = $this->userFormSucceeded;

		if ($this->person) {
			$form->setDefaults($this->person);
		}

		return $form;
	}


	/**
	 * @param UI\Form $form
	 */
	public function userFormSucceeded(UI\Form $form)
	{
		$values = $form->getValues();

		// if we not use images
		if (!isset($values->avatar)) {
			$values['avatar'] = NULL;
		}

		$person = $this->userManager->edit(
			$this->user->id,
			array(
				'name' => $values->name,
				'avatar' => $values->avatar)
		);

		$this->person = $person;

		// update identity
		$this->user->identity->name = $person->name;
		$this->user->identity->avatar = $person->avatar;

		$this->flashMessage('Profile was updated');
		$this->redirect('default');

	}


	/**
	 * Change Email Form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentChangeEmailForm()
	{
		$form = BaseFormRenderer::factory();
		$form->addText('new_email', 'New email:')
			->setType('email')
			->setRequired('Please enter new email address.');

		$form->addSubmit('send', 'Change');

		$form->onSuccess[] = $this->changeEmailFormSucceeded;

		return $form;
	}


	public function changeEmailFormSucceeded(UI\Form $form)
	{
		$values = $form->getValues();

		$template = $this->createTemplate();
		$template->setFile(__DIR__ . "/../templates/UserSettings/emails/SendChangeEmailToken.latte");

		$mailMessage = new Message;
		$mailMessage->setFrom($this->context->parameters['email']['from']);

		$status = $this->userManager->changeEmailStepOne(
			$this->user->id,
			$values->new_email,
			$template,
			$mailMessage,
			$this->mailer
		);

		if ($status) {
			$this->flashMessage("Check your's both address for codes");
		} else {
			$this->flashMessage("Ups... Something's wrong", 'error');
		}
		$this->redirect('email');

	}


	/**
	 * Change Email step two Form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentChangeEmailStepTwoForm()
	{
		$form = BaseFormRenderer::factory();
		$form->addText('code_one', 'Code one:')
			->setRequired('Please enter first code.');

		$form->addText('code_two', 'Code two:')
			->setRequired('Please enter second code.');


		$form->addSubmit('send', 'Change');

		$form->onSuccess[] = $this->changeEmailStepTwoFormSucceeded;

		return $form;
	}


	/**
	 * @param UI\Form $form
	 */
	public function changeEmailStepTwoFormSucceeded(UI\Form $form)
	{
		$values = $form->getValues();

		$newEmail = $this->userManager->changeEmailStepTwo(
			$this->user->id,
			$values->code_one,
			$values->code_two
		);

		if ($newEmail) {
			// change identity email
			$this->user->identity->email = $newEmail;
			$this->flashMessage("Your email was changed");
		} else {
			$this->flashMessage("Ups... Something's wrong", 'error');
		}

	}
}