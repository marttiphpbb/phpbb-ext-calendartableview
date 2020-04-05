<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_SETTINGS_SAVED'
	=> 'The settings have been saved successfully!',

// links
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MENUITEMS_NOT_ENABLED'
	=> 'The %1$sMenu Items extension%2$s should be installed
	for this functionality.',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_LINKS'
	=> 'Links',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_LINKS_EXPLAIN'
	=> 'Menu link locations to the Calendar Table View page',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DAYS_OFFSET_MENU'
	=> 'Days offset from today for menu links',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DAYS_OFFSET_TAG'
	=> 'Days offset from event for tag links',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DAYS_OFFSET_LINK'
	=> 'Days offset for inline view links',

// page_rendering
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_PAGE'
	=> 'Calendar page',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TABLES'
	=> 'Tables',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TABLE_COUNT'
	=> 'Number of tables',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TABLE_DAY_COUNT'
	=> 'Number of days for each table',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_EVENT_ROWS'
	=> 'Event rows',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MIN_ROW_COUNT'
	=> 'Minimum event rows',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MAX_ROW_COUNT'
	=> 'Maximum event rows',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_HILIT'
	=> 'Hilight',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TOPIC_HILIT'
	=> 'Hilight topic',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TOPIC_HILIT_EXPLAIN'
	=> 'when linked from a calendar topic tag',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TODAY'
	=> 'Mark today´s date',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_SORT_EXPLAIN'
	=> 'Drag the items to select and sort them',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_SORT_USED'
	=> 'Used',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_SORT_NOT_USED'
	=> 'Not used',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MERGE_PREVIOUS'
	=> 'Will be merged in previous row.',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MONTH'
	=> 'Month',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MONTHDAY'
	=> 'Month day',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_WEEKDAY'
	=> 'Weekday',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_MOONPHASE'
	=> 'Moon phase',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_ISOWEEK'
	=> 'ISO week number (ISO 1806)',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_BLANK'
	=> 'Blank',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_HEADER'
	=> 'Table header',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_HEADER_EN'
	=> 'Table header enabled',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_REPEATED_HEADER'
	=> 'Table repeated header',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_REPEATED_HEADER_EN'
	=> 'Table repeated header enabled',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_REPEATED_HEADER_NUM_ROWS'
	=> 'Repeat after this number of rows',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_REPEATED_HEADER_OMIT_ROWS'
	=> 'Omit the repeated header this number of rows before end',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_FOOTER'
	=> 'Table footer',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_FOOTER_EN'
	=> 'Table footer enabled',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_WEEKDAY_MAX_CHAR_COUNT'
	=> 'Maximum characters for weekdays',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TIME_FORMAT'
	=> 'Time format',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_TIME_FORMAT_EXPLAIN'
	=> 'This is the format used for displaying the time of the moon phases.',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DERIVE_USER_TIME_FORMAT'
	=> 'Derive user time format',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DERIVE_USER_TIME_FORMAT_EXPLAIN'
	=> 'Try to derive the time format from the user datetime configuration. Fallback on the default setting below when this fails.',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DEFAULT_TIME_FORMAT'
	=> 'Default time format',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_DEFAULT_TIME_FORMAT_EXPLAIN'
	=> 'See the %1$sPHP date() function%2$s for defining the format.',

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_STYLESHEET'
	=> 'Stylesheet',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_LOAD_STYLESHEET'
	=> 'Load stylesheet',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_LOAD_STYLESHEET_EXPLAIN'
	=> 'Disable when you load your own stylesheet',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_EXTRA_STYLESHEET'
	=> 'Extra stylesheet',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_EXTRA_STYLESHEET_EXPLAIN'
	=> 'Location of your own stylesheet to overwrite or replace the
	default one. Leave empty when not used.',
]);
