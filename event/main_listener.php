<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2022 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\event;

use phpbb\event\data as event;
use marttiphpbb\calendartableview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents():array
	{
		return [
			'core.user_setup'	=> 'core_user_setup',
		];
	}

	public function core_user_setup(event $event):void
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => cnst::FOLDER,
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
