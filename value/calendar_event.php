<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\value;

use marttiphpbb\calendarmonthview\value\topic;
use marttiphpbb\calendarmonthview\value\dayspan;

class calendar_event extends dayspan
{
	protected $topic;

	public function __construct(
		int $start_jd,
		int $end_jd,
		topic $topic
	)
	{
		parent::__construct($start_jd, $end_jd);
		$this->topic = $topic;
	}

	public function get_topic():topic
	{
		return $this->topic;
	}
}
