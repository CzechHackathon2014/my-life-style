<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Nette;

class DayManager extends Nette\Object
{

	/**
	 * @var DayRepository
	 */
	protected $repository;


	/**
	 * @param DayRepository $repository
	 */
	public function __construct(DayRepository $repository)
	{
		$this->repository = $repository;
	}


	public function findAllForUser($userId)
	{
		return $this->repository->findAll()->where('user_id', $userId);
	}


	public function startDay($userId)
	{

	}


	public function evaluateDay($userId)
	{

	}


	public function endDay($userId)
	{

	}
}