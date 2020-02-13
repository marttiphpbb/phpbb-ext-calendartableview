<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\value;

use marttiphpbb\calendartableview\value\topic;
use marttiphpbb\calendartableview\value\span;

class calendar_event extends span
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
