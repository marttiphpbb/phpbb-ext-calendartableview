<?php

/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\controller;

use marttiphpbb\calendartableview\service\user_today;
use phpbb\controller\helper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class redirect
{
	protected $user_today;
	protected $helper;

	public function __construct(
		user_today $user_today,
		helper $helper
	)
	{
		$this->user_today = $user_today;
		$this->helper = $helper;
	}

	public function to_now():Response
	{
		$now = $this->user_today->get_date();

		$link = $this->helper->route('marttiphpbb_calendartableview_page_controller', [
			'year'	=> $now['year'],
			'month'	=> $now['mon'],
		]);

		return RedirectResponse::create($link);
	}

	public function to_year(int $year):Response
	{
		$link = $this->helper->route('marttiphpbb_calendartableview_page_controller', [
			'year'	=> $year,
			'month'	=> 1,
		]);

		return RedirectResponse::create($link);
	}
}
