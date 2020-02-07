<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\service;

use phpbb\user;

class user_today
{
	protected $user;

	public function __construct(
		user $user
	)
	{
		$this->user = $user;
	}

	public function get_date():array
	{
		$now = $this->user->create_datetime();
		$time_offset = $now->getOffset();
		return phpbb_gmgetdate($now->getTimestamp() + $time_offset);
	}

	public function get_date_with_day_offset(int $day_offset):array
	{
		$now = $this->user->create_datetime();
		$time_offset = $now->getOffset();
		return phpbb_gmgetdate($now->getTimestamp() + $time_offset + ($day_offset * 86400));
	}

	public function get_jd():int
	{
		$now = $this->get_date();
		return cal_to_jd(CAL_GREGORIAN, $now['mon'], $now['mday'], $now['year']);
	}
}
