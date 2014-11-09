<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin.@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use Nette\Application\UI\Form,
	Nette\Utils\DateTime;

class EveningPresenter extends DiaryPresenter 
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

		# deside if we wan't to display new day form or redirect to any other
		# presenter
		# now we're providing set of links instead of "magic"

	}

	public function renderSleeptime()
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}
	}

	public function renderTooLong()
	{

	}

	public function createComponentEveningForm()
	{
		$form = new Form();
		$now  = new DateTime();

		$form -> addTextarea('experience_1', 'První zážitek');
		$form -> addTextarea('experience_2', 'Druhý zážitel');
		$form -> addTextarea('experience_3', 'Třetí zážitek');
		$form -> addText('time')->setDefaultValue($now->format('H:i'));

		$form -> addSubmit('submitEvening', 'Uzavřít den');

		$form -> onSuccess[] = callback($this, 'saveEveningForm');

		return $form;
	}

	public function saveEveningForm(Form $form)
	{
		$values = $form -> getValues();

		$today = new DateTime;
		$time = new DateTime;
		$end_time = $time->from($today->format('Y-m-d ').$values['time']);

		$experiences = array(
			$values['experience_1'], $values['experience_2'], $values['experience_3'],
		);

		$last = $this-> dayManager -> findAllDaysForUser($this->user->id)->order('date DESC')->limit(1);
		if (count($last) == 0){
			$store_time = new DateTime();
		} else {
			$store_time = $last->fetch()->date;
		}
		
		
		# evaluate the day, we should always have the beginning
		# $this -> dayManager->startDay(1, $values['mood'])
		$this -> dayManager -> evaluateDay($this->user->id, $store_time, $experiences);
		$this -> dayManager -> endDay($this->user->id, $store_time, $end_time);

		$this -> redirect('evening:sleeptime');
	}

}
