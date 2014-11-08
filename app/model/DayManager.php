<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 * @author Martin Surovcak <martin@surovcak.cz>
 */

namespace App\Model;

use Nette,
	Nette\DateTime;

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
	 * @param DateTime $day
	 */
	protected function createNew($userId, $day)
	{
		return $this->repository->table()->
					insert(['user_id' => $userId, 'date' => $day->format('Y-m-d')]);
	}
	
	/**
	 * 
	 * @param int $userId
	 * @param DateTime $timeStamp
	 * @param int $mood
	 * @return boolean
	 */
	public function startDay($userId, $timeStamp, $mood) {
		$day = $timeStamp->format('Y-m-d');
		
		$dayId = $this->getDayIdAndCreateNew($userId, $day);
		
		$endDateTime = new DateTime();
		
		$this->repository->table()->where('id=?', $dayId)->update(['start_time' => $timeStamp->format('Y-m-d H:i:s'), 'mood' => $mood]);
		
		return true;
	}
	
	/**
	 *
	 * @param int $userId        	
	 * @param array $notes
	 * @return boolean        	
	 */
	public function evaluateDay($userId, array $notes) {
		$day = date("Y-m-d");
		
		$dayId = $this->getDayIdAndCreateNew($userId, $day);
		
		//TODO inser experience into day
		$experienceDateTime = new \DateTime();
		
		$this->repository->update($dayId, ['experience_time' => $experienceDateTime->format('Y-m-d H:i:s')]);
		
		return true;
	}
	
	/*
	 * @param int $userId
	 * @param int $timeStamp
	 * @return boolean
	 */
	public function endDay($userId, $timeStamp) {
		$day = date("Y-m-d", $timeStamp);
		
		$dayId = $this->getDayIdAndCreateNew($userId, $day);
		
		$endDateTime = new \DateTime();
		
		$this->repository->update($dayId, ['end_time' => $endDateTime->format('Y-m-d H:i:s')]);
		
		return true;
	}
	
	protected function getDayIdAndCreateNew($userId, $day) {
		$existingDay = $this->findOneForUser($userId, $day);
		if (!$existingDay) {
			$existingDay = $this->createNew($userId, $day);
		}
		
		$id = 1;
		return $id;
	}
}
