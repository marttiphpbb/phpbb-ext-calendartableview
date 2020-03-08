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

		$event_row_count = $min_rows;

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

			for ($row = 0; $row < $max_rows; $row++)
			{
				if (!isset($row_ary[$row]))
				{
					$row_ary[$row] = [];
				}

				$overlaps = false;
				$event_index = 0;

				foreach ($row_ary[$row] as $event_index => $ev)
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
					array_splice($row_ary[$row], $event_index, 0, [[
						'start'		=> $e_col_start,
						'end'		=> $e_col_end,
					]]);

					$col_ary[$e_col_start][$row] = [
						'start'		=> $e_col_start,
						'end'		=> $e_col_end,
						'topic'		=> $e['topic_id'],
					];

					$col_ary[$e_col_end + 1][$row] = [
						'free'		=> true,
					];

					$event_row_count = max($event_row_count, $row + 1);

					break;
				}
			}
		}

		if ($repeated_header_en)
		{
			$repeated_header_effective_rows = $event_row_count - $repeated_header_omit_rows;
			$repeated_header_count = intdiv($repeated_header_effective_rows, $repeated_header_num_rows);
			$event_body_num_rows = $repeated_header_count === 0 ? $event_row_count : $repeated_header_num_rows;
		}
		else
		{
			$repeated_header_count = 0;
			$event_body_num_rows = $event_row_count;
		}

		$event_body_count = $repeated_header_count + 1;

		for ($table = 0; $table < $num_tables; $table++)
		{
			$this->template->assign_block_vars('tables', []);

			$table_start = $num_days_one_table * $table;
			$table_next = $num_days_one_table + $table_start;

			$new_table = true;

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

					$month_abbrev = $day['abbrevmonth'] === 'May' ? 'May_short' : $day['abbrevmonth'];

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
					$moon_phase = next($moon_phase_ary);
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
				$this->template->assign_block_vars('tables.months.days', $day_tpl);

				$day_tpl_ary[$col] = $day_tpl;
			}

			if ($header_en)
			{
				$this->assign_header_tpl($header, 'header_rows');
			}

			if ($repeated_header_en)
			{
				$this->assign_header_tpl($repeated_header, 'repeated_header_rows');
			}

			if ($footer_en)
			{
				$this->assign_header_tpl($footer, 'footer_rows');
			}

			$col_rowspan_cache = [
				0 => $event_row_count, ///////////// wrong
			];

			$hold_body = false;

			for ($row = 0; $row < $event_row_count; $row++)
			{
				if (($row % $event_body_num_rows) === 0
					&& !$hold_body)
				{
					$this->template->assign_block_vars('tables.event_bodies', []);

					$body++;
					$not_last_event_body = $repeated_header_count < $repeated_header_times;



					if ($table === 0)
					{
						$tblock_col_cache = '';
					}
				}

				$this->template->assign_block_vars('tables.event_bodies.event_rows', []);



				for ($col = $table_start; $col < $table_next; $col++)
				{
					$col_ary[$e_col_start][$row_index] = [
						'start'		=> $e_col_start,
						'end'		=> $e_col_end,
						'topic'		=> $e['topic_id'],
					];

					$col_ary[$e_col_end + 1][$row_index] = [
						'free'		=> true,
					];

				}
			}
		}

//		$this->pagination->render($start_jd, $num_days_one_table);

		$this->template->assign_vars([
			'TODAY_JD'			=> $today_jd,
			'TOPIC_HILIT'		=> $this->request->variable('t', 0),
			'SHOW_TODAY'		=> $this->store->get_show_today(),
			'LOAD_STYLESHEET'	=> $this->store->get_load_stylesheet(),
			'EXTRA_STYLESHEET'	=> $this->store->get_extra_stylesheet(),
		]);

		make_jumpbox(append_sid($this->root_path . 'viewforum.' . $this->php_ext));

		$title = $this->language->lang('MARTTIPHPBB_CALENDARTABLEVIEW_CALENDAR');
		return $this->helper->render('calendar.html', $title);
	}

	private function assign_header_tpl(array $header_ary, string $block_name)
	{
		$block_name = 'tables.' . $block_name;

		foreach($header_ary as $header_id)
		{
			$header_name = cnst::HEADER_ROWS[$header_id]['name'];

			if (isset($row_tpl)
				&& !in_array($header_name, ['isoweek', 'moonphase']))
			{
				$this->template->assign_block_vars($block_name, $row_tpl);
				unset($row_tpl);
			}

			if (!isset($row_tpl))
			{
				$row_tpl = cnst::INIT_HEADER_TPL;
			}

			if ($header_id === 'month')
			{
				$row_tpl['S_MONTH'] = true;

				$this->template->assign_block_vars('tables.header_rows', $row_tpl);
				unset($row_tpl);

				continue;
			}

			if (in_array($header_id, ['isoweek', 'moonphase']))
			{
				if (!($row_tpl['S_BLANK']
					|| $row_tpl['S_MONTHDAY']
					|| $row_tpl['S_WEEKDAY']
				))
				{
					$row_tpl['S_BLANK'] = true;
				}

				if ($header_id === 'isoweek')
				{
					$row_tpl['S_ISOWEEK'] = true;

					if (!$row_tpl['S_MOONPHASE_FIRST'])
					{
						$row_tpl['S_ISOWEEK_FIRST'] = true;
					}
				}
				else
				{
					$row_tpl['S_MOONPHASE'] = true;

					if (!$row_tpl['S_ISOWEEK_FIRST'])
					{
						$row_tpl['S_MOONPHASE_FIRST'] = true;
					}
				}

				continue;
			}

			if ($header_id === 'weekday')
			{
				$row_tpl['S_WEEKDAY'] = true;
				continue;
			}

			if ($header_id === 'monthday')
			{
				$row_tpl['S_MONTHDAY'] = true;
				continue;
			}

			$row_tpl['S_BLANK'] = true;
		}

		if (isset($row_tpl))
		{
			$this->template->assign_block_vars($block_name, $row_tpl);
			unset($row_tpl);
		}
	}
}
