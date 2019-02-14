<?php

/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
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

	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_SETTINGS_SAVED'
	=> 'The settings have been saved successfully!',

// links
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_MENUITEMS_NOT_ENABLED'
	=> 'The %1$sMenu Items extension%2$s should be installed
	for this functionality.',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_LINKS'
	=> 'Links',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_LINKS_EXPLAIN'
	=> 'Menu link locations to the Calendar Month View page',

// page_rendering
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_PAGE'
	=> 'Calendar page',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_ISOWEEK'
	=> 'Display the week number (ISO 1806)',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_MOON_PHASE'
	=> 'Display the moon phase',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_TOPIC_HILIT'
	=> 'Hilight topic',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_TOPIC_HILIT_EXPLAIN'
	=> 'when linked from a calendar topic tag',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_TODAY'
	=> 'Mark today´s date',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_FIRST_WEEKDAY'
	=> 'First day of the week',

	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_TIME_FORMAT'
	=> 'Time format',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_TIME_FORMAT_EXPLAIN'
	=> 'This is the format used for displaying the time of the moon phases.',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_DERIVE_USER_TIME_FORMAT'
	=> 'Derive user time format',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_DERIVE_USER_TIME_FORMAT_EXPLAIN'
	=> 'Try to derive the time format from the user datetime configuration. Fallback on the default setting below when this fails.',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_DEFAULT_TIME_FORMAT'
	=> 'Default time format',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_DEFAULT_TIME_FORMAT_EXPLAIN'
	=> 'See the %1$sPHP date() function%2$s for defining the format.',

	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_EVENT_ROWS'
	=> 'Event rows',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_MIN_ROWS'
	=> 'Minimum event rows',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_MAX_ROWS'
	=> 'Maximum event rows',

	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_PAGINATION'
	=> 'Pagination',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_PAGINATION_NEIGHBOURS'
	=> 'Number of neighbour months',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_PAGINATION_SHOW_PREV_NEXT'
	=> 'Show previous/next month links',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_STYLESHEET'
	=> 'Stylesheet',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_LOAD_STYLESHEET'
	=> 'Load stylesheet',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_LOAD_STYLESHEET_EXPLAIN'
	=> 'Disable when you load your own stylesheet',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_EXTRA_STYLESHEET'
	=> 'Extra stylesheet',
	'ACP_MARTTIPHPBB_CALENDARMONTHVIEW_EXTRA_STYLESHEET_EXPLAIN'
	=> 'Location of your own stylesheet to overwrite or replace the
	default one. Leave empty when not used.',
]);
