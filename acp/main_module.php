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

					$header_rows = $request->variable('header_rows', '');
					$header = explode(',', $header_rows);
					$repeated_header_rows = $request->variable('repeated_header_rows', '');
					$repeated_header = explode(',', $repeated_header_rows);
					$footer_rows = $request->variable('footer_rows', '');
					$footer = explode(',', $footer_rows);

					$store->transaction_start();

					$store->set_num_tables($request->variable('num_tables', 0));
					$store->set_num_days_one_table($request->variable('num_days_one_table', 0));
					$store->set_min_rows($request->variable('min_rows', 0));
					$store->set_max_rows($request->variable('max_rows', 0));

					$store->set_show_today($request->variable('show_today', 0) ? true : false);
					$store->set_topic_hilit($request->variable('topic_hilit', 0) ? true : false);

					$store->set_header_en($request->variable('header_en', 0) ? true : false);
					$store->set_header($header);

					$store->set_repeated_header_en($request->variable('repeated_header_en', 0) ? true : false);
					$store->set_repeated_header_num_rows($request->variable('repeated_header_num_rows', 0));
					$store->set_repeated_header_omit_rows($request->variable('repeated_header_omit_rows', 0));
					$store->set_repeated_header($repeated_header);

					$store->set_footer_en($request->variable('footer_en', 0) ? true : false);
					$store->set_footer($footer);

					$store->set_weekday_max_chars($request->variable('weekday_max_chars', 0));

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
					'REPEATED_HEADER_EN'		=> $store->get_repeated_header_en(),
					'REPEATED_HEADER_NUM_ROWS'	=> $store->get_repeated_header_num_rows(),
					'REPEATED_HEADER_OMIT_ROWS'	=> $store->get_repeated_header_omit_rows(),
					'FOOTER_EN'					=> $store->get_footer_en(),
					'WEEKDAY_MAX_CHARS'			=> $store->get_weekday_max_chars(),
					'DERIVE_USER_TIME_FORMAT'	=> $store->get_derive_user_time_format(),
					'DEFAULT_TIME_FORMAT'		=> $store->get_default_time_format(),
					'LOAD_STYLESHEET'			=> $store->get_load_stylesheet(),
					'EXTRA_STYLESHEET'			=> $store->get_extra_stylesheet(),
				]);

				$stored_header_items = [
					'header'			=> $store->get_header(),
					'repeated_header' 	=> $store->get_repeated_header(),
					'footer' 			=> $store->get_footer(),
				];

				foreach ($stored_header_items as $type => $ary)
				{
					$not_used = cnst::HEADER_ROWS;

					foreach($ary as $val)
					{
						[$name, $merge] = explode('.', $val);

						if (!isset($not_used[$name]))
						{
							continue;
						}

						$mergeable = isset($not_used[$name]['merge']);

						if (isset($merge))
						{
							if (!$mergeable
								|| $merge !== 'merge'
							)
							{
								unset($merge);
							}
						}

						unset($not_used[$name]);

						$template->assign_block_vars($type, [
							'NAME'			=> $name,
							'MERGEABLE'		=> $mergeable,
							'MERGE'			=> isset($merge),
						]);
					}

					$type_not_used = $type . '_not_used';

					foreach ($not_used as $name => $ary)
					{
						$template->assign_block_vars($type_not_used, [
							'NAME'			=> $name,
							'MERGEABLE'		=> isset($ary['merge']),
							'MERGE'			=> false,
						]);
					}
				}

			break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
