<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\event;

use phpbb\controller\helper;
use phpbb\event\data as event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use marttiphpbb\calendartableview\service\store;

class link_listener implements EventSubscriberInterface
{
	protected $helper;
	protected $store;

	public function __construct(helper $helper, store $store)
	{
		$this->helper = $helper;
		$this->store = $store;
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

		$num_days_offset_link = $this->store->get_num_days_offset_link();

		if ($num_days_offset_link)
		{
			$jd = $event['jd'] - $num_days_offset_link;
			$start = cal_from_jd($jd, CAL_GREGORIAN);

			$params = [
				'year'	=> $start['year'],
				'month'	=> $start['month'],
				'day'	=> $start['day'],
			];
		}
		else
		{
			$params = [
				'year'	=> $event['year'],
				'month'	=> $event['month'],
				'day'	=> $event['monthday'],
			];
		}

		$link = $this->helper->route('marttiphpbb_calendartableview_page_controller', $params);

		$event['link'] = $link;
	}
}
