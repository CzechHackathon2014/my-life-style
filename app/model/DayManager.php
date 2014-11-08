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
		return $this->repository->table()->where('user_id', $userId)->where('date', $day);
	}
	
	/**
	 * 
	 * @param int $userId
	 * @param string $day
	 */
	protected function createNew($userId, $day)
	{
		return $this->repository->table()->insert(['user_id' => $userId, 'date' => $day]);
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
		
		$this->createNew($userId, $day);
		
		$endDateTime = new \DateTime();
		$id = 1;
		
		$this->repository->update($id, ['start_time' => $startDateTime->format('Y-m-d H:i:s'), 'mood' => $mood]);
		
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
			$existingDay = $this->createNew($userId, $day);
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
			$existingDay = $this->createNew($userId, $day);
		}
		
		$endDateTime = new \DateTime();
		$id = 1;
		
		$this->repository->update($id, ['end_time' => $endDateTime->format('Y-m-d H:i:s')]);
		
		return true;
	}
}
