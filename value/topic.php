<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\value;

class topic
{
	protected $topic_id;
	protected $forum_id;
	protected $topic_title;

	public function __construct(
		int $topic_id,
		int $forum_id,
		string $topic_title
	)
	{
		$this->topic_id = $topic_id;
		$this->forum_id = $forum_id;
		$this->topic_title = $topic_title;
	}

	public function get_topic_id():int
	{
		return $this->topic_id;
	}

	public function get_forum_id():int
	{
		return $this->forum_id;
	}

	public function get_topic_title():string
	{
		return $this->topic_title;
	}
}
