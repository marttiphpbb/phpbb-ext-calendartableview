<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\event;

use phpbb\controller\helper;
use marttiphpbb\calendartableview\service\store;
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

		$num_days_offset_tag = $this->store->get_num_days_offset_tag();

		if ($num_days_offset_tag)
		{
			$start_jd = $event['start_jd'] - $num_days_offset_tag;
			$start = cal_from_jd($start_jd, CAL_GREGORIAN);

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
				'day'	=> $event['day'],
			];
		}

		if ($this->store->get_topic_hilit())
		{
			$params['t'] = $event['topic_id'];
		}

		$link = $this->helper->route('marttiphpbb_calendartableview_page_controller', $params);

		$event['link'] = $link;
	}
}
