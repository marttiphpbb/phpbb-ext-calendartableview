<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\event;

use phpbb\controller\helper;
use phpbb\event\data as event;
use phpbb\auth\auth;
use marttiphpbb\calendarmonthview\service\user_today;
use marttiphpbb\calendarmonthview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class menu_listener implements EventSubscriberInterface
{
	protected $helper;
	protected $user_today;
	protected $auth;

	public function __construct(
		helper $helper,
		user_today $user_today,
		auth $auth
	)
	{
		$this->helper = $helper;
		$this->user_today = $user_today;
		$this->auth = $auth;
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

		$now = $this->user_today->get_date();

		$link = $this->helper->route('marttiphpbb_calendarmonthview_page_controller', [
			'year'	=> $now['year'],
			'month'	=> $now['mon'],
		]);

		$items[cnst::FOLDER]['links'] = [
			'link'		=> $link,
			'include'	=> cnst::TPL . 'include/menu_item.html',
		];

		$event['items'] = $items;
	}
}
