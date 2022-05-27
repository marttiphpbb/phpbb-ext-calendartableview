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

	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW'
	=> 'Calendar Table View',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_LINKS'
	=> 'Links',
	'ACP_MARTTIPHPBB_CALENDARTABLEVIEW_PAGE_RENDERING'
	=> 'Page rendering',
]);
