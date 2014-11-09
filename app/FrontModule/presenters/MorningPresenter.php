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

		$form -> addHidden('time_adjusted');
		$form -> addSelect('mood', 'Moje nálada po ránu', $moods)->setDefaultValue(2)->addCondition(Form::IS_IN, array(0,1,2));
		$form -> addText('time', 'Vstal jsem v')->setDefaultValue($now->format('H:i'));

		$form -> addSubmit('submitMorning', 'submit');

		$form -> onSuccess[] = callback($this, 'saveMorningForm');

		return $form;
	}

	public function saveMorningForm(Form $form)
	{
		$values = $form -> getValues();

		# detect value for time
		$today = new DateTime;
		$time = new DateTime;
		if ($values['time_adjusted']){
			$time = $time->from($today->format('Y-m-d ').$values['time']);
		};

		# do funny stuff and store to database

		# we should be storing mood also
		$this -> dayManager->startDay($this->user->id, $time, $values['mood']);

		$this -> redirect('morning:howdy');
	}

}
