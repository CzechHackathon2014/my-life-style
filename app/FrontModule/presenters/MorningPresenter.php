<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin@surovcak.cz>
 */

namespace app\FrontModule\presenters;

use	Nette\Application\UI\Form,
    Nette\Utils\DateTime;

class MorningPresenter extends BasePresenter
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

		$form = new Form();

		$form -> addHidden('time_adjusted');
		$form -> addSelect('mood', 'Mood', $moods)->addCondition(Form::IS_IN, array(0,1,2));
		$form -> addText('time');

		$form -> addSubmit('submitMorning', 'submit');

		$form -> onSuccess[] = callback($this, 'saveMorningForm');

		return $form;
	}

	public function saveMorningForm(Form $form)
	{
		$values = $form -> getValues();

		# TODO: detect value for time
		$time = new DateTime;

		# do funny stuff and store to database

		# we should be storing mood also
		$this -> dayManager->startDay(1, $time, '1');

		$this -> redirect('morning:howdy');
	}

}
