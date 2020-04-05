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
		'table_count'				=> 4,
		'table_day_count'			=> 28,
		'days_offset_menu'			=> 0,
		'days_offset_tag'			=> 7,
		'days_offset_link'			=> 0,
		'min_row_count'				=> 5,
		'max_row_count'				=> 30,
		'show_today'				=> true,
		'header_en'					=> true,
		'header'				=> [
			'month',
			'monthday',
			'weekday',
			'isoweek',
			'moonphase',
		],
		'repeated_header_en'		=> false,
		'repeated_header_num_rows'	=> 15,
		'repeated_header_omit_rows'	=> 10,
		'repeated_header'			=> [
			'monthday',
			'moonphase',
		],
		'footer_en'					=> true,
		'footer'					=> [
			'monthday',
			'moonphase',
			'month',
		],
		'weekday_max_char_count'	=> 3,
		'topic_hilit'				=> false,
		'load_stylesheet'			=> true,
		'extra_stylesheet'			=> '',
		'derive_user_time_format'	=> true,
		'default_time_format'		=> 'H:i',
	];

	const HEADER_ROWS = [
		'month'		=> [
			'name'			=> 'month',
			'blocks_merge'	=> true,
		],
		'monthday'	=> [
			'name'			=> 'monthday',
		],
		'weekday'	=> [
			'name'			=> 'weekday',
		],
		'moonphase'	=> [
			'name'			=> 'moonphase',
			'will_merge'	=> true,
		],
		'isoweek'	=> [
			'name'			=> 'isoweek',
			'will_merge'	=> true,
		],
		'blank_1'	=> [
			'name'			=> 'blank',
		],
		'blank_2'	=> [
			'name'			=> 'blank',
		],
		'blank_3'	=> [
			'name'			=> 'blank',
		],
		'blank_4'	=> [
			'name'			=> 'blank',
		],
		'blank_5'	=> [
			'name'			=> 'blank',
		],
	];

	const INIT_HEADER_TPL = [
		'S_MONTH'			=> false,
		'S_MONTHDAY'		=> false,
		'S_WEEKDAY'			=> false,
		'S_BLANK'			=> false,
		'S_ISOWEEK'			=> false,
		'S_ISOWEEK_FIRST'	=> false,
		'S_MOONPHASE'		=> false,
		'S_MOONPHASE_FIRST'	=> false,
	];

	const MONTH_NAME = [
		1	=> 'January',
		2	=> 'February',
		3	=> 'March',
		4	=> 'April',
		5	=> 'May',
		6	=> 'June',
		7	=> 'July',
		8	=> 'August',
		9	=> 'September',
		10	=> 'October',
		11	=> 'November',
		12	=> 'December',
	];

	const MONTH_CLASS = [
		1	=> 'jan',
		2	=> 'feb',
		3	=> 'mar',
		4	=> 'apr',
		5	=> 'may',
		6	=> 'jun',
		7	=> 'jul',
		8	=> 'aug',
		9	=> 'sep',
		10	=> 'oct',
		11	=> 'nov',
		12	=> 'dec',
	];
}
