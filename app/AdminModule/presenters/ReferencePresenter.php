<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\AdminModule\Presenters;

use Aprila\BaseFormRenderer,
	Nette\Application\ForbiddenRequestException,
	Nette\Application\UI\Form,
	Nette\Utils\Strings,
	Nette\Utils\Paginator;

class ReferencePresenter extends \Aprila\BaseSecurePresenter
{

	/**
	 * @var \App\Model\ReferenceManager @inject
	 */
	public $referenceManager;


	/** @var \Nette\Database\Table\IRow */
	public $reference;

	/**
	 * @var string
	 * @persistent
	 */
	public $q = '';


	/** @var string */
	public $fulltextQuery = '';


	public function startup()
	{
		parent::startup();
		if (!$this->user->isAllowed('References', 'view')) {
			throw new ForbiddenRequestException('Access denied');
			$this->redirect('Dashboard:default');
		}
	}


	public function renderDefault($page = 1)
	{
//		$paginator = new Paginator();
//		$paginator->setItemsPerPage(3);
//		$paginator->setPage($page);
//		$count = $this->referenceManager->getCount();
//		$paginator->setItemCount($count);

		$empty = FALSE;
		if ($this->fulltextQuery != '') {
			$this->template->listOfReferences = $this->referenceManager->findFulltext($this->fulltextQuery);
		} else {
			$this->template->listOfReferences = $this->referenceManager->findAll(); // ->limit($paginator->getLength(), $paginator->getOffset());
			if (empty($this->template->listOfReferences)) {
				$empty = TRUE;
			}
		}
		$this->template->fulltextQuery = $this->fulltextQuery;
		$this->template->empty = $empty;

		$this->template->page = $page;
//		if ($count > 10){
//			$this->template->showMoreButton = TRUE;
//		}

		if ($this->ajax){
			$this->redrawControl('datalistResult');
		}
	}


	public function renderAdd()
	{

	}


	/**
	 * @param int $id
	 * @throws ForbiddenRequestException
	 */
	public function actionEdit($id)
	{
		$this->reference = $this->referenceManager->get($id);

		if (!$this->reference) {
			throw new ForbiddenRequestException('Access denied');
			$this->redirect('Dashboard:default');
		}
	}


	public function renderEdit()
	{
		$this->template->reference = $this->reference;
	}


	/**
	 * @param int $id
	 * @throws ForbiddenRequestException
	 */
	public function actionDelete($id)
	{
		if (!$this->user->isAllowed('References', 'edit')) {
			throw new ForbiddenRequestException('Access denied');
		}

		$this->reference = $this->referenceManager->get($id);
		if (!$this->reference) {
			throw new ForbiddenRequestException('Access denied');
			$this->redirect('Dashboard:default');
		}
		$this->referenceManager->deleteReference($id);
		$this->flashMessage('Reference was removed');
		$this->redirect('default');
	}


	/**
	 * @param int $id
	 */
	public function handleDelete($id)
	{
		if ($this->user->isAllowed('References', 'edit')) {
			$this->reference = $this->referenceManager->get($id);
			if ($this->reference) {
				$this->referenceManager->deleteReference($id);
				$this->flashMessage('Reference was removed');
				$this->redrawControl('flashes');
//				$this->redrawControl('datalist');
			}
		}

		if (!$this->isAjax()) {
			$this->redirect('this');
		}
	}


	/**
	 * Reference form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentReferenceForm()
	{
		$form = BaseFormRenderer::factory();
		$form->addCheckbox('onlyLogo', 'is it only logo');
		$form->addText('name', 'Who:')
			->addConditionOn($form['onlyLogo'], Form::NOT_EQUAL, TRUE)
			->setRequired('Please enter name.');

		$form->addText('company', 'Company:')
			->setRequired('Please enter company.');

		$form->addTextArea('content', 'Reference:')
			->addConditionOn($form['onlyLogo'], Form::NOT_EQUAL, TRUE)
			->setRequired('Please enter reference.');

		$stars = array('0' => '-- without stars --',
			'5' => '★★★★★',
			'4' => '★★★★',
			'3' => '★★★');
		$form->addSelect('stars', 'Stars:', $stars);

		$image = $form->addUpload('image', 'Image:');

		if (!$this->reference) {
			// add condition for adding reference and image
			$image->addConditionOn($form['onlyLogo'], Form::EQUAL, TRUE)
				->setRequired('Please select image.');
		}

		$image->addCondition(Form::FILLED)
			->addRule(Form::IMAGE, 'Please select image file');
//			->addRule(Form::MIME_TYPE, 'Please select jpeg image file', 'image/jpeg')
//			->addRule(Form::MAX_FILE_SIZE, 'Max allowed file size is 2 MB.', '20000');


		if ($this->reference) {
			$form->addHidden('id');
			$form->setDefaults($this->reference);
		}

		$form->addSubmit('send', $this->reference ? 'Save reference' : 'Add reference');
		$form->onSuccess[] = $this->referenceFormSucceeded;

		return $form;
	}


	/**
	 * @param Form $form
	 * @throws ForbiddenRequestException
	 */
	public function referenceFormSucceeded(Form $form)
	{
		if (!$this->user->isAllowed('References', 'edit')) {
			throw new ForbiddenRequestException('Access denied');
		}

		$v = $form->getValues();

		if (isset($v->id) && $v->id > 0) {
			$this->referenceManager->editReference(
				$v->id,
				$v->onlyLogo,
				$v->name,
				$v->company,
				$v->content,
				$v->stars,
				$v->image);

			$this->flashMessage('Reference was updated');
			$this->redirect('edit', $v->id);

		} else {
			$reference = $this->referenceManager
				->addReference(
					$v->onlyLogo,
					$v->name,
					$v->company,
					$v->content,
					$v->stars,
					$v->image);

			$this->flashMessage('Reference was added');
			$this->redirect('edit', $reference->id);
		}
	}


	/**
	 * Quick Search form factory.
	 *
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentQuickSearchForm()
	{
		$form = BaseFormRenderer::factory();
		$form->setMethod('GET');
		$form->addText('q', 'Query:');

		$form->addSubmit('search', 'Search');
		$form->onSuccess[] = $this->quickSearchFormSucceeded;

		return $form;
	}


	/**
	 * @param Form $form
	 */
	public function quickSearchFormSucceeded(Form $form)
	{
		$v = $form->getValues();
		if (Strings::length($v->q) >= 3) {
			$this->fulltextQuery = $v->q;
		}
		if ($this->isAjax()) {
			$this->redrawControl('datalist');
		}
	}
}