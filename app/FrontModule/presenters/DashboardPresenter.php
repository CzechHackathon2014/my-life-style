<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use Nette\Application\UI\Form,
	Nette\Utils\DateTime;

class DashboardPresenter extends DiaryPresenter
{

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

	public function renderList()
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}

		# TODO: pagination
		$now = new DateTime();
		$this -> template -> days = $this-> dayManager -> findAllDaysForUser($this->user->id)->order('date DESC')->limit(10) -> fetchAll();

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
