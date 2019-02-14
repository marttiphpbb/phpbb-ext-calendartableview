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

	'MARTTIPHPBB_CALENDARMONTHVIEW_MONTH_YEAR'
	=> '%1$s %2$s',
	'MARTTIPHPBB_CALENDARMONTHVIEW_NEW_MOON'
	=> 'New moon&#10;@ %s',
	'MARTTIPHPBB_CALENDARMONTHVIEW_FIRST_QUARTER_MOON'
	=> 'First quarter moon&#10;@ %s',
	'MARTTIPHPBB_CALENDARMONTHVIEW_FULL_MOON'
	=> 'Full moon&#10;@ %s',
	'MARTTIPHPBB_CALENDARMONTHVIEW_THIRD_QUARTER_MOON'
	=> 'Third quarter moon&#10;@ %s',
]);
