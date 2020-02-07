<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\util;

class cnst
{
	const FOLDER = 'marttiphpbb/calendartableview';
	const ID = 'marttiphpbb_calendartableview';
	const PREFIX = self::ID . '_';
	const CACHE_ID = '_' . self::ID;
	const L = 'MARTTIPHPBB_CALENDARTABLEVIEW';
	const L_ACP = 'ACP_' . self::L;
	const L_MCP = 'MCP_' . self::L;
	const TPL = '@' . self::ID . '/';
	const EXT_PATH = 'ext/' . self::FOLDER . '/';

	const MOON_ICON = [
		0 	=> 'fa-circle',
		1	=> 'fa-adjust fa-rotate-180',
		2	=> 'fa-circle-o',
		3	=> 'fa-adjust',
	];

	const MOON_LANG = [
		0	=> 'NEW_MOON',
		1	=> 'FIRST_QUARTER_MOON',
		2	=> 'FULL_MOON',
		3	=> 'THIRD_QUARTER_MOON',
	];

	const DEFAULT_SETTINGS = [
		'num_tables'				=> 4,
		'num_days_one_table'		=> 14,
		'num_days_offset_menu'		=> 0,
		'num_days_offset_tag'		=> 7,
		'num_days_offset_link'		=> 0,
		'min_rows'					=> 5,
		'max_rows'					=> 30,
		'show_today'				=> true,
		'show_isoweek'				=> false,
		'show_moon_phase'			=> false,
		'topic_hilit'				=> false,
		'load_stylesheet'			=> true,
		'extra_stylesheet'			=> '',
		'derive_user_time_format'	=> true,
		'default_time_format'		=> 'H:i',
	];
}
