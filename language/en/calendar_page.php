<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2022 marttiphpbb <info@martti.be>
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

	'MARTTIPHPBB_CALENDARTABLEVIEW_MONTH_YEAR'
	=> '%1$s %2$s',
	'MARTTIPHPBB_CALENDARTABLEVIEW_NEW_MOON'
	=> 'New moon&#10;@ %s',
	'MARTTIPHPBB_CALENDARTABLEVIEW_FIRST_QUARTER_MOON'
	=> 'First quarter moon&#10;@ %s',
	'MARTTIPHPBB_CALENDARTABLEVIEW_FULL_MOON'
	=> 'Full moon&#10;@ %s',
	'MARTTIPHPBB_CALENDARTABLEVIEW_THIRD_QUARTER_MOON'
	=> 'Third quarter moon&#10;@ %s',
]);
