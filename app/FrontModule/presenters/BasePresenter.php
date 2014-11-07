<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\FrontModule\Presenters;

use Nette,
	App\Model;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	public function beforeRender()
	{
		parent::beforeRender();

		$this->template->production = !$this->context->parameters['site']['develMode'];
		$this->template->version = $this->context->parameters['site']['version'];
	}
}
