<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use	Nette\Application\UI\Form,
    Nette\Utils\DateTime;

class MorningPresenter extends DiaryPresenter
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

		$this -> template -> next_action = array('label' => 'Začít nový den', 'action' => 'Morning:default');
		
		# deside if we wan't to display new day form or redirect to any other
		# presenter
		# now we're providing set of links instead of "magic"

	}

	public function renderHowdy()
	{
		# Send unauth users away - we should do so in parent Presenter
		if ( $this->user->isLoggedIn() !== true ){
			$this->redirect('homepage:default');
		}
	}

	public function createComponentMorningForm()
	{

		$moods = array(0 => ':(', 1 => ':|', 2 => ':)');
		$now   = new DateTime;

		$form = new Form();

		$form -> addText('time', 'Vstal jsem v')->setDefaultValue($now->format('H:i'))->addRule(Form::PATTERN, 'Zadej čas (10, 10:00)', "^[0-9]{1,2}(:[0-9]{2})?$");
		$form -> addSubmit('submitMorning0', ':(');
		$form -> addSubmit('submitMorning1', ':|');
		$form -> addSubmit('submitMorning2', ':)');

		$form -> onSuccess[] = callback($this, 'saveMorningForm');

		return $form;
	}

	public function saveMorningForm(Form $form)
	{
		$values = $form -> getValues();
		if ($form['submitMorning0']->submittedBy) {
			$mood = 0;
		}
		if ($form['submitMorning1']->submittedBy) {
			$mood = 1;
		}
		if ($form['submitMorning2']->submittedBy) {
			$mood = 2;
		}

		# detect value for time
		$today = new DateTime;
		$time = new DateTime;

		$time = $time->from($today->format('Y-m-d ').(strpos($values['time'],':') ? ($values['time']) : ($values['time'].':00')));

		# do funny stuff and store to database

		# we should be storing mood also
		$this -> dayManager->startDay($this->user->id, $time, $mood);

		$this -> redirect('morning:howdy');
	}

}
