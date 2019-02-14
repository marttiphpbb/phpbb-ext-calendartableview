<?php

/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\service;

use phpbb\user;
use marttiphpbb\calendarmonthview\service\store;

class user_time
{
	protected $user;
	protected $store;
	protected $format;

	public function __construct(
		user $user,
		store $store
	)
	{
		$this->user = $user;
		$this->store = $store;
	}

	public function get(int $time):string
	{
		if (!$this->format)
		{
			$this->find_format();
		}

		return $this->user->format_date($time, $this->format);
	}

	private function find_format()
	{
		if (!$this->store->get_derive_user_time_format())
		{
			$this->set_default_format();
			return;
		}

		$user_date_format = $this->user->date_format;

		$i_pos = strpos($user_date_format, 'i');

		if ($i_pos === false)
		{
			$this->set_default_format();
			return;
		}

		$a_pos = stripos($user_date_format, 'a');
		$g_pos = stripos($user_date_format, 'g');
		$h_pos = stripos($user_date_format, 'h');

		$x_pos = $g_pos === false ? $h_pos : $g_pos;

		if ($x_pos === false || $i_pos <= $x_pos)
		{
			$this->set_default_format();
			return;
		}

		$this->format = substr($user_date_format, $x_pos, $i_pos - $x_pos + 1);

		if ($a_pos !== false)
		{
			$this->format .= ' ';
			$this->format .= substr($user_date_format, $a_pos, 1);
		}
	}

	private function set_default_format()
	{
		$this->format = $this->store->get_default_time_format();
		return;
	}
}
