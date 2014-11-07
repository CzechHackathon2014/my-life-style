<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\AdminModule\Presenters;

use Aprila\BaseFormRenderer;
use Aprila\DuplicateEntryException;
use Nette\Forms\Form;
use Nette\Application\UI;
use Nette\Utils\Strings;
use Nette\Application\ForbiddenRequestException;

class UsersPresenter extends \Aprila\BaseSecurePresenter
{

	/**
	 * @var \Aprila\Model\UserManager
	 * @inject
	 */
	public $userManager;

	/**
	 * @var \Nette\Database\Table\IRow
	 */
	public $person;

	/**
	 * @var string
	 */
	public $fulltextQuery;


	public function startup()
	{
		parent::startup();
		if ($this->user->isAllowed('Users', 'edit')) {
			$this->redirect('Dashboard:default');
		}
	}


	public function renderDefault()
	{
		$empty = FALSE;
		if ($this->fulltextQuery) {
			$this->template->listOfUsers = $this->userManager->findFulltext($this->fulltextQuery);
		} else {
			$this->template->listOfUsers = $this->userManager->findAll();
			if (count($this->template->listOfUsers) === 1) {
				$empty = TRUE;
			}
		}
		$this->template->fulltextQuery = $this->fulltextQuery;
		$this->template->listOfDeactivatedUsers = $this->userManager->findAllDeactivated();
		$this->template->empty = $empty;
	}


	public function renderDeactivated()
	{
		$this->template->listOfDeactivatedUsers = $this->userManager->findAllDeactivated();
	}


	public function renderAdd()
	{

	}


	public function actionEdit($id)
	{
		$this->person = $this->userManager->get($id);

		if (!$this->person) {
			throw new ForbiddenRequestException('Access denied');
			$this->redirect('Dashboard:default');
		}
	}


	public function renderEdit()
	{
		$this->template->person = $this->person;
	}


	/**
	 * @param $personId int
	 */
	public function handleDeleteAvatar($personId)
	{
		$status = $this->userManager->removeAvatar($personId);
		if ($status) {
			$this->flashMessage('Photo was removed');
		} else {
			$this->flashMessage('Problem with photo removing', 'error');
		}

		$this->person = $this->userManager->get($personId);

		/* todo: implement ajax?
		$this->redrawControl('flashes');
		$this->redrawControl('profilePhoto');
		$this->redrawControl('layoutAvatar'); // if current user

		if (!$this->isAjax()) {
			$this->redirect('this');
		}
		*/
	}


	/**
	 * @param $userId int
	 */
	public function handleDeactivate($userId)
	{
		$status = $this->userManager->deactivateUser($userId);
		if ($status) {
			$this->flashMessage('User was deactivated');
		} else {
			$this->flashMessage('Error', 'error');
		}
		$this->redirect('default');

	}


	/**
	 * @param $userId int
	 */
	public function handleActivate($userId)
	{
		$status = $this->userManager->activateUser($userId);
		if ($status) {
			$this->flashMessage('User was activated');
		} else {
			$this->flashMessage('Error', 'error');
		}
		$this->redirect('default');

	}


	/**
	 * User form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentUserForm()
	{
		$userRoleArray = $this->userManager->findUserRoles();

		$form = BaseFormRenderer::factory();
		$form->addText('username', 'Username:')
			->setRequired('Please enter username.');

		$password = $form->addPassword('password', 'Password:');
		$password->setAttribute('class', 'b-password-input--with-strength')
			->addCondition(Form::FILLED)
			->addRule(Form::MIN_LENGTH, 'Password min length is %d chars', 6);;

		if (!$this->person) {
			$password->setRequired('Please enter password.');
		}

		$form->addText('email', 'E-mail:')
			->setRequired('Please enter e-mail.')
			->addRule(Form::EMAIL, 'This is not valid e-mail.');

		$form->addText('name', 'Name:');

		if ($this->userManager->useFiles) {
			$form->addUpload('avatar', 'Avatar:')
				->addCondition(Form::FILLED)
				->addRule(Form::IMAGE, 'Please select image file');
		}

		$form->addSelect("role", "User role:", $userRoleArray)
			->setRequired('Select user role.');


		if ($this->person) {
			$form->addHidden('id');
			$form->setDefaults($this->person);
		}

		$form->addSubmit('send', $this->person ? 'Save user' : 'Add user');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = $this->userFormSucceeded;

		return $form;
	}


	/**
	 * @param UI\Form $form
	 * @throws \Aprila\DuplicateEntryException
	 */
	public function userFormSucceeded(UI\Form $form)
	{
		try {
			$values = $form->getValues();

			// if we not use images
			if (!isset($values->avatar)) {
				$values['avatar'] = NULL;
			}

			if (isset($values->id) && $values->id > 0) {
				// edit
				$this->userManager->editUser(
					$values->id,
					$values->email,
					$values->username,
					$values->password,
					$values->role,
					$values->name,
					$values->avatar
				);

				$this->flashMessage('User was updated');
				$this->redirect('edit', $values->id);
			} else {
				// add
				$person = $this->userManager->addUser(
					$values->email,
					$values->username,
					$values->password,
					$values->role,
					$values->name,
					$values->avatar
				);
				$this->flashMessage('User was added');
//				$form->setValues(array(), TRUE);
				$this->redirect('edit', $person->id);
			}

		} catch (DuplicateEntryException $e) {
			$form->addError('Duplicate username or email.');
//			$form['username']->addError('Duplicate username.');
//			$form['username']->setValue($values->username . '2');
		}
	}


	protected function createComponentQuickSearchForm()
	{
		$form = BaseFormRenderer::factory();
		$form->setMethod('GET');
		$form->addText('q', 'Query:');

		$form->addSubmit('search', 'Search');
		$form->onSuccess[] = $this->quickSearchFormSucceeded;

		return $form;
	}


	/**
	 * @param Form $form
	 */
	public function quickSearchFormSucceeded(Form $form)
	{
		$v = $form->getValues();
		if (Strings::length($v->q) >= 3) {
			$this->fulltextQuery = $v->q;
		}
		if ($this->isAjax()) {
			$this->redrawControl('datalist');
		}
	}
}