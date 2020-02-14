<?php
/**
* phpBB Extension - marttiphpbb calendartableview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendartableview\acp;

use marttiphpbb\calendartableview\util\cnst;

class main_module
{
	var $u_action;

	function main($id, $mode):void
	{
		global $phpbb_container;

		$template = $phpbb_container->get('template');
		$request = $phpbb_container->get('request');
		$ext_manager = $phpbb_container->get('ext.manager');
		$store = $phpbb_container->get('marttiphpbb.calendartableview.store');

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

					$store->transaction_start();
					$store->set_num_days_offset_menu($request->variable('num_days_offset_menu', 0));
					$store->set_num_days_offset_tag($request->variable('num_days_offset_tag', 0));
					$store->set_num_days_offset_link($request->variable('num_days_offset_link', 0));
					$store->set_show_today();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$menuitems_acp->assign_to_template(cnst::FOLDER);

				$template->assign_vars([
					'NUM_DAYS_OFFSET_MENU'		=> $store->get_num_days_offset_menu(),
					'NUM_DAYS_OFFSET_TAG'		=> $store->get_num_days_offset_tag(),
					'NUM_DAYS_OFFSET_LINK'		=> $store->get_num_days_offset_link(),
				]);

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
					$store->set_num_tables($request->variable('num_tables', 0));
					$store->set_num_days_one_table($request->variable('num_days_one_table', 0));
					$store->set_min_rows($request->variable('min_rows', 0));
					$store->set_max_rows($request->variable('max_rows', 0));
					$store->set_show_today($request->variable('show_today', 0) ? true : false);
					$store->set_topic_hilit($request->variable('topic_hilit', 0) ? true : false);

					$store->set_header_en($request->variable('header_en', 0) ? true : false);
					$store->set_repeat_header_en($request->variable('repeat_header_en', 0) ? true : false);
					$store->set_footer_en($request->variable('footer_en', 0) ? true : false);
					$store->set_derive_user_time_format($request->variable('derive_user_time_format', 0) ? true : false);
					$store->set_default_time_format($request->variable('default_time_format', ''));
					$store->set_load_stylesheet($request->variable('load_stylesheet', 0) ? true : false);
					$store->set_extra_stylesheet($request->variable('extra_stylesheet', ''));
					$store->transaction_end();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'NUM_TABLES'				=> $store->get_num_tables(),
					'NUM_DAYS_ONE_TABLE'		=> $store->get_num_days_one_table(),
					'MIN_ROWS'					=> $store->get_min_rows(),
					'MAX_ROWS'					=> $store->get_max_rows(),
					'SHOW_TODAY'				=> $store->get_show_today(),
					'TOPIC_HILIT'				=> $store->get_topic_hilit(),
					'HEADER_EN'					=> $store->get_header_en(),
					'REPEAT_HEADER_EN'			=> $store->get_repeat_header_en(),
					'FOOTER_EN'					=> $store->get_footer_en(),
					'DERIVE_USER_TIME_FORMAT'	=> $store->get_derive_user_time_format(),
					'DEFAULT_TIME_FORMAT'		=> $store->get_default_time_format(),
					'LOAD_STYLESHEET'			=> $store->get_load_stylesheet(),
					'EXTRA_STYLESHEET'			=> $store->get_extra_stylesheet(),
				]);

			break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
