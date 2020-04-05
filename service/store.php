<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\service;

use phpbb\config\db_text as config_text;
use phpbb\cache\driver\driver_interface as cache;
use marttiphpbb\calendartableview\util\cnst;

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

	public function set_header_en(bool $header_en):void
	{
		$this->set_boolean('header_en', $header_en);
	}

	public function get_header_en():bool
	{
		return $this->get_boolean('header_en');
	}

	public function set_header(array $header):void
	{
		$this->set_array('header', $header);
	}

	public function get_header():array
	{
		return $this->get_array('header');
	}

	public function set_repeated_header_en(bool $repeated_header_en):void
	{
		$this->set_boolean('repeated_header_en', $repeated_header_en);
	}

	public function get_repeated_header_en():bool
	{
		return $this->get_boolean('repeated_header_en');
	}

	public function set_repeated_header_num_rows(int $repeated_header_num_rows):void
	{
		$this->set_int('repeated_header_num_rows', $repeated_header_num_rows);
	}

	public function get_repeated_header_num_rows():int
	{
		return $this->get_int('repeated_header_num_rows');
	}

	public function set_repeated_header_omit_rows(int $repeated_header_num_rows):void
	{
		$this->set_int('repeated_header_omit_rows', $repeated_header_num_rows);
	}

	public function get_repeated_header_omit_rows():int
	{
		return $this->get_int('repeated_header_omit_rows');
	}

	public function set_repeated_header(array $repeated_header):void
	{
		$this->set_array('repeated_header', $repeated_header);
	}

	public function get_repeated_header():array
	{
		return $this->get_array('repeated_header');
	}

	public function set_footer_en(bool $footer_en):void
	{
		$this->set_boolean('footer_en', $footer_en);
	}

	public function get_footer_en():bool
	{
		return $this->get_boolean('footer_en');
	}

	public function set_footer(array $footer):void
	{
		$this->set_array('footer', $footer);
	}

	public function get_footer():array
	{
		return $this->get_array('footer');
	}

	public function set_topic_hilit(bool $topic_hilit):void
	{
		$this->set_boolean('topic_hilit', $topic_hilit);
	}

	public function get_topic_hilit():bool
	{
		return $this->get_boolean('topic_hilit');
	}

	public function set_table_count(int $table_count):void
	{
		$this->set_int('table_count', $table_count);
	}

	public function get_table_count():int
	{
		return $this->get_int('table_count');
	}

	public function set_table_day_count(int $table_day_count):void
	{
		$this->set_int('table_day_count', $table_day_count);
	}

	public function get_table_day_count():int
	{
		return $this->get_int('table_day_count');
	}

	public function set_days_offset_menu(int $days_offset_menu):void
	{
		$this->set_int('days_offset_menu', $days_offset_menu);
	}

	public function get_days_offset_menu():int
	{
		return $this->get_int('days_offset_menu');
	}

	public function set_days_offset_tag(int $days_offset_tag):void
	{
		$this->set_int('days_offset_tag', $days_offset_tag);
	}

	public function get_days_offset_tag():int
	{
		return $this->get_int('days_offset_tag');
	}

	public function set_days_offset_link(int $days_offset_link):void
	{
		$this->set_int('days_offset_link', $days_offset_link);
	}

	public function get_days_offset_link():int
	{
		return $this->get_int('days_offset_link');
	}

	public function set_min_row_count(int $min_row_count):void
	{
		$this->set_int('min_row_count', $min_row_count);
	}

	public function get_min_row_count():int
	{
		return $this->get_int('min_row_count');
	}

	public function set_max_row_count(int $max_row_count):void
	{
		$this->set_int('max_row_count', $max_row_count);
	}

	public function get_max_row_count():int
	{
		return $this->get_int('max_row_count');
	}

	public function set_weekday_max_char_count(int $weekday_max_char_count):void
	{
		$this->set_int('weekday_max_char_count', $weekday_max_char_count);
	}

	public function get_weekday_max_char_count():int
	{
		return $this->get_int('weekday_max_char_count');
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
