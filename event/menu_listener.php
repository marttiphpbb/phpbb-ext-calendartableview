<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2022 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\event;

use phpbb\controller\helper;
use phpbb\event\data as event;
use phpbb\auth\auth;
use marttiphpbb\calendartableview\service\user_today;
use marttiphpbb\calendartableview\service\store;
use marttiphpbb\calendartableview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class menu_listener implements EventSubscriberInterface
{
	protected $helper;
	protected $user_today;
	protected $auth;
	protected $store;

	public function __construct(
		helper $helper,
		user_today $user_today,
		auth $auth,
		store $store
	)
	{
		$this->helper = $helper;
		$this->user_today = $user_today;
		$this->auth = $auth;
		$this->store = $store;
	}

	static public function getSubscribedEvents():array
	{
		return [
			'marttiphpbb.menuitems'	=> 'add_items',
		];
	}

	public function add_items(event $event):void
	{
		$items = $event['items'];

		if (!count($this->auth->acl_getf('f_read')))
		{
			return;
		}

		$days_offset_menu = $this->store->get_days_offset_menu();
		$start = $this->user_today->get_date_with_day_offset(-$days_offset_menu);

		$link = $this->helper->route('marttiphpbb_calendartableview_page_controller', [
			'year'	=> $start['year'],
			'month'	=> $start['mon'],
			'day'	=> $start['mday'],
		]);

		$items[cnst::FOLDER]['links'] = [
			'link'		=> $link,
			'include'	=> cnst::TPL . 'include/menu_item.html',
		];

		$event['items'] = $items;
	}
}
