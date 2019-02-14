<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\event;

use phpbb\controller\helper;
use marttiphpbb\calendarmonthview\service\store;
use phpbb\event\data as event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class tag_listener implements EventSubscriberInterface
{
	protected $helper;

	public function __construct(helper $helper, store $store)
	{
		$this->helper = $helper;
		$this->store = $store;
	}

	static public function getSubscribedEvents():array
	{
		return [
			'marttiphpbb.calendartag.link'	=> 'link',
		];
	}

	public function link(event $event):void
	{
		$link = $event['link'];

		if ($link)
		{
			return;
		}

		$params = [
			'year'	=> $event['year'],
			'month'	=> $event['month'],
		];

		if ($this->store->get_topic_hilit())
		{
			$params['t'] = $event['topic_id'];
		}

		$link = $this->helper->route('marttiphpbb_calendarmonthview_page_controller', $params);

		$event['link'] = $link;
	}
}
