<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use Nette\Application\UI\Form,
	Nette\Utils\DateTime;

class DashboardPresenter extends BasePresenter
{

	/**
	 * @var \App\Model\DayManager @inject
	 */
	public $dayManager;

	public function renderDefault()
	{

		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}

		# dashboard is used as a nifty router,
		# based on data e decide what to show, if in doubts display links

		$today = new DateTime();

		# show morning
		#$this -> redirect('morning:default');

		# last choice is to show list
		$this -> redirect('list');

	}

	public function renderList()
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}

		# TODO: pagination
		$this -> template -> days = $this-> dayManager -> findAllForUser(1) -> limit(10);
	}

	public function renderDetail($date)
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isAuthenticated() !== true ){
			$this->redirect('homepage:default');
		}
		
		$this -> template -> detail = $this -> dayManager -> findAllForUser(1) -> where('date = ?', $date);
	}

}
