<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
	/**
	 * @var \App\Model\ReferenceManager @inject
	 */
	public $referenceManager;


	public function renderDefault()
	{
		$this->template->logos = $this->referenceManager->findRandomLogos();
	}

}
