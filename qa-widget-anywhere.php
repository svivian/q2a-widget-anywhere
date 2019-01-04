<?php
/*
	Question2Answer Widget Anywhere plugin
	Copyright (C) 2012 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

class qa_widget_anywhere
{
	private $directory;
	private $urltoroot;
	private $pluginkey = 'widgetanyw';

	private $titleLength = 30;

	private $positionlangs = [
		// custom positions
		'head-tag' => 'widgetanywhere/place_head',
		'q-item-after' => 'widgetanywhere/place_after_q',

		// standard Q2A positions
		'full-top' => 'options/place_full_top',
		'full-high' => 'options/place_full_below_nav',
		'full-low' => 'options/place_full_below_content',
		'full-bottom' => 'options/place_full_below_footer',
		'main-top' => 'options/place_main_top',
		'main-high' => 'options/place_main_below_title',
		'main-low' => 'options/place_main_below_lists',
		'main-bottom' => 'options/place_main_bottom',
		'side-top' => 'options/place_side_top',
		'side-high' => 'options/place_side_below_sidebar',
		'side-low' => 'options/place_side_below_categories',
		'side-bottom' => 'options/place_side_last',
	];

	// copied from qa-page-admin-widgets.php
	private $templatelangkeys = [
		'question' => 'admin/question_pages',
		'qa' => 'main/recent_qs_as_title',
		'activity' => 'main/recent_activity_title',
		'questions' => 'admin/question_lists',
		'hot' => 'main/hot_qs_title',
		'unanswered' => 'main/unanswered_qs_title',
		'tags' => 'main/popular_tags',
		'categories' => 'misc/browse_categories',
		'users' => 'main/highest_users',
		'ask' => 'question/ask_title',
		'tag' => 'admin/tag_pages',
		'user' => 'admin/user_pages',
		'message' => 'misc/private_message_title',
		'search' => 'main/search_title',
		'feedback' => 'misc/feedback_title',
		'login' => 'users/login_title',
		'register' => 'users/register_title',
		'account' => 'profile/my_account_title',
		'favorites' => 'misc/my_favorites_title',
		'updates' => 'misc/recent_updates_title',
		'ip' => 'admin/ip_address_pages',
		'admin' => 'admin/admin_title',
	];

	public function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;

		// set up position list
		foreach ($this->positionlangs as $pos => $langkey) {
			$this->positionlangs[$pos] = qa_lang_html($langkey);
		}
	}

	public function match_request($request)
	{
		return $request == 'admin/'.$this->pluginkey;
	}

	public function init_queries($tableslc)
	{
		$tablename = qa_db_add_table_prefix($this->pluginkey);

		if (!in_array($tablename, $tableslc)) {
			return 'CREATE TABLE IF NOT EXISTS ^'.$this->pluginkey.' ( '.
				'`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT, '.
				'`title` varchar(' . $this->titleLength . ') NOT NULL, '.
				'`pages` varchar(800) NOT NULL, '.
				'`position` varchar(30) NOT NULL, '.
				'`ordering` smallint(5) unsigned NOT NULL, '.
				'`content` text NOT NULL, '.
				'PRIMARY KEY (`id`), '.
				'KEY `position` (`position`,`ordering`) '.
			' ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		}

		// we're already set up
		return null;
	}

	public function process_request($request)
	{
		// double check we are admin
		if (qa_get_logged_in_level() < QA_USER_LEVEL_ADMIN)
			return;

		if (qa_clicked('docancel'))
			qa_redirect('admin/plugins');

		$qa_content = qa_content_prepare();
		$qa_content['title'] = qa_lang_html('widgetanywhere/plugin_name');
		$optionsUrl = qa_admin_module_options_path('page', 'Widget Anywhere');
		$qa_content['custom'] = '<p><a href="' . $optionsUrl . '">&lsaquo; ' . qa_lang_html('widgetanywhere/link_options') . '</a></p>';

		$saved_msg = null;
		$editid = qa_get('editid');
		$errors = [];

		if (qa_post_text('dodelete')) {
			$this->delete_widget();
			qa_redirect('admin/plugins');
		} elseif (qa_clicked('save_button')) {
			// save widget
			$widget = $this->save_widget($errors);
			if (empty($errors))
				$saved_msg = qa_lang_html('widgetanywhere/alert_saved');
		} elseif (empty($editid)) {
			// display blank form
			$widget = [
				'id' => 0,
				'title' => '',
				'pages' => '',
				'position' => '',
				'ordering' => 1,
				'content' => '',
			];
		} else {
			// load specified widget
			$sql = 'SELECT * FROM ^'.$this->pluginkey.' WHERE id=#';
			$result = qa_db_query_sub($sql, $editid);
			$widget = qa_db_read_one_assoc($result);
		}

		$positionKey = isset($widget['position']) ? $widget['position'] : null;
		$selectedPosition = isset($this->positionlangs[$positionKey]) ? $this->positionlangs[$positionKey] : null;

		// set up page (template) list
		$widget_pages = explode(',', $widget['pages']);

		$sel_pages = [];
		$custom_pages = [];
		foreach ($widget_pages as $page) {
			if (strpos($page, 'custom:') === 0) {
				$custom_pages[] = substr($page, 7);
			} else {
				$sel_pages[] = $page;
			}
		}

		$pages_html = '';
		foreach ($this->templatelangkeys as $tmpl => $langkey) {
			$checked = in_array($tmpl, $sel_pages) ? 'checked' : '';
			$pages_html .= '<label><input type="checkbox" name="wpages_' . $tmpl . '" ' . $checked . '> ' . qa_lang_html($langkey) . '</label><br>';
		}

		$qa_content['form'] = [
			'tags' => 'method="post" action="'.qa_self_html().'"',
			'style' => 'tall',
			'ok' => $saved_msg,

			'fields' => [
				'title' => [
					'label' => qa_lang_html('widgetanywhere/label_title'),
					'tags' => 'name="wtitle"',
					'value' => qa_html($widget['title']),
					'error' => isset($errors['title']) ? qa_html($errors['title']) : '',
				],

				'position' => [
					'type' => 'select',
					'label' => qa_lang_html('admin/position'),
					'tags' => 'name="wposition"',
					'options' => $this->positionlangs,
					'value' => $selectedPosition,
				],

				'all_pages' => [
					'type' => 'checkbox',
					'id' => 'tb_pages_all',
					'label' => qa_lang_html('admin/widget_all_pages'),
					'tags' => 'name="wpages_all" ID="wpages_all"',
					'value' => in_array('all', $sel_pages),
				],

				'pages' => [
					'type' => 'custom',
					'id' => 'tb_pages_list',
					'label' => qa_lang_html('admin/widget_pages_explanation'),
					'html' => $pages_html,
				],

				'show_custom_pages' => [
					'type' => 'checkbox',
					'id' => 'tb_show_custom_pages',
					'label' => qa_lang_html('widgetanywhere/label_custom'),
					'tags' => 'name="cb_custom_pages" ID="cb_custom_pages"',
					'value' => count($custom_pages) > 0,
				],
				'custom_pages' => [
					'id' => 'tb_custom_pages',
					'label' => qa_lang_html('widgetanywhere/label_slugs'),
					'tags' => 'name="wpages_custom"',
					'value' => qa_html(implode(',', $custom_pages)),
					'note' => preg_replace('/`(.+?)`/', '<code>$1</code>', qa_lang_html('widgetanywhere/note_slugs')),
				],

				'ordering' => [
					'type' => 'number',
					'label' => qa_lang_html('widgetanywhere/label_order'),
					'tags' => 'name="wordering"',
					'value' => qa_html($widget['ordering']),
				],

				'content' => [
					'type' => 'textarea',
					'label' => qa_lang_html('widgetanywhere/label_content'),
					'tags' => 'name="wcontent"',
					'value' => qa_html($widget['content']),
					'rows' => 12,
				],
			],

			'hidden' => [
				'wid' => $widget['id'],
			],

			'buttons' => [
				'save' => [
					'tags' => 'name="save_button"',
					'label' => qa_lang_html('widgetanywhere/button_save'),
					'value' => '1',
				],

				'cancel' => [
					'tags' => 'name="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				],
			],
		];

		if ($widget['id'] > 0) {
			$qa_content['form']['fields']['delete'] = [
				'tags' => 'name="dodelete"',
				'label' => qa_lang_html('widgetanywhere/label_delete'),
				'value' => 0,
				'type' => 'checkbox',
			];
		}

		qa_set_display_rules($qa_content, [
			'tb_pages_list' => '!wpages_all',
			'tb_show_custom_pages' => '!wpages_all',
			'tb_custom_pages' => 'cb_custom_pages && !wpages_all',
		]);

		return $qa_content;
	}

	public function admin_form(&$qa_content)
	{
		// plugin is active, so show list of current widgets
		$sql = 'SELECT id, title, pages, position FROM ^'.$this->pluginkey.' ORDER BY position, ordering';
		$result = qa_db_query_sub($sql);
		$widgets = qa_db_read_all_assoc($result);

		$urlbase = 'admin/' . $this->pluginkey;
		$custom = "<ul>\n";
		foreach ($widgets as $w) {
			$param = ['editid' => $w['id']];
			$p = $w['position'];
			$posit = isset($this->positionlangs[$p]) ? $this->positionlangs[$p] : '[none]';
			$custom .= '<li>';
			$custom .= '<b>' . qa_html($w['title']) . '</b>';
			$custom .= ' - <a href="' . qa_path($urlbase, $param) . '">' . qa_html($posit) . '</a>';
			$custom .= '</li>'."\n";
		}
		$custom .= "</ul>\n";
		$custom .= '<p><a href="' . qa_path($urlbase) . '">' . qa_lang_html('widgetanywhere/link_new') . '</a></p>'."\n";

		return [
			'fields' => [
				[
					'type' => 'custom',
					'html' => $custom,
				],
			],
		];
	}

	private function save_widget(&$errors)
	{
		$widget = [
			'id' => qa_post_text('wid'),
			'title' => qa_post_text('wtitle'),
			'position' => qa_post_text('wposition'),
			'ordering' => qa_post_text('wordering'),
			'content' => qa_post_text('wcontent'),
		];

		$pages = [];
		if (qa_post_text('wpages_all')) {
			$pages[] = 'all';
		} else {
			foreach ($this->templatelangkeys as $key => $lang) {
				if (qa_post_text('wpages_'.$key))
					$pages[] = $key;
			}

			if (qa_post_text('cb_custom_pages')) {
				$wpages_custom = explode(',', qa_post_text('wpages_custom'));
				foreach ($wpages_custom as $cp) {
					$pages[] = 'custom:'.$cp;
				}
			}
		}
		$widget['pages'] = implode(',', $pages);

		if (strlen($widget['title']) > $this->titleLength) {
			$errors['title'] = qa_lang_html_sub('widgetanywhere/error_title_length', $this->titleLength);
			return $widget;
		}

		if ($widget['id'] === '0') {
			$sql = 'INSERT INTO ^'.$this->pluginkey.' (id, title, pages, position, ordering, content) VALUES (0, $, $, $, #, $)';
			$success = qa_db_query_sub($sql, $widget['title'], $widget['pages'], $widget['position'], $widget['ordering'], $widget['content']);
			$widget['id'] = qa_db_last_insert_id();
		} else {
			$sql = 'UPDATE ^'.$this->pluginkey.' SET title=$, pages=$, position=$, ordering=#, content=$ WHERE id=#';
			$success = qa_db_query_sub($sql, $widget['title'], $widget['pages'], $widget['position'], $widget['ordering'], $widget['content'], $widget['id']);
		}

		return $widget;
	}

	private function delete_widget()
	{
		$wid = qa_post_text('wid');
		$sql = 'DELETE FROM ^'.$this->pluginkey.' WHERE id=#';

		return qa_db_query_sub($sql, $wid);
	}
}
