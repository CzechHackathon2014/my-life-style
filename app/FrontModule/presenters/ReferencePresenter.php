<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace app\FrontModule\presenters;

class ReferencePresenter extends BasePresenter
{

	/**
	 * @var \App\Model\ReferenceManager @inject
	 */
	public $referenceManager;


	public function renderDefault()
	{
		$this->template->listOfReferences = $this->referenceManager->findRandomWithImage();
	}

}