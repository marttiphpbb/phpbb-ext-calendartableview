<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\event;

use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\user;
use phpbb\event\data as event;
use marttiphpbb\calendarmonthview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class viewing_listener implements EventSubscriberInterface
{
	protected $helper;
	protected $php_ext;
	protected $language;
	protected $user;

	public function __construct(
		helper $helper,
		string $php_ext,
		language $language,
		user $user
	)
	{
		$this->helper = $helper;
		$this->php_ext = $php_ext;
		$this->language = $language;
		$this->user = $user;
	}

	static public function getSubscribedEvents():array
	{
		return [
			'core.viewonline_overwrite_location'	=> 'core_viewonline_overwrite_location',
		];
	}

	public function core_viewonline_overwrite_location(event $event):void
	{
		if (strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/calendar') === 0)
		{
			$now = $this->user->create_datetime();
			$time_offset = $now->getOffset();
			$now = phpbb_gmgetdate($now->getTimestamp() + $time_offset);

			$link = $this->helper->route('marttiphpbb_calendarmonthview_page_controller', [
				'year'	=> $now['year'],
				'month'	=> $now['mon'],
			]);

			$event['location'] = $this->language->lang(cnst::L . '_VIEWING');
			$event['location_url'] = $link;
		}
	}
}
