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
	 * @var ExperienceRepository
	 */
	protected $experienceRepository;


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
	
	public function findOneForUser($userId, $day)
	{
		return $this->repository->table()->wherePrimary($day);
	}
	
	/**
	 * 
	 * @param int $userId
	 * @param string $day
	 */
	protected function createNew($userId, $day)
	{
		return $this->repository->table()->insert(['day' => $day, 'user' => $userId]);
	}
	
	/**
	 * 
	 * @param int $userId
	 * @param int $timeStamp
	 * @param string $mood
	 * @return boolean
	 */
	public function startDay($userId, $timeStamp, $mood) {
		$day = date("m/d/y",$timeStamp);
		
		$this->createNew($userId,$day);
		
		$this->repository->update($day, ['morning' => $timeStamp]);
		
		return true;
	}
	
	/**
	 *
	 * @param int $userId        	
	 * @param array $notes
	 * @return boolean        	
	 */
	public function evaluateDay($userId, array $notes) {
		$day = date("m/d/y");
		
		$existingDay = $this->findOneForUser($userId, $day);
		if(!$existingDay) {
			$existingDay = $this->createNew($userId,$day);
		}
		
		//TODO inser notes into day
		
		return true;
	}
	
	/*
	 * @param int $userId
	 * @param int $timeStamp
	 * @return boolean
	 */
	public function endDay($userId, $timeStamp) {
		$day = date("m/d/y", $timeStamp);
		
		$existingDay = $this->findOneForUser($userId, $day);
		if(!$existingDay) {
			$existingDay = $this->createNew($userId,$day);
		}
		
		$this->repository->update($day, ['evening' => $timeStamp]);
		
		return true;
	}
}