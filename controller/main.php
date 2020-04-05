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
		$free_ary = [];
		$topic_ary = [];

		$header_en = $this->store->get_header_en();
		$header = $this->store->get_header();

		if (count($header) === 0)
		{
			$header_en = false;
		}

		$repeated_header_en = $this->store->get_repeated_header_en();
		$repeated_header = $this->store->get_repeated_header();
		$repeated_header_row_count = $this->store->get_repeated_header_row_count();
		$repeated_header_omit_row_count = $this->store->get_repeated_header_omit_row_count();

		if (count($repeated_header) === 0)
		{
			$repeated_header_en = false;
		}

		$footer_en = $this->store->get_footer_en();
		$footer = $this->store->get_footer();

		if (count($footer) === 0)
		{
			$footer_en = false;
		}

		$weekday_max_char_count = $this->store->get_weekday_max_char_count();

		$min_row_count = $this->store->get_min_row_count();
		$max_row_count = $this->store->get_max_row_count();

		$event_row_count = $min_row_count;

		$table_count = $this->store->get_table_count();
		$table_day_count = $this->store->get_table_day_count();
		$day_count = $table_count * $table_day_count;
		$last_col = $day_count - 1;

		$today_jd = $this->user_today->get_jd();

		$start_jd = cal_to_jd(CAL_GREGORIAN, $month, $day, $year);
		$end_jd = $start_jd + $day_count - 1;

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

			$topic_ary[$e['topic_id']]['link'] = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, [
				't'		=> $e['topic_id'],
				'f'		=> $e['forum_id'],
			]);

			$start_col = (int) max($e['start_jd'] - $start_jd, 0);
			$end_col = (int) min($e['end_jd'] - $start_jd, $last_col);

			for ($row = 0; $row < $max_row_count; $row++)
			{
				if (!isset($row_ary[$row]))
				{
					$row_ary[$row] = [];
				}

				$overlaps = false;
				$event_index = 0;

				foreach ($row_ary[$row] as $event_index => $ev)
				{
					if ($start_col <= $ev['end_col']
						&& $end_col >= $ev['start_col'])
					{
						$overlaps = true;
						break;
					}

					if ($end_col < $ev['start_col'])
					{
						break;
					}
				}

				if (!$overlaps)
				{
					array_splice($row_ary[$row], $event_index, 0, [[
						'start_col'		=> $start_col,
						'end_col'		=> $end_col,
					]]);

					$c_start = $start_col;

					$end_table = intdiv($end_col, $table_day_count);

					do
					{
						$start_table = intdiv($c_start, $table_day_count);

						if ($end_table > $start_table)
						{
							$c_end = ($start_table + 1) * $table_day_count - 1;
						}
						else
						{
							$c_end = $end_col;
						}

						if (!isset($col_ary[$c_start]))
						{
							$col_ary[$c_start] = [];
						}

						$col_ary[$c_start][$row] = [
							'colspan'	=> $c_end - $c_start + 1,
							'topic_id'	=> $e['topic_id'],
						];

						$c_start = $c_end + 1;

					} while ($end_table > $start_table);

					$next_col = $end_col + 1;

					if (!isset($col_ary[$next_col]))
					{
						$col_ary[$next_col] = [];
					}

					if (!isset($col_ary[$next_col][$row]))
					{
						$col_ary[$next_col][$row] = [
							'clear'		=> true,
						];
					}

					$event_row_count = (int) max($event_row_count, $row + 1);

					break;
				}
			}
		}

		if (!isset($col_ary[0]))
		{
			$col_ary[0] = [];
		}

		for ($row = 0; $row < $event_row_count; $row++)
		{
			if (!isset($col_ary[0][$row]))
			{
				$col_ary[0][$row] = [
					'clear'		=> true,
				];
			}
		}

		for ($table = 0; $table < $table_count; $table++)
		{
			$last_table_col = ($table + 1) * $table_day_count - 1;

			if (isset($col_ary[$last_table_col]))
			{
				continue;
			}

			$col_ary[$last_table_col][0] = [
				'last_table_col'	=> true,
			];
		}

		ksort($col_ary);

		if ($repeated_header_en)
		{
			$repeated_header_effective_rows = $event_row_count - $repeated_header_omit_row_count;
			$repeated_header_count = intdiv($repeated_header_effective_rows, $repeated_header_row_count);
			$tbody_row_count = $repeated_header_count === 0 ? $event_row_count : $repeated_header_row_count;
		}
		else
		{
			$repeated_header_count = 0;
			$tbody_row_count = $event_row_count;
		}

		$tbody_count = $repeated_header_count + 1;
		$last_tbody_row_count = $event_row_count - ($repeated_header_count * $tbody_row_count);

		$tbody_ary = [];
		$tbody_row_taken_ary = [];

		foreach ($col_ary as $col => $col_row)
		{
			$tbody_changed_ary = [];
			$tbody_start_event_ary = [];

			$is_last_table_col = (($col + 1) % $table_day_count) === 0;

			foreach ($col_row as $row => $col_row_ary)
			{
				$tbody = intdiv($row, $tbody_row_count);

				if ($tbody > $repeated_header_count)
				{
					$tbody = $repeated_header_count;
				}

				$tbody_changed_ary[$tbody] = true;

				$tbody_start_row = $tbody_row_count * $tbody;
				$tbody_row = $row - $tbody_start_row;

				if (isset($col_row_ary['clear']))
				{
					unset($tbody_row_taken_ary[$tbody][$tbody_row]);
					continue;
				}

				if (isset($col_row_ary['last_table_col']))
				{
					continue;
				}

				$tbody_row_taken_ary[$tbody][$tbody_row] = true;

				if (!isset($tbody_start_event_ary[$tbody]))
				{
					$tbody_start_event_ary[$tbody] = [];
				}

				$tbody_start_event_ary[$tbody][$tbody_row] = $col_row_ary;
			}

			if ($is_last_table_col)
			{
				for($tbody = 0; $tbody < $tbody_count; $tbody++)
				{
					$is_last_tbody = $repeated_header_count === $tbody;
					$current_tbody_row_count = $is_last_tbody ? $last_tbody_row_count : $tbody_row_count;

					for ($tbody_row = 0; $tbody_row < $current_tbody_row_count; $tbody_row++)
					{
						if (isset($tbody_start_event_ary[$tbody][$tbody_row]))
						{
							$tbody_ary[$tbody][$tbody_row][$col] = $tbody_start_event_ary[$tbody][$tbody_row];
							continue;
						}

						if (isset($tbody_row_taken_ary[$tbody][$tbody_row]))
						{
							continue;
						}

						$tbody_ary[$tbody][$tbody_row][$col] = [
							'rowspan'	=> 1,
						];
					}
				}

				continue;
			}

			foreach($tbody_changed_ary as $tbody => $bool)
			{
				if (!isset($tbody_ary[$tbody]))
				{
					$tbody_ary[$tbody] = [];
				}

				$is_last_tbody = $tbody === $repeated_header_count;
				$tbody_limit_row = $is_last_tbody ? $last_tbody_row_count : $tbody_row_count;

				$rowspan = 0;
				$rowspan_tbody_start_row = 0;

				for ($tbody_row = 0; $tbody_row < $tbody_limit_row; $tbody_row++)
				{
					if (isset($tbody_row_taken_ary[$tbody][$tbody_row]))
					{
						if ($rowspan)
						{
							$tbody_ary[$tbody][$rowspan_tbody_start_row][$col] = [
								'rowspan'	=> $rowspan,
							];

							$rowspan = 0;
						}

						if (isset($tbody_start_event_ary[$tbody][$tbody_row]))
						{
							$tbody_ary[$tbody][$tbody_row][$col] = $tbody_start_event_ary[$tbody][$tbody_row];
						}

						$rowspan_tbody_start_row = $tbody_row + 1;

						continue;
					}

					$tbody_ary[$tbody][$tbody_row][$col] = [
						'clear'		=> true,
					];

					$rowspan++;
				}

				if ($rowspan)
				{
					if (!isset($tbody_ary[$tbody][$rowspan_tbody_start_row]))
					{
						$tbody_ary[$tbody][$rowspan_tbody_start_row] = [];
					}

					$tbody_ary[$tbody][$rowspan_tbody_start_row][$col] = [
						'rowspan'	=> $rowspan,
					];
				}
			}
		}

		$tbody_rowspan_cache = [];

		for ($table = 0; $table < $table_count; $table++)
		{
			$this->template->assign_block_vars('tables', []);

			$table_start = $table_day_count * $table;
			$table_next = $table_day_count + $table_start;

			$new_table = true;

			for ($col = $table_start; $col < $table_next; $col++)
			{
				$max_table_span = $table_next - $col;

				$jd = $start_jd + $col;
				$date_ary = cal_from_jd($jd, CAL_GREGORIAN);

				if ($date_ary['dayname'] === 'Monday' || $new_table)
				{
					$isoweek = gmdate('W', jdtounix($jd));
				}

				if ($date_ary['day'] === 1 || $new_table)
				{
					$month_day_count = cal_days_in_month(CAL_GREGORIAN, $date_ary['month'], $date_ary['year']);
					$month_span = min($month_day_count - $date_ary['day'] + 1, $max_table_span);

					$month_abbrev = $date_ary['abbrevmonth'] === 'May' ? 'May_short' : $date_ary['abbrevmonth'];

					// colgroups ~ months
					$month_tpl = [
						'MONTH'				=> $date_ary['month'],
						'MONTH_NAME'		=> $this->language->lang(['datetime', $date_ary['monthname']]),
						'MONTH_ABBREV'		=> $this->language->lang(['datetime', $month_abbrev]),
						'MONTH_CLASS'		=> strtolower($date_ary['abbrevmonth']),
						'YEAR'				=> $date_ary['year'],
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

				$weekday_name = $this->language->lang(['datetime', $date_ary['dayname']]);
				$weekday_abbrev = substr($weekday_name, 0, $weekday_max_char_count);

				// cols ~ days
				$day_tpl = [
					'COL'				=> $col,
					'JD'				=> $jd,
					'WEEKDAY'			=> $date_ary['dow'],
					'WEEKDAY_NAME'		=> $weekday_name,
					'WEEKDAY_ABBREV'	=> $weekday_abbrev,
					'WEEKDAY_CLASS'		=> strtolower($date_ary['abbrevdayname']),
					'MONTHDAY'			=> $date_ary['day'],
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

			for ($tbody = 0; $tbody < $tbody_count; $tbody++)
			{
				$this->template->assign_block_vars('tables.tbodies', []);

				$is_last_tbody = $tbody === $repeated_header_count;
				$tbody_limit_row = $is_last_tbody ? $last_tbody_row_count : $tbody_row_count;

				if (!isset($tbody_rowspan_cache[$tbody]))
				{
					$tbody_rowspan_cache[$tbody] = [];
				}

				for ($tbody_row = 0; $tbody_row < $tbody_limit_row; $tbody_row++)
				{
					$this->template->assign_block_vars('tables.tbodies.rows', []);

					for ($col = $table_start; $col < $table_next; $col++)
					{
						if (isset($tbody_ary[$tbody][$tbody_row][$col]))
						{
							if (isset($tbody_ary[$tbody][$tbody_row][$col]['topic_id']))
							{
								$topic = $topic_ary[$tbody_ary[$tbody][$tbody_row][$col]['topic_id']];

								$this->template->assign_block_vars('tables.tbodies.rows.cells', [
									'COLSPAN'		=> $tbody_ary[$tbody][$tbody_row][$col]['colspan'],
									'TOPIC_ID'		=> $topic['topic_id'],
									'FORUM_ID'		=> $topic['forum_id'],
									'TOPIC_TITLE'	=> $topic['topic_title'],
									'TOPIC_LINK'	=> $topic['link'],
								]);

								unset($tbody_rowspan_cache[$tbody][$tbody_row]);

								continue;
							}

							if (isset($tbody_ary[$tbody][$tbody_row][$col]['clear']))
							{
								unset($tbody_rowspan_cache[$tbody][$tbody_row]);
								continue;
							}

							$tbody_rowspan_cache[$tbody][$tbody_row] = $tbody_ary[$tbody][$tbody_row][$col]['rowspan'];
						}

						if (isset($tbody_rowspan_cache[$tbody][$tbody_row]))
						{
							$this->template->assign_block_vars('tables.tbodies.rows.cells', [
								'ROWSPAN'	=> $tbody_rowspan_cache[$tbody][$tbody_row],
							]);
						}
					}
				}
			}
		}

		if ($this->store->get_nav_en())
		{
			$nav_month_max_char_count = $this->store->get_nav_month_max_char_count();
			$nav_month_count = $this->store->get_nav_month_count();
			$nav_months_pre = round(($nav_month_count - ($day_count / 30)) / 2);

			$nav_month = $month - $nav_months_pre;
			$nav_year = $year;

			while ($nav_month < 1)
			{
				$nav_year--;
				$nav_month += 12;
			}
		}
		else
		{
			$nav_month_count = 0;
		}

		for ($n = 0; $n < $nav_month_count; $n++)
		{
			if ($nav_month > 12)
			{
				$nav_month = 1;
				$nav_year++;
			}

			$this->template->assign_block_vars('nav_links', [
				'LINK'	 => $this->helper->route('marttiphpbb_calendartableview_page_controller', [
					'year'	=> $nav_year,
					'month'	=> $nav_month,
					'day'	=> 1,
				]),
			]);

			$this->template->assign_block_vars('nav_links', [
				'LINK'	 => $this->helper->route('marttiphpbb_calendartableview_page_controller', [
					'year'	=> $nav_year,
					'month'	=> $nav_month,
					'day'	=> 7,
				]),
			]);

			$this->template->assign_block_vars('nav_links', [
				'LINK'	 => $this->helper->route('marttiphpbb_calendartableview_page_controller', [
					'year'	=> $nav_year,
					'month'	=> $nav_month,
					'day'	=> 15,
				]),
			]);

			$this->template->assign_block_vars('nav_links', [
				'LINK'	 => $this->helper->route('marttiphpbb_calendartableview_page_controller', [
					'year'	=> $nav_year,
					'month'	=> $nav_month,
					'day'	=> 23,
				]),
			]);

			$month_str = cnst::MONTH_NAME[$nav_month];
			$month_name = $this->language->lang(['datetime', $month_str]);

			$this->template->assign_block_vars('nav_months', [
				'MONTH'	 		=> $nav_month,
				'MONTH_NAME'	=> $month_name,
				'MONTH_ABBREV'	=> substr($month_name, 0, $nav_month_max_char_count),
				'MONTH_CLASS'	=> cnst::MONTH_CLASS[$nav_month],
			]);

			$nav_month++;
		}

		$nav_select_start = 50;
		$nav_select_count = 16;

		$this->template->assign_vars([
			'TODAY_JD'			=> $today_jd,
			'TOPIC_HILIT'		=> $this->request->variable('t', 0),
			'SHOW_TODAY'		=> $this->store->get_show_today(),
			'LOAD_STYLESHEET'	=> $this->store->get_load_stylesheet(),
			'EXTRA_STYLESHEET'	=> $this->store->get_extra_stylesheet(),
			'NAV_SELECT_START'	=> $nav_select_start,
			'NAV_SELECT_COUNT'	=> $nav_select_count,
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
