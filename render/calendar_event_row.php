<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\render;

use marttiphpbb\calendartableview\value\calendar_event;

class calendar_event_row
{
	protected $events = [];

	public function __construct()
	{
	}

	public function insert_calendar_event(calendar_event $calendar_event):bool
	{
		$index = 0;

		foreach ($this->events as $index => $event)
		{
			if ($event->overlaps($calendar_event))
			{
				return false;
			}
			if ($event->starts_after($calendar_event))
			{
				break;
			}
		}

		array_splice($this->events, $index, 0, $calendar_event);
		return true;
	}

	public function get():array
	{
		return $this->events;
	}
}
