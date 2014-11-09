<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use Nette\Application\UI\Form,
	Nette\Utils\DateTime;
use Nette\Utils\Paginator;

class DashboardPresenter extends DiaryPresenter
{
	/**
	 * @var int
	 */
	public $page = 1;

	public function renderDefault()
	{

		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}

		# dashboard is used as a nifty router,
		# based on data e decide what to show, if in doubts display links

		$now = new DateTime();
		$last = $this -> template -> days = $this-> dayManager -> findAllDaysForUser($this->user->id)->order('date DESC')->limit(1) -> fetch();
		if ($last === FALSE){
			# no day, lets start with dashboard
			$this->redirect('Dashboard:list');
		}
		if (!$last['end_time']){
			if ($last['date'] == $now->format('Y-m-d 00:00:00')){
				$this->redirect('Evening:default');
			}
			# Older day, just forgot it!
			$this->redirect('Morning:default');
		}
		if ($last['date'] == $now->format('Y-m-d 00:00:00')){
			$this->redirect('Dashboard:list');
		}
		# there is no event, we start with welcome on Dashboard:list


		# show morning

		$this->redirect('Morning:default');

		# last choice is to show list
	}

	public function renderList($page = 1)
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}


		$this->page = $page;

		$paginator = new Paginator();
		$paginator->setItemsPerPage(10);
		$paginator->setPage($this->page);
		$count = $this->dayManager->getCountDays($this->user->id);
		$paginator->setItemCount($count);

		if ($count > $paginator->itemsPerPage && !$paginator->last) {
			$this->template->page = $page;
			$this->template->showMoreButton = TRUE;
		} else {
			$this->template->showMoreButton = FALSE;
		}

		$now = new DateTime();
		$this->template->days = $this->dayManager->findAllDaysForUser($this->user->id)
			->order('date DESC')
			->limit($paginator->itemsPerPage, $paginator->offset)
			->fetchAll();

		$experiences = array();
		$slept = array();
		foreach ($this -> template ->days as $id => $day) {
			$experiences[$id] = $this -> dayManager -> findAllExperiencesForDay($id);
			$slept[$id] = null;
		}

		$this -> template -> experiences = $experiences;
		$this -> template -> slept = $slept;
		$this -> template -> now = $now;
		$this -> template -> today = $now->format('Y-m-d');


		if ($this->isAjax()){
			$this->redrawControl('timelineList');
			$this->redrawControl('timelineListButton');
		}
	}

	public function renderDetail($date)
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isAuthenticated() !== true ){
			$this->redirect('homepage:default');
		}

		$this -> template -> detail = $this -> dayManager -> findAllDaysForUser($this->user->getId()) -> where('date = ?', $date);
	}

}
