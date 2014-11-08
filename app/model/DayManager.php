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
	
	/**
	 * 
	 * @param int $userId
	 * @param int $timeStamp
	 * @param string $mood
	 * @return boolean
	 */
	public function startDay($userId, $timeStamp, $mood) {
		return true;
	}
	
	/**
	 *
	 * @param int $userId        	
	 * @param array $notes
	 * @return boolean        	
	 */
	public function evaluateDay($userId, array $notes) {
		return true;
	}
	
	/*
	 * @param int $userId
	 * @param int $timeStamp
	 * @return boolean
	 */
	public function endDay($userId, $timeStamp) {
		return true;
	}
}