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

		$row_ary = [];
		$col_ary = [];
		$topic_ary = [];

		$header_en = $this->store->get_header_en();
		$header = $this->store->get_header();
		$repeated_header_en = $this->store->get_repeated_header_en();
		$repeated_header = $this->store->get_repeated_header();
		$repeated_header_num_rows = $this->store->get_repeated_header_num_rows();
		$repeated_header_omit_rows = $this->store->get_repeated_header_omit_rows();
		$footer_en = $this->store->get_footer_en();
		$footer = $this->store->get_footer();
		$weekday_max_chars = $this->store->get_weekday_max_chars();

		$min_rows = $this->store->get_min_rows();
		$mex_rows = $this->store->get_max_rows();

		$ev_row_count = $min_rows;

		$num_tables = $this->store->get_num_tables();
		$num_days_one_table = $this->store->get_num_days_one_table();
		$num_days = $num_tables * $num_days_one_table;
		$end_col = $num_days - 1;

		$today_jd = $this->user_today->get_jd();

		$start_jd = cal_to_jd(CAL_GREGORIAN, $month, $day, $year);
		$end_jd = $start_jd + $num_days - 1;

		$moon_phase_ary = moon_phase::find($start_jd, $end_jd);
		$moon_phase = reset($moon_phase_ary);

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

		error_log(json_encode($events));

		foreach($events as $e)
		{
			if ($e['end_jd'] < $start_jd)
			{
				continue;
			}

			if ($e['start_jd'] > $end_jd)
			{
				continue;
			}

			$topic_ary[$e['topic_id']] = $e;

			$e_start_col = max($start_jd - $e['start_jd'], 0);
			$e_end_col = min($end_jd - $e['end_jd'], $end_col);

			for ($row_index = 0; $row_index < $max_rows; $row_index++)
			{
				if (!isset($row_ary[$row_index]))
				{
					$row_ary[$row_index] = [];
				}

				$overlaps = false;
				$ev_index = 0;

				foreach ($row_ary[$row_index] as $ev_index => $ev)
				{
					if ($e_start_col <= $ev['end']
						&& $e_end_col >= $ev['start'])
					{
						$overlaps = true;
						break;
					}

					if ($e_end_col < $ev['start'])
					{
						break;
					}
				}

				if (!$overlaps)
				{
					array_splice($row_ary[$row_index], $ev_index, 0, [[
						'start'		=> $e_col_start,
						'end'		=> $e_col_end,
					]]);

					$col_ary[$e_col_start][$row_index] = [
						'start'		=> $e_col_start,
						'end'		=> $e_col_end,
						'topic'		=> $e['topic_id'],
					];

					$col_ary[$e_col_end + 1][$row_index] = [
						'free'		=> true,
					];

					$ev_row_count = max($ev_row_count, $row_index + 1);

					break;
				}
			}
		}

		if ($header_en)
		{
			$header_ary = [];

			while ($header_name = array_pop($header))
			{
				[$header_name] = explode('_', $header_name);



			}

			$header_ary = [];

			foreach($header as $header_row)
			{


				array_splice($row_ary, 0, 0, [[
					'start'		=> $e_col_start,
					'end'		=> $e_col_end,
				]]);

			//	'headers' => ['moonphase', 'isoweek', 'blank'];

			//	array_unshift($row_ary, )
			}
		}

		$repeated_header_times = 0;

		if ($repeated_header_en
			&& $repeated_header_num_rows)
		{
			$repeated_header_ef_rows = $ev_row_count - $repeated_header_omit_rows;
			$repeated_header_times = intdiv($repeated_header_ef_rows, $repeated_header_num_rows);
		}

		if ($repeated_header_times)
		{

		}

		if ($footer_en)
		{
			foreach($footer as $footer_row)
			{

			}
		}

		for ($table = 0; $table < $num_tables; $table++)
		{
			$this->template->assign_block_vars('tables', []);

			$table_start = $num_days_one_table * $table;
			$table_next = $num_days_one_table + $table_start;

			$new_table = true;

			for ($row = 0; $row < $row_count; $row++)
			{

			}

			for ($col = $table_start; $col < $table_next; $col++)
			{
				$max_table_span = $table_next - $col;

				$jd = $start_jd + $col;
				$day = cal_from_jd($jd, CAL_GREGORIAN);

				if ($day['dayname'] === 'Monday' || $new_table)
				{
					$isoweek = gmdate('W', jdtounix($jd));
				}

				if ($day['day'] === 1 || $new_table)
				{
					$month_day_count = cal_days_in_month(CAL_GREGORIAN, $day['month'], $day['year']);
					$month_span = min($month_day_count - $day['day'] + 1, $max_table_span);

					// colgroups ~ months
					$month_tpl = [
						'MONTH'				=> $day['month'],
						'MONTH_NAME'		=> $this->language->lang(['datetime', $day['monthname']]),
						'MONTH_ABBREV'		=> $this->language->lang(['datetime', $month_abbrev]),
						'MONTH_CLASS'		=> strtolower($day['abbrevmonth']),
						'YEAR'				=> $day['year'],
						'SPAN'				=> $month_span,
					];

					// colgroup
					$this->template->assign_block_vars('tables.months', $month_tpl);

					$new_table = false;
				}

				if (isset($moon_phase['jd'])
					&& $moon_phase['jd'] === $jd)
				{
					$phase = $moon_phase['phase'];
					$moon_time = $this->user_time->get($moon_phase['time']);
					$moon_title = $this->language->lang(cnst::L . '_' . cnst::MOON_LANG[$phase], $moon_time);
					$moon_icon = cnst::MOON_ICON[$phase];
					$moon_phase = next($moon_phases);
				}
				else
				{
					$moon_title = false;
					$moon_icon = false;
				}

				$weekday_name = $this->language->lang(['datetime', $day['dayname']]);
				$weekday_abbrev = substr($weekday_name, 0, $weekday_max_chars);

				// cols ~ days
				$day_tpl = [
					'COL'				=> $col,
					'JD'				=> $jd,
					'WEEKDAY'			=> $day['dow'],
					'WEEKDAY_NAME'		=> $weekday_name,
					'WEEKDAY_ABBREV'	=> $weekday_abbrev,
					'WEEKDAY_CLASS'		=> strtolower($day['abbrevdayname']),
					'MONTHDAY'			=> $day['day'],
					'ISOWEEK'			=> $isoweek,
					'MOON_TITLE'		=> $moon_title,
					'MOON_ICON'			=> $moon_icon,
				];

				// cols
				$this->template->assing_block_vars('tables.months.days', $day_tpl);
			}


		}

//		$this->pagination->render($start_jd, $num_days_one_table);

		$this->template->assign_vars([
			'TODAY_JD'			=> $today_jd,
			'TOPIC_HILIT'		=> $this->request->variable('t', 0),
			'SHOW_TODAY'		=> $this->store->get_show_today(),
			'LOAD_STYLESHEET'	=> $this->store->get_load_stylesheet(),
			'EXTRA_STYLESHEET'	=> $this->store->get_extra_stylesheet(),
			'EVENT_ROW_COUNT'	=> $row_container->get_row_count(),
		]);

		make_jumpbox(append_sid($this->root_path . 'viewforum.' . $this->php_ext));

		$title = $this->language->lang('MARTTIPHPBB_CALENDARTABLEVIEW_CALENDAR');
		return $this->helper->render('calendar.html', $title);
	}
}
