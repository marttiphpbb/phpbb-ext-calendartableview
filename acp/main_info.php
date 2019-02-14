<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\acp;

use marttiphpbb\calendarmonthview\util\cnst;

class main_info
{
	function module()
	{
		return [
			'filename'	=> '\marttiphpbb\calendarmonthview\acp\main_module',
			'title'		=> cnst::L_ACP,
			'modes'		=> [
				'links'	=> [
					'title' => cnst::L_ACP . '_LINKS',
					'auth' => 'ext_marttiphpbb/calendarmonthview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
				'page_rendering'	=> [
					'title' => cnst::L_ACP . '_PAGE_RENDERING',
					'auth' => 'ext_marttiphpbb/calendarmonthview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
			],
		];
	}
}
