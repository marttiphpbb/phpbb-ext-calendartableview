<?php

/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\service;

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

	public function get_jd():int
	{
		$now = $this->get_date();
		return cal_to_jd(CAL_GREGORIAN, $now['mon'], $now['mday'], $now['year']);
	}
}
