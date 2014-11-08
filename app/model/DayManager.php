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
	 * @param ExperienceRepository $experienceRepository
	 */
	public function __construct(DayRepository $repository, ExperienceRepository $experienceRepository)
	{
		$this->repository = $repository;
		$this->experienceRepository = $experienceRepository;
	}


	public function findAllForUser($userId)
	{
		return $this->repository->findAll()->where('user_id', $userId);
	}

	/**
	*
	* @param int $userId
	* @param DateTime $day
	*/
	private function getUserDayId($userId, $day)
	{
		$where = array(
				'user_id' => $userId,
				'date'    => $day->format('Y-m-d')
			);

		$ret = $this->repository->table()->where($where)->fetch()->toArray();
		return $ret['id'];
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
	 */
	public function startDay($userId, $timeStamp, $mood) {

		$insert = array(
				'user_id'    => $userId,
				'date'       => $timeStamp->format('Y-m-d'),
				'start_time' => $timeStamp->format('Y-m-d H:i:s'),
				'mood'       => $mood
			);

		$this->repository->table()->insert($insert);
		# TODO: handle uniq key constraints

	}
	
	/**
	 *
	 * @param int $userId
	 * @param DateTime $day
	 * @param array $notes
	 * @return boolean
	 */
	public function evaluateDay($userId, $day, array $notes) {

		$now = new DateTime;

		# update date
		$where = array(
				'user_id' => $userId,
				'date'    => $day->format('Y-m-d')
			);

		$update = array(
				'experience_time' => $now->format('Y-m-d H:i:s'),
			);

		$this->repository->table()->where($where)->update($update);

		# add experiences - at first get `day_id`
		$day_id = $this->getUserDayId(1, $now);

		foreach ($notes as $note) {
			$insert = array(
					'day_id'      => $day_id,
					'description' => $note,
					'category_id' => NULL
				);

			$this->experienceRepository->table()->insert($insert);
		}
	}
	
	/*
	 * @param int $userId
	 * @param DateTime $timeStamp
	 * @return boolean
	 */
	public function endDay($userId, $day) {
		
		$where = array(
				'user_id' => $userId,
				'date'    => $day->format('Y-m-d')
			);

		$update = array(
				'end_time' => $day->format('Y-m-d H:i:s')
			);

		$this->repository->table()->where($where)->update($update);

	}

}
