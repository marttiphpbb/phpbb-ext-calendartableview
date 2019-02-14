<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\service;

use phpbb\config\db_text as config_text;
use phpbb\cache\driver\driver_interface as cache;
use marttiphpbb\calendarmonthview\util\cnst;

class store
{
	protected $config_text;
	protected $cache;
	protected $local_cache;
	protected $transaction = false;

	public function __construct(
		config_text $config_text,
		cache $cache
	)
	{
		$this->config_text = $config_text;
		$this->cache = $cache;
	}

	private function get_all():array
	{
		if (isset($this->local_cache) && is_array($this->local_cache))
		{
			return $this->local_cache;
		}

		$settings = $this->cache->get(cnst::CACHE_ID);

		if ($settings)
		{
			$this->local_cache = $settings;
			return $settings;
		}

		$this->local_cache = unserialize($this->config_text->get(cnst::ID));
		$this->cache->put(cnst::CACHE_ID, $this->local_cache);

		return $this->local_cache;
	}

	private function set(array $ary):void
	{
		if ($ary === $this->local_cache)
		{
			return;
		}
		$this->local_cache = $ary;

		if (!$this->transaction)
		{
			$this->write($ary);
		}
	}

	private function write(array $ary):void
	{
		$this->cache->put(cnst::CACHE_ID, $ary);
		$this->config_text->set(cnst::ID, serialize($ary));
	}

	public function transaction_start():void
	{
		$this->transaction = true;
	}

	public function transaction_end():void
	{
		$this->transaction = false;
		$this->write($this->local_cache);
	}

	private function get_array(string $name):array
	{
		return $this->get_all()[$name];
	}

	private function set_array(string $name, array $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function set_string(string $name, string $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function get_string(string $name):string
	{
		return $this->get_all()[$name];
	}

	private function set_int(string $name, int $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function get_int(string $name):int
	{
		return $this->get_all()[$name];
	}

	private function set_boolean(string $name, bool $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function get_boolean(string $name):bool
	{
		return $this->get_all()[$name];
	}

	public function set_show_today(bool $show_today):void
	{
		$this->set_boolean('show_today', $show_today);
	}

	public function get_show_today():bool
	{
		return $this->get_boolean('show_today');
	}

	public function set_show_isoweek(bool $show_isoweek):void
	{
		$this->set_boolean('show_isoweek', $show_isoweek);
	}

	public function get_show_isoweek():bool
	{
		return $this->get_boolean('show_isoweek');
	}

	public function set_show_moon_phase(bool $show_moon_phase):void
	{
		$this->set_boolean('show_moon_phase', $show_moon_phase);
	}

	public function get_show_moon_phase():bool
	{
		return $this->get_boolean('show_moon_phase');
	}

	public function set_topic_hilit(bool $topic_hilit):void
	{
		$this->set_boolean('topic_hilit', $topic_hilit);
	}

	public function get_topic_hilit():bool
	{
		return $this->get_boolean('topic_hilit');
	}

	public function set_first_weekday(int $first_weekday):void
	{
		$this->set_int('first_weekday', $first_weekday);
	}

	public function get_first_weekday():int
	{
		return $this->get_int('first_weekday');
	}

	public function set_min_rows(int $min_rows):void
	{
		$this->set_int('min_rows', $min_rows);
	}

	public function get_min_rows():int
	{
		return $this->get_int('min_rows');
	}

	public function set_max_rows(int $max_rows):void
	{
		$this->set_int('max_rows', $max_rows);
	}

	public function get_max_rows():int
	{
		return $this->get_int('max_rows');
	}

	public function set_pag_neighbours(int $pag_neighbours):void
	{
		$this->set_int('pag_neighbours', $pag_neighbours);
	}

	public function get_pag_neighbours():int
	{
		return $this->get_int('pag_neighbours');
	}

	public function set_pag_show_prev_next(bool $pag_show_prev_next):void
	{
		$this->set_boolean('pag_show_prev_next', $pag_show_prev_next);
	}

	public function get_pag_show_prev_next():bool
	{
		return $this->get_boolean('pag_show_prev_next');
	}

	public function set_load_stylesheet(bool $load_stylesheet):void
	{
		$this->set_boolean('load_stylesheet', $load_stylesheet);
	}

	public function get_load_stylesheet():bool
	{
		return $this->get_boolean('load_stylesheet');
	}

	public function set_extra_stylesheet(string $extra_stylesheet):void
	{
		$this->set_string('extra_stylesheet', $extra_stylesheet);
	}

	public function get_extra_stylesheet():string
	{
		return $this->get_string('extra_stylesheet');
	}

	public function set_derive_user_time_format(bool $derive_user_time_format):void
	{
		$this->set_boolean('derive_user_time_format', $derive_user_time_format);
	}

	public function get_derive_user_time_format():bool
	{
		return $this->get_boolean('derive_user_time_format');
	}

	public function set_default_time_format(string $default_time_format):void
	{
		$this->set_string('default_time_format', $default_time_format);
	}

	public function get_default_time_format():string
	{
		return $this->get_string('default_time_format');
	}
}
