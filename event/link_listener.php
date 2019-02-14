<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\event;

use phpbb\controller\helper;
use phpbb\event\data as event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class link_listener implements EventSubscriberInterface
{
	protected $helper;

	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	static public function getSubscribedEvents()
	{
		return [
			'marttiphpbb.calendar.view_link'	=> 'link',
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

		$link = $this->helper->route('marttiphpbb_calendarmonthview_page_controller', $params);

		$event['link'] = $link;
	}
}
