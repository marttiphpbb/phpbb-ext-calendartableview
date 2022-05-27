<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2022 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\migrations;

use marttiphpbb\calendartableview\util\cnst;

class mgr_2 extends \phpbb\db\migration\migration
{
	static public function depends_on():array
	{
		return [
			'\marttiphpbb\calendartableview\migrations\mgr_1',
		];
	}

	public function update_data():array
	{
		return [
			['config_text.add', [cnst::ID, serialize(cnst::DEFAULT_SETTINGS)]],
		];
	}
}
