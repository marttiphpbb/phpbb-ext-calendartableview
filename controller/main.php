<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\controller;

use phpbb\event\dispatcher;
use phpbb\request\request;
use phpbb\template\twig\twig as template;
use phpbb\language\language;
use phpbb\controller\helper;
use marttiphpbb\calendartableview\render\row_container;
use marttiphpbb\calendartableview\value\topic;
use marttiphpbb\calendartableview\value\dayspan;
use marttiphpbb\calendartableview\value\calendar_event;
use marttiphpbb\calendartableview\service\store;
use marttiphpbb\calendartableview\service\user_today;
use marttiphpbb\calendartableview\service\user_time;
use marttiphpbb\calendartableview\service\pagination;
use marttiphpbb\calendartableview\util\cnst;
use marttiphpbb\calendartableview\util\moon_phase;
use Symfony\Component\HttpFoundation\Response;

class main
{
	protected $dispatcher;
	protected $request;
	protected $php_ext;
	protected $template;
	protected $language;
	protected $helper;
	protected $root_path;
	protected $pagination;
	protected $store;
	protected $user_today;
	protected $user_time;

	public function __construct(
		dispatcher $dispatcher,
		request $request,
		string $php_ext,
		template $template,
		language $language,
		helper $helper,
		string $root_path,
		pagination $pagination,
		store $store,
		user_today $user_today,
		user_time $user_time
	)
	{
		$this->dispatcher = $dispatcher;
		$this->request = $request;
		$this->php_ext = $php_ext;
		$this->template = $template;
		$this->language = $language;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->pagination = $pagination;
		$this->store = $store;
		$this->user_today = $user_today;
		$this->user_time = $user_time;
	}

	public function page(int $year, int $month, int $day):Response
	{
		$this->language->add_lang('calendar_page', cnst::FOLDER);

		$num_tables = $this->store->get_num_tables();
		$num_days_one_table = $this->store->get_num_days_one_table();
		$num_days = $num_tables * $num_days_one_table;

		$today_jd = $this->user_today->get_jd();

		$start_jd = cal_to_jd(CAL_GREGORIAN, $month, $day, $year);
		$end_jd = $start_jd + $num_days - 1;

		if ($this->store->get_show_moon_phase())
		{
			$moon_phase = new moon_phase();
			$moon_phases = $moon_phase->find($start_jd, $end_jd);
			$mphase = reset($moon_phases);
		}
		else
		{
			$mphase = [];
		}

		$events = [];

		/**
		 * Event to fetch the calendar events for the view
		 *
		 * @event
		 * @var int 	start_jd	start julian day of the view
		 * @var int 	end_jd		end julian day of the view
		 * @var array   events      items should contain
		 * start_jd, end_jd, topic_id, forum_id, topic_title
		 */
		$vars = ['start_jd', 'end_jd', 'events'];
		extract($this->dispatcher->trigger_event('marttiphpbb.calendar.view', compact($vars)));

		$row_container = new row_container($this->store->get_min_rows(), $this->store->get_max_rows());

		foreach($events as $e)
		{
			$topic = new topic($e['topic_id'], $e['forum_id'], $e['topic_title']);
			$calendar_event = new calendar_event($e['start_jd'], $e['end_jd'], $topic);
			$row_container->add_calendar_event($calendar_event);
		}

		$col = 0;
		$year_begin_jd = cal_to_jd(CAL_GREGORIAN, 1, 1, $year);
		$total_dayspan = new dayspan($start_jd, $end_jd);
//		$rows = $row_container->get_rows();
		$start_row = 0;
		$end_row = $row_container->get_row_count() - 1;

		$current_block_ary = [[
			'dayspan' 		=> $total_dayspan,
			'start_row' 	=> $start_row,
			'end_row'		=> $end_row,
		]];

		$row_block_ary = [];

		for ($row_index = 0; $row_index < $end_row; $row_index++)
		{
			$row_block_ary[$row_index] = [];
			$row = $row_container->get_row($row_index);
			$segments = $row->get_segments($total_dayspan);

			foreach ($segments as $segment)
			{
				if ($segment instanceof calendar_event)
				{
					$row_block_ary[$row_index][] = [
						'segment'	=> $segment,
						'row_start'	=> $row_index,
						'row_end'	=> $row_index,
					];
				}
				else
				{
					foreach ($current_block_ary as $block)
					{
						$new_dayspan = $segment->create_from_overlap($block['dayspan']);
						$dayspan = $block['dayspan'];
					}
				}
			}
		}

		for($table_index = 0; $table_index < $num_tables; $table_index++)
		{
			$table_start_jd = $start_jd + $num_days_one_table * $table_index;
			$table_dayspan = new dayspan($table_start_jd, $table_start_jd + $num_days_one_table - 1);

		}

		for ($jd = $start_jd; $jd <= $end_jd; $jd++)
		{
			$first_day = !$col;
			$table_col = $col % $num_days_one_table;
			$day = cal_from_jd($jd, CAL_GREGORIAN);

			if ($day['dayname'] === 'Monday' || $first_day)
			{
				$isoweek = gmdate('W', jdtounix($jd));
			}

			if ($day['day'] === 1 || $first_day)
			{
				$month_abbrev = $day['abbrevmonth'] === 'May' ? 'May_short' : $day['abbrevmonth'];
				$month_abbrev = $this->language->lang(['datetime', $month_abbrev]);
				$month_name = $this->language->lang(['datetime', $day['monthname']]);

				if ($month === $day['month'])
				{
					$this->template->assign_vars([
						'MONTH'				=> $month,
						'MONTH_NAME'		=> $this->language->lang(['datetime', $day['monthname']]),
						'MONTH_ABBREV'		=> $this->language->lang(['datetime', $month_abbrev]),
						'YEAR'				=> $year,
						'TODAY_JD'			=> $today_jd,
						'TOPIC_HILIT'		=> $this->request->variable('t', 0),
						'SHOW_ISOWEEK'		=> $this->store->get_show_isoweek(),
						'SHOW_TODAY'		=> $this->store->get_show_today(),
						'SHOW_MOON_PHASE'	=> $this->store->get_show_moon_phase(),
						'LOAD_STYLESHEET'	=> $this->store->get_load_stylesheet(),
						'EXTRA_STYLESHEET'	=> $this->store->get_extra_stylesheet(),
						'EVENT_ROW_COUNT'	=> $row_container->get_row_count(),
					]);
				}
			}

			if (!$table_col)
			{
				$this->template->assign_block_vars('tables', []);

				foreach($rows as $row)
				{
					$this->template->assign_block_vars('tables.eventrows', []);

					$week_end_jd = $jd + 6;

					$week_dayspan = new dayspan($jd, $week_end_jd);
					$segments = $row->get_segments($week_dayspan);

					foreach($segments as $segment)
					{
						if ($segment instanceof calendar_event)
						{
							$topic = $segment->get_topic();
							$params = [
								't'		=> $topic->get_topic_id(),
								'f'		=> $topic->get_forum_id(),
							];
							$link = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, $params);

							$this->template->assign_block_vars('weeks.eventrows.eventsegments', [
								'TOPIC_ID'			=> $topic->get_topic_id(),
								'FORUM_ID'			=> $topic->get_forum_id(),
								'TOPIC_TITLE'		=> $topic->get_topic_title(),
								'TOPIC_LINK'		=> $link,
								'FLEX'				=> $segment->get_overlap_day_count($week_dayspan),
								'S_START'			=> $week_dayspan->contains_day($segment->get_start_jd()),
								'S_END'				=> $week_dayspan->contains_day($segment->get_end_jd()),
							]);
						}
						else if ($segment instanceof dayspan)
						{
							$this->template->assign_block_vars('weeks.eventrows.eventsegments', [
								'FLEX'		=> $segment->get_overlap_day_count($week_dayspan),
							]);
						}
					}
				}
			}

			if (isset($mphase['jd']) && $mphase['jd'] === $jd)
			{
				$phase = $mphase['phase'];
				$moon_time = $this->user_time->get($mphase['time']);
				$moon_title = $this->language->lang(cnst::L . '_' . cnst::MOON_LANG[$phase], $moon_time);
				$moon_icon = cnst::MOON_ICON[$phase];
				$mphase = next($moon_phases);
			}
			else
			{
				$moon_title = false;
				$moon_icon = false;
			}

			$this->template->assign_block_vars('tables.weekdays', [
				'JD'				=> $jd,
				'WEEKDAY'			=> $day['dow'],
				'WEEKDAY_NAME'		=> $this->language->lang(['datetime', $day['dayname']]),
				'WEEKDAY_ABBREV'	=> $this->language->lang(['datetime', $day['abbrevdayname']]),
				'WEEKDAY_CLASS'		=> strtolower($day['abbrevdayname']),
				'MONTHDAY'			=> $day['day'],
				'MONTH'				=> $day['month'],
				'MONTH_NAME'		=> $month_name,
				'MONTH_ABBREV'		=> $month_abbrev,
				'YEAR'				=> $day['year'],
				'YEARDAY'			=> $year_begin_jd - $jd + 1,
				'ISOWEEK'			=> $isoweek,
				'MOON_TITLE'		=> $moon_title,
				'MOON_ICON'			=> $moon_icon,
				'COL'				=> $col,
				'TABLE_COL'			=> $table_col,
			]);

			$col++;
		}

//		$this->pagination->render($start_jd, $num_days_one_table);

		make_jumpbox(append_sid($this->root_path . 'viewforum.' . $this->php_ext));

		$title = $this->language->lang('MARTTIPHPBB_CALENDARTABLEVIEW_CALENDAR');
		return $this->helper->render('calendar.html', $title);
	}
}
