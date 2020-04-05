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
					$store->set_days_offset_menu($request->variable('days_offset_menu', 0));
					$store->set_days_offset_tag($request->variable('days_offset_tag', 0));
					$store->set_days_offset_link($request->variable('days_offset_link', 0));
					$store->set_show_today();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$menuitems_acp->assign_to_template(cnst::FOLDER);

				$template->assign_vars([
					'DAYS_OFFSET_MENU'		=> $store->get_days_offset_menu(),
					'DAYS_OFFSET_TAG'		=> $store->get_days_offset_tag(),
					'DAYS_OFFSET_LINK'		=> $store->get_days_offset_link(),
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

					$store->set_table_count($request->variable('table_count', 0));
					$store->set_table_day_count($request->variable('table_day_count', 0));
					$store->set_min_row_count($request->variable('min_row_count', 0));
					$store->set_max_row_count($request->variable('max_row_count', 0));

					$store->set_show_today($request->variable('show_today', 0) ? true : false);
					$store->set_topic_hilit($request->variable('topic_hilit', 0) ? true : false);

					$store->set_header_en($request->variable('header_en', 0) ? true : false);
					$store->set_header($header);

					$store->set_repeated_header_en($request->variable('repeated_header_en', 0) ? true : false);
					$store->set_repeated_header_row_count($request->variable('repeated_header_row_count', 0));
					$store->set_repeated_header_omit_row_count($request->variable('repeated_header_omit_row_count', 0));
					$store->set_repeated_header($repeated_header);

					$store->set_footer_en($request->variable('footer_en', 0) ? true : false);
					$store->set_footer($footer);

					$store->set_weekday_max_char_count($request->variable('weekday_max_char_count', 0));

					$store->set_nav_en($request->variable('nav_en', 0) ? true : false);
					$store->set_nav_month_count($request->variable('nav_month_count', 0));
					$store->set_nav_month_max_char_count($request->variable('nav_month_max_char_count', 0));

					$store->set_derive_user_time_format($request->variable('derive_user_time_format', 0) ? true : false);
					$store->set_default_time_format($request->variable('default_time_format', ''));
					$store->set_load_stylesheet($request->variable('load_stylesheet', 0) ? true : false);
					$store->set_extra_stylesheet($request->variable('extra_stylesheet', ''));

					$store->transaction_end();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'TABLE_COUNT'						=> $store->get_table_count(),
					'TABLE_DAY_COUNT'					=> $store->get_table_day_count(),
					'MIN_ROW_COUNT'						=> $store->get_min_row_count(),
					'MAX_ROW_COUNT'						=> $store->get_max_row_count(),
					'SHOW_TODAY'						=> $store->get_show_today(),
					'TOPIC_HILIT'						=> $store->get_topic_hilit(),
					'HEADER_EN'							=> $store->get_header_en(),
					'REPEATED_HEADER_EN'				=> $store->get_repeated_header_en(),
					'REPEATED_HEADER_ROW_COUNT'			=> $store->get_repeated_header_row_count(),
					'REPEATED_HEADER_OMIT_ROW_COUNT'	=> $store->get_repeated_header_omit_row_count(),
					'FOOTER_EN'							=> $store->get_footer_en(),
					'WEEKDAY_MAX_CHAR_COUNT'			=> $store->get_weekday_max_char_count(),
					'NAV_EN'							=> $store->get_nav_en(),
					'NAV_MONTH_COUNT'					=> $store->get_nav_month_count(),
					'NAV_MONTH_MAX_CHAR_COUNT'			=> $store->get_nav_month_max_char_count(),
					'DERIVE_USER_TIME_FORMAT'			=> $store->get_derive_user_time_format(),
					'DEFAULT_TIME_FORMAT'				=> $store->get_default_time_format(),
					'LOAD_STYLESHEET'					=> $store->get_load_stylesheet(),
					'EXTRA_STYLESHEET'					=> $store->get_extra_stylesheet(),
				]);

				$stored_header_items = [
					'header'			=> $store->get_header(),
					'repeated_header' 	=> $store->get_repeated_header(),
					'footer' 			=> $store->get_footer(),
				];

				foreach ($stored_header_items as $type => $ary)
				{
					$not_used_ary = cnst::HEADER_ROWS;

					foreach($ary as $id)
					{
						if (!isset($not_used_ary[$id]))
						{
							continue;
						}

						$item = $not_used_ary[$id];

						$template->assign_block_vars($type, [
							'ID'			=> $id,
							'NAME'			=> $item['name'],
							'WILL_MERGE'	=> isset($item['will_merge']),
							'BLOCKS_MERGE'	=> isset($item['blocks_merge']),
						]);

						unset($not_used_ary[$id]);
					}

					$type_not_used = $type . '_not_used';

					foreach ($not_used_ary as $id => $item)
					{
						$template->assign_block_vars($type_not_used, [
							'ID'			=> $id,
							'NAME'			=> $item['name'],
							'WILL_MERGE'	=> isset($item['will_merge']),
							'BLOCKS_MERGE'	=> isset($item['blocks_merge']),
						]);
					}
				}

			break;
		}

		$template->assign_var('U_ACTION', $this->u_action);
	}
}
