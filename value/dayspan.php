<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\value;

class dayspan
{
	protected $start_jd;
	protected $end_jd;

	public function __construct(int $start_jd, int $end_jd)
	{
		$this->start_jd = $start_jd;
		$this->end_jd = $end_jd;
	}

	public function fits_in(dayspan $dayspan):bool
	{
		return $this->start_jd >= $dayspan->get_start_jd() && $this->end_jd <= $dayspan->get_end_jd();
	}

	public function contains(dayspan $dayspan):bool
	{
		return $this->start_jd <= $dayspan->get_start_jd() && $this->end_jd >= $dayspan->get_end_jd();
	}

	public function contains_day(int $jd):bool
	{
		return $this->start_jd <= $jd && $this->end_jd >= $jd;
	}

	public function overlaps(dayspan $dayspan):bool
	{
		return $this->start_jd <= $dayspan->get_end_jd() && $this->end_jd >= $dayspan->get_start_jd();
	}

	public function touches(dayspan $dayspan):bool
	{
		if ($this->get_first_jd_before() === $dayspan->get_end_jd())
		{
			return true;
		}

		if ($this->get_first_jd_after() === $dayspan->get_start_jd())
		{
			return true;
		}

		if ($this->overlaps($dayspan))
		{
			return true;
		}

		return false;
	}

	public function fits_after_start(dayspan $dayspan):bool
	{
		return $this->start_jd <= $dayspan->get_start_jd();
	}

	public function fits_before_end(dayspan $dayspan):bool
	{
		return $this->end_jd >= $dayspan->get_end_jd();
	}

	public function starts_before(dayspan $dayspan):bool
	{
		return $this->start_jd < $dayspan->get_start_jd();
	}

	public function ends_after(dayspan $dayspan):bool
	{
		return $this->end_jd > $dayspan->get_end_jd();
	}

	public function is_after(dayspan $dayspan):bool
	{
		return $this->start_jd > $dayspan->get_end_jd();
	}

	public function is_before(dayspan $dayspan):bool
	{
		return $this->end_jd < $dayspan->get_start_jd();
	}

	public function has_same_start(dayspan $dayspan):bool
	{
		return $dayspan->get_start_jd() === $this->start_jd;
	}

	public function has_same_end(dayspan $dayspan):bool
	{
		return $dayspan->get_end_jd() === $this->end_jd;
	}

	public function get_duration():int
	{
		return $this->end_jd - $this->start_jd + 1;
	}

	public function compare_start_with(dayspan $dayspan):int
	{
		return $this->start_jd <=> $dayspan->get_start_jd();
	}

	public function compare_end_with(dayspan $dayspan):int
	{
		return $this->end_jd <=> $dayspan->get_end_jd();
	}

	public function get_start_jd():int
	{
		return $this->start_jd;
	}

	public function get_end_jd():int
	{
		return $this->end_jd;
	}

	public function get_first_jd_after():int
	{
		return $this->end_jd + 1;
	}

	public function get_first_jd_before():int
	{
		return $this->start_jd - 1;
	}

	public function create_with_start_jd(int $start_jd):dayspan
	{
		return new dayspan($start_jd, $this->end_jd);
	}

	public function create_with_end_jd(int $end_jd):dayspan
	{
		return new dayspan($this->start_jd, $end_jd);
	}

	public function get_overlap_day_count(dayspan $dayspan):int
	{
		return max(0, (min($this->end_jd, $dayspan->get_end_jd()) - max($this->start_jd, $dayspan->get_start_jd()) + 1));
	}
}
