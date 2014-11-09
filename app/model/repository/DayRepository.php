<?php
/**
 * @author Honza Cerny (http://honzacerny.com)
 */

namespace App\Model;

use Aprila\Model\BaseRepository;
use Nette\Utils\Strings;

class DayRepository extends BaseRepository
{
	/** @var string */
	public $table = 'day';


	/**
	 *
	 * @param int $userId
	 * @return int
	 */
	public function getCountDaysForUser($userId = 0)
	{
		return (int) $this->table()->where('user_id = ?', $userId)->count();
	}

}