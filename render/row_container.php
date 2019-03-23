<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\render;

use marttiphpbb\calendartableview\value\calendar_event;
use marttiphpbb\calendartableview\render\calendar_event_row;

class row_container
{
	const MIN_DAY = 1000000;
	protected $rows = [];
	protected $end_ary = [];
	protected $event_ref_ary = [];
	protected $end_refs = [];
	protected $event_locs = [];
	protected $event_locs_en = [];

	public function __construct(int $min_rows, int $max_rows)
	{
		for ($row_index = 0; $row_index < $min_rows; $row_index++)
		{
			$this->get_or_create_row($row_index);
		}

		$this->max_rows = $max_rows;
	}

	protected function get_or_create_row(int $row_index):calendar_event_row
	{
		if (!$this->rows[$row_index])
		{
			$this->rows[$row_index] = new calendar_event_row();
		}

		return $this->rows[$row_index];
	}

	public function add_calendar_event(calendar_event $calendar_event):void
	{
		for($row_index = 0; $row_index < $this->max_rows; $row_index++)
		{
			$row = $this->get_or_create_row($row_index);

			if ($row->insert_calendar_event($calendar_event))
			{
				return;
			}
		}
	}

	private function ekey(int $row_index, int $jd):string
	{
		$row_index = max(self::MIN_DAY, $row_index);
		return (string) $row_index . '_' . (string) $jd;
	}

	private function rkey(int $jd):string
	{
		return '_' . (string) $jd;
	}

	private function ev_loc_insert(int $jd):void
	{
		$this->event_locs[] = $jd;
		$this->event_locs_en[$jd] = true;
	}

	private function get_prev_ev_loc(int $jd):int
	{
		if (isset($this->event_locs_en[$jd]))
		{
			return $jd;
		}

		$end = end($this->event_locs);

		if ($jd > $end)
		{
			$this->ev_loc_insert($jd);
			return $end;
		}

		$this->ev_loc_insert($jd);
		sort($this->event_locs);
		$flip = array_flip($this->event_locs);
		$index = $flip[$jd];

		return $this->event_locs[$index - 1];
	}

	private function get_ev_locs_from_to_before(int $start_jd, int $next_jd):array
	{
		$out = [];
		$flip = array_flip($this->event_locs);
		$index = $flip[$start_jd];
		$jd = $start_jd;

		while ($jd < $next_jd)
		{
			$out[$index] = $jd = $this->event_locs[$index];
			$index++;
		}

		return $out;
	}

	public function calc_ref():void
	{
		$row_count = $this->get_row_count();
		$end_row = $row_count - 1;
		$row_def_ary = [];

		$ekey = $this->ekey(0, 0);
		$this->ev_loc_insert(self::MIN_DAY);
		$this->end_ary[$ekey] = $end_row;
		$this->end_refs[self::MIN_DAY] = $ekey;

		for ($row_index = 0; $row_index < $row_count; $row_index++)
		{
			$events = $this->rows[$row_index]->get_events();

			foreach ($events as $event_index => $event)
			{
				$start_jd = $event->get_start_jd();
				$prev_jd = $this->get_prev_ev_loc($start_jd);

				if ($prev_jd !== $start_jd)
				{
					$prev_ekey = $this->end_refs[$prev_jd];
					$prev_end_row = $this->end_ary[$prev_ekey];

					if ($prev_end_row !== $row_index - 1)
					{
						[$prev_row] = explode('_', $prev_ekey);
						$ekey = $this->ekey($prev_row, $start_jd);
						$this->end_ary[$ekey] = $row_index - 1;
					}
				}

				$ekey = $this->ekey($row_index, $start_jd);
				$this->end_ary[$ekey] = $row_index;
				$this->event_ref_ary[$ekey] = $event;

				if ($end_row !== $row_index)
				{
					$next_row_index = $row_index + 1;
					$ekey = $this->ekey((int) $next_row_index, $start_jd);
					$this->end_ary[$ekey] = $end_row;
					$this->end_refs[$start_jd] = $ekey;
				}

				$next_jd = $event->get_first_jd_after();
				$from_to_before_ary = $this->get_from_to_before($start_jd, $next_jd);

				$rkey = $this->rkey($event->get_start_jd());

				if (isset($row_def_ary[$rkey]))
				{
					$ekey = $row_def_ary[$rkey];
					if ($this->end_ary[$ekey] === $row_index - 1)
					{

					}
				}

				$ekey = $this->ekey($row_index, $event->get_start_jd());
				$row_def_ary[$rkey] = $ekey;
				$this->end_ary[$ekey] = $row_index;
				$this->event_ref_ary[$ekey] = $event;
				$ekey = $this->ekey($row_index, $event->get_first_jd_after());
				$this->end_ary[$ekey] = $end_row;

				if ($row_index)
				{
					continue;
				}



			}
		}
	}

	public function get_row_count():int
	{
		return count($this->rows);
	}

	public function get_rows():array
	{
		return $this->rows;
	}

	public function get_row(int $row_index):calendar_event_row
	{
		return $this->rows[$row_index];
	}
}
