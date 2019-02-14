<?php
/**
* phpBB Extension - marttiphpbb calendarmonthview
* @copyright (c) 2014 - 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarmonthview\acp;

use marttiphpbb\calendarmonthview\util\cnst;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $phpbb_container;

		$template = $phpbb_container->get('template');
		$config = $phpbb_container->get('config');
		$request = $phpbb_container->get('request');
		$ext_manager = $phpbb_container->get('ext.manager');
		$store = $phpbb_container->get('marttiphpbb.calendarmonthview.store');
		$phpbb_root_path = $phpbb_container->getParameter('core.root_path');

		$language = $phpbb_container->get('language');
		$language->add_lang('acp', cnst::FOLDER);
		add_form_key(cnst::FOLDER);

		switch($mode)
		{
			case 'links':

				$this->tpl_name = 'links';
				$this->page_title = $language->lang(cnst::L_ACP . '_LINKS');

				if (!$ext_manager->is_enabled('marttiphpbb/menuitems'))
				{
					$msg = $language->lang(cnst::L_ACP . '_MENUITEMS_NOT_ENABLED',
						'<a href="https://github.com/marttiphpbb/phpbb-ext-menuitems">',
						'</a>');
					trigger_error($msg, E_USER_WARNING);
				}

				$menuitems_acp = $phpbb_container->get('marttiphpbb.menuitems.acp');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$menuitems_acp->process_form(cnst::FOLDER, 'links');

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$menuitems_acp->assign_to_template(cnst::FOLDER);

			break;

			case 'page_rendering':

				$this->tpl_name = 'page_rendering';
				$this->page_title = $language->lang(cnst::L_ACP . '_PAGE_RENDERING');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$store->transaction_start();
					$store->set_show_today($request->variable('show_today', 0) ? true : false);
					$store->set_show_isoweek($request->variable('show_isoweek', 0) ? true : false);
					$store->set_show_moon_phase($request->variable('show_moon_phase', 0) ? true : false);
					$store->set_topic_hilit($request->variable('topic_hilit', 0) ? true : false);
					$store->set_first_weekday($request->variable('first_weekday', 0));
					$store->set_derive_user_time_format($request->variable('derive_user_time_format', 0) ? true : false);
					$store->set_default_time_format($request->variable('default_time_format', ''));
					$store->set_min_rows($request->variable('min_rows', 0));
					$store->set_max_rows($request->variable('max_rows', 0));
					$store->set_pag_neighbours($request->variable('pag_neighbours', 0));
					$store->set_pag_show_prev_next($request->variable('pag_show_prev_next', 0) ? true : false);
					$store->set_load_stylesheet($request->variable('load_stylesheet', 0) ? true : false);
					$store->set_extra_stylesheet($request->variable('extra_stylesheet', ''));
					$store->transaction_end();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'SHOW_TODAY'				=> $store->get_show_today(),
					'SHOW_ISOWEEK'				=> $store->get_show_isoweek(),
					'SHOW_MOON_PHASE'			=> $store->get_show_moon_phase(),
					'TOPIC_HILIT'				=> $store->get_topic_hilit(),
					'FIRST_WEEKDAY'				=> $store->get_first_weekday(),
					'DERIVE_USER_TIME_FORMAT'	=> $store->get_derive_user_time_format(),
					'DEFAULT_TIME_FORMAT'		=> $store->get_default_time_format(),
					'MIN_ROWS'					=> $store->get_min_rows(),
					'MAX_ROWS'					=> $store->get_max_rows(),
					'PAG_NEIGHBOURS'			=> $store->get_pag_neighbours(),
					'PAG_SHOW_PREV_NEXT'		=> $store->get_pag_show_prev_next(),
					'LOAD_STYLESHEET'			=> $store->get_load_stylesheet(),
					'EXTRA_STYLESHEET'			=> $store->get_extra_stylesheet(),
				]);

			break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
