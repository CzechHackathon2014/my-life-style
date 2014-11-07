<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\AdminModule\Presenters;

use Nette\Application\UI,
	app\AdminModule\Forms\ISignFormFactory,
	Nette\Security\AuthenticationException;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends \Aprila\BaseSecurePresenter
{

	/** @var ISignFormFactory */
	private $signFormFactory;


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
	public function __construct(ISignFormFactory $signFormFactory)
	{
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


}
