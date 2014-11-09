<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Nette,
	App\Model,
	Nette\DateTime;

/**
 * Base presenter for all application presenters.
 */
abstract class DiaryPresenter extends BasePresenter
{
	/**
	 * @var \App\Model\DayManager @inject
	 */
	public $dayManager;

	private $next_action;

	public function beforeRender()
	{
		parent::beforeRender();
		$this -> template -> next_action = $this->getNextAction();

		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}

		
	}

	private function getNextAction(){

		$next_action = array();

		$now = new DateTime();
		$last = $this-> dayManager -> findAllDaysForUser($this->user->id)->order('date DESC')->limit(1);
		
		# no day, lets start with morning or evening
		if (count($last) === 0){
			if ( 5 < intval($now->format('h')) && intval($now->format('h')) < 15 ){
				return array('action' => 'Morning:default', 'label' => 'Začít nový den');
			} else {
				return array('action' => 'Evening:default', 'label' => 'Uzavřít dnešní den');
			}
		}

		$last = $last->fetch();

		# last day is not ended
		if (!$last->end_time){
			# it is safe to close it - yet today
			if ($last['date'] == $now->format('Y-m-d 00:00:00')){
				return array('action' => 'Evening:default', 'label' => 'Uzavřít dnešní den');
			}
			# Older day, just forgot it!
			return array('action' => 'Morning:default', 'label' => 'Začít nový den');
		}
		# last day ended and it was today
		if ($last['date'] == $now->format('Y-m-d 00:00:00')){
			#$this->flashMessage('# Day is over and new hasn\'t begun yet. Go get some sleep and come back tomorrow!');
			#$this->redirect('Dashboard:list');
			#return array('action' => 'Dashboard:list', 'label' => 'Procházet deníček');
			return null;
		}

		# or it is mornoing
		return array('action' => 'Morning:default', 'label' => 'Začít nový den');
	}
}
