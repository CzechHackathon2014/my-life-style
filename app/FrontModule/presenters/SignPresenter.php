<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Kdyby\Facebook\FacebookApiException;
use Nette\Application\UI,
	app\AdminModule\Forms\ISignFormFactory;
use Nette\Security\Identity;
use Tracy\Debugger;

class SignPresenter extends BasePresenter
{
	/** @var ISignFormFactory */
	private $signFormFactory;

	/** @var \Aprila\Forms\UserFormFactory */
	private $formFactory;

	/** @var \FacebookUserManager */
	public $facebookUserManager;

	/** @var \Kdyby\Facebook\Facebook */
	private $facebook;



	/** @var int */
	public $loginCounter = 0;


	/**
	 * @param ISignFormFactory $signFormFactory
	 */
	public function __construct(ISignFormFactory $signFormFactory,
								\Aprila\Forms\UserFormFactory $formFactory,
								\Kdyby\Facebook\Facebook $facebook,
								\FacebookUserManager $facebookUserManager
	)
	{
		parent::__construct();

		$this->facebook = $facebook;
		$this->facebookUserManager = $facebookUserManager;
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



	/** @return \Kdyby\Facebook\Dialog\LoginDialog */
	protected function createComponentFbLogin()
	{
		$dialog = $this->facebook->createDialog('login');
		/** @var \Kdyby\Facebook\Dialog\LoginDialog $dialog */

		$dialog->onResponse[] = function (\Kdyby\Facebook\Dialog\LoginDialog $dialog) {
			$fb = $dialog->getFacebook();

			if (!$fb->getUser()) {
				$this->flashMessage("Sorry bro, facebook authentication failed.");
				return;
			}

			/**
			 * If we get here, it means that the user was recognized
			 * and we can call the Facebook API
			 */

			try {
				$me = $fb->api('/me');

				if (!$existing = $this->facebookUserManager->findByFacebookId($fb->getUser())) {
					/**
					 * Variable $me contains all the public information about the user
					 * including facebook id, name and email, if he allowed you to see it.
					 */
					$existing = $this->facebookUserManager->registerFromFacebook($fb->getUser(), $me);
				}

				/**
				 * You should save the access token to database for later usage.
				 *
				 * You will need it when you'll want to call Facebook API,
				 * when the user is not logged in to your website,
				 * with the access token in his session.
				 */
				// we don't use it now
				// $this->facebookUserManager->updateFacebookAccessToken($fb->getUser(), $fb->getAccessToken());

				/**
				 * Nette\Security\User accepts not only textual credentials,
				 * but even an identity instance!
				 */
				$this->user->login(new Identity($existing->id, $existing->role, $existing));

				/**
				 * You can celebrate now! The user is authenticated :)
				 */

			} catch (FacebookApiException $e) {
				/**
				 * You might wanna know what happened, so let's log the exception.
				 *
				 * Rendering entire bluescreen is kind of slow task,
				 * so might wanna log only $e->getMessage(), it's up to you
				 */
				Debugger::log($e, 'facebook');
				$this->flashMessage("Sorry bro, facebook authentication failed hard.");
			}

			$this->redirect('Dashboard:');
		};

		return $dialog;
	}


}