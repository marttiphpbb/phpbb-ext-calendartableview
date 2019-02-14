<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\service;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\template\template;
use phpbb\language\language;

class pagination
{
	protected $config;
	protected $helper;
	protected $template;
	protected $language;

	static protected $month_abbrev = [
		1	=> 'Jan',
		2	=> 'Feb',
		3	=> 'Mar',
		4	=> 'Apr',
		5	=> 'May_short',
		6	=> 'Jun',
		7	=> 'Jul',
		8	=> 'Aug',
		9	=> 'Sep',
		10	=> 'Oct',
		11	=> 'Nov',
		12	=> 'Dec',
	];

	public function __construct(
		config $config,
		helper $helper,
		template $template,
		language $language
	)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->language = $language;
	}

	public function render(int $year, int $month, bool $show_prev_next, int $num_neighbours):void
	{
		if ($show_prev_next)
		{
			$this->template->assign_block_vars('pagination', [
				'S_IS_PREV'		=> true,
				'PAGE_URL'		=> $this->helper->route('marttiphpbb_calendarmonthview_page_controller', [
					'year' 	=> ($month == 1) ? $year - 1 : $year,
					'month'	=> ($month == 1) ? 12 : $month - 1,
				]),
			]);
		}

		$min = -$num_neighbours;
		$max = 1 + $num_neighbours;

		for ($i = $min; $i < $max; $i++)
		{
			$pag_month = $month + $i;
			$pag_year = $year;

			if ($pag_month < 1)
			{
				$pag_year--;
				$pag_month += 12;
			}
			else if ($pag_month > 12)
			{
				$pag_year++;
				$pag_month -= 12;
			}

			$this->template->assign_block_vars('pagination', [
				'S_IS_CURRENT'	=> ($i) ? false : true,
				'PAGE_NUMBER'	=> $this->language->lang(['datetime', pagination::$month_abbrev[$pag_month]]),
				'PAGE_URL'		=> $this->helper->route('marttiphpbb_calendarmonthview_page_controller', [
					'year' 	=> $pag_year,
					'month'	=> $pag_month,
				]),
			]);
		}

		if ($show_prev_next)
		{
			$this->template->assign_block_vars('pagination', [
				'S_IS_NEXT'		=> true,
				'PAGE_URL'		=> $this->helper->route('marttiphpbb_calendarmonthview_page_controller', [
					'year' 	=> ($month == 12) ? $year + 1 : $year,
					'month'	=> ($month == 12) ? 1 : $month + 1,
				]),
			]);
		}
	}
}
