<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2022 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\acp;

use marttiphpbb\calendartableview\util\cnst;

class main_info
{
	function module():array
	{
		return [
			'filename'	=> '\marttiphpbb\calendartableview\acp\main_module',
			'title'		=> cnst::L_ACP,
			'modes'		=> [
				'links'	=> [
					'title' => cnst::L_ACP . '_LINKS',
					'auth' => 'ext_marttiphpbb/calendartableview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
				'page_rendering'	=> [
					'title' => cnst::L_ACP . '_PAGE_RENDERING',
					'auth' => 'ext_marttiphpbb/calendartableview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
			],
		];
	}
}
