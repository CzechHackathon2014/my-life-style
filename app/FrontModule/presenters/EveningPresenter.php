<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin.@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use Nette\Application\UI\Form,
	Nette\Utils\DateTime;

class EveningPresenter extends DairyPresenter 
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

		$form -> addHidden('time_adjusted');
		$form -> addTextarea('experience_1');
		$form -> addTextarea('experience_2');
		$form -> addTextarea('experience_3');
		$form -> addText('time');

		$form -> addSubmit('submitEvening', 'submit');

		$form -> onSuccess[] = callback($this, 'saveEveningForm');

		return $form;
	}

	public function saveEveningForm(Form $form)
	{
		$values = $form -> getValues();

		if ( $values['time_adjusted'] ){
			$store_time = $values['time'];
		} else {
			$store_time = new DateTime();
		}

		$experiences = array(
			$values['experience_1'], $values['experience_2'], $values['experience_3'],
		);

		$last = $this-> dayManager -> findAllDaysForUser($this->user->id)->order('date DESC')->limit(1)->fetch();
		$store_time = $last->date;
		
		
		# evaluate the day, we should always have the beginning
		# $this -> dayManager->startDay(1, $values['mood'])
		$this -> dayManager -> evaluateDay($this->user->id, $store_time, $experiences);
		$this -> dayManager -> endDay($this->user->id, $store_time);

		$this -> redirect('evening:sleeptime');
	}

}
