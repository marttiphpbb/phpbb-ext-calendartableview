<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\value;

abstract class span
{
	protected $start;
	protected $end;

	public function __construct(int $start, int $end)
	{
		$this->start = $start;
		$this->end = $end;
	}

	public function fits_in(span $span):bool
	{
		return $this->start >= $span->get_start()
			&& $this->end <= $span->get_end();
	}

	public function contains(span $span):bool
	{
		return $this->start <= $span->get_start()
			&& $this->end >= $span->get_end();
	}

	public function contains_index(int $index):bool
	{
		return $this->start <= $index && $this->end >= $index;
	}

	public function overlaps(span $span):bool
	{
		return $this->start <= $span->get_end()
			&& $this->end >= $span->get_start();
	}

	public function touches(span $span):bool
	{
		if ($this->get_first_before() === $span->get_end())
		{
			return true;
		}

		if ($this->get_first_after() === $span->get_start())
		{
			return true;
		}

		if ($this->overlaps($span))
		{
			return true;
		}

		return false;
	}

	public function fits_after_start(span $span):bool
	{
		return $this->start <= $span->get_start();
	}

	public function fits_before_end(span $span):bool
	{
		return $this->end >= $span->get_end();
	}

	public function starts_before(span $span):bool
	{
		return $this->start < $span->get_start();
	}

	public function ends_after(span $span):bool
	{
		return $this->end > $span->get_end();
	}

	public function is_after(span $span):bool
	{
		return $this->start > $span->get_end();
	}

	public function is_before(span $span):bool
	{
		return $this->end < $span->get_start();
	}

	public function has_same_start(span $span):bool
	{
		return $span->get_start() === $this->start;
	}

	public function has_same_end(span $span):bool
	{
		return $span->get_end() === $this->end;
	}

	public function get_duration():int
	{
		return ($this->end - $this->start + 1);
	}

	public function compare_start_with(span $span):int
	{
		return $this->start <=> $span->get_start();
	}

	public function compare_end_with(span $span):int
	{
		return $this->end <=> $span->get_end();
	}

	public function get_start():int
	{
		return $this->start;
	}

	public function get_end():int
	{
		return $this->end;
	}

	public function get_first_after():int
	{
		return $this->end + 1;
	}

	public function get_first_before():int
	{
		return $this->start - 1;
	}

	public function create_with_start(int $start):span
	{
		return new span($start, $this->end);
	}

	public function create_with_end(int $end):span
	{
		return new span($this->start, $end);
	}

	public function create_from_overlap(span $span):?span
	{
		$start = max($this->start, $span->get_start());
		$end = min($this->end, $span->get_end());

		if ($start <= $end)
		{
			return new span($start, $end);
		}

		return null;
	}

	public function get_overlap_count(span $span):int
	{
		return max(0, (min($this->end, $span->get_end()) - max($this->start, $span->get_start()) + 1));
	}
}
