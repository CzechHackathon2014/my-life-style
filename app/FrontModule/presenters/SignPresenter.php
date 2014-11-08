<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Nette\Application\UI,
	app\AdminModule\Forms\ISignFormFactory;

class SignPresenter extends BasePresenter
{
	/** @var ISignFormFactory */
	private $signFormFactory;

	/** @var \Aprila\Forms\UserFormFactory */
	private $formFactory;

	/**
	 * @var \Aprila\Model\UserManager
	 * @inject
	 */
	public $users;


	/** @var int */
	public $loginCounter = 0;


	/**
	 * @param ISignFormFactory $signFormFactory
	 */
	public function __construct(ISignFormFactory $signFormFactory, \Aprila\Forms\UserFormFactory $formFactory)
	{
		$this->formFactory = $formFactory;
		$this->signFormFactory = $signFormFactory;
	}


	public function actionOut()
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}


	public function renderIn()
	{
		$login = $this->session->getSection('login');
		if (isset($login->counter)) {
			$this->loginCounter = $login->counter;
		}
		$this->template->loginCounter = $this->loginCounter;
	}


	/**
	 * Sign-in form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentSignForm()
	{
		$signForm = $this->signFormFactory->create();

		$signForm->onSuccess[] = function () {
			$this->redirect('Dashboard:');
		};

		return $signForm;

	}


	/**
	 * Sign-up form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentRegistrationForm()
	{
		$registrationForm = $this->formFactory->createRegistrationForm();

		$registrationForm->onSuccess[] = function () {
			$this->flashMessage('You are successfully registered and logged in');
			$this->redirect('Dashboard:');
		};

		return $registrationForm;

	}

}