<?php

class qa_widget_anywhere
{
	private $directory;
	private $urltoroot;
	private $pluginkey = 'widgetanyw';
	private $opt = 'widgetanyw_active';

	private $positionlangs = array(
		'header-before' => 'Before header',
		'header-after' => 'After header',
		'q-item-before' => 'Before question text',
		'q-item-after' => 'After question text',
		'a-list-after-first' => 'After first answer',
		'a-list-after' => 'After answer list',
		'head-tag' => 'End of &lt;head&gt; tag',
	);

	// copied from qa-page-admin-widgets.php
	private $templatelangkeys = array(
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
	);

	public function load_module($directory, $urltoroot)
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}

	function match_request($request)
	{
		return $request == 'admin/'.$this->pluginkey;
	}

	function process_request($request)
	{
		// double check we are admin
		if ( qa_get_logged_in_level() < QA_USER_LEVEL_SUPER )
			return;

		$qa_content = qa_content_prepare();
		$qa_content['title'] = 'Widget Anywhere';
		// $qa_content['custom'] = '';

		// TODO: save widget
		if ( qa_clicked('widgetanyw_save_button') )
		{
			$qa_content['custom'] = '<pre>'.print_r($_POST, true).'</pre>';
		
		
			$qa_content['form'] = array(
				'ok' => 'Thinking about saving...',
				'style' => 'wide',
				'buttons' => array(
					'ok' => array(
						'tags' => 'NAME="widgetanyw_save_button"',
						'label' => 'Save widget',
						'value' => '1',
					),
				),
			);
			return $qa_content;
		}

		// fetch requested widget or display blank
		$editid = qa_get('edit');
		if ( empty($editid) )
		{
			$widget = array(
				'id' => 0,
				'title' => '',
				'pages' => array(),
				'position' => '',
				'ordering' => 1,
				'content' => '',
			);
		}
		else
		{
			$sql = 'SELECT * FROM ^'.$this->pluginkey.' WHERE id=#';
			$result = qa_db_query_sub($sql, $editid);
			$widget = qa_db_read_one_assoc($result);
		}


		$pages_html = '<label><input type="checkbox" name="pages[all]"> ' . qa_lang_html('admin/widget_all_pages') . '</label><br><br>';
		foreach ( $this->templatelangkeys as $tmpl=>$langkey )
		{
			$pages_html .= '<label><input type="checkbox" name="pages[' . $tmpl . ']"> ' . qa_lang_html($langkey) . '</label><br>';
		}

		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
			'style' => 'wide',

			'fields' => array(
				'title' => array(
					'label' => 'Title',
					'tags' => 'NAME="widgetanyw_title"',
					'value' => $widget['title'],
				),
				
				'position' => array(
					'type' => 'select',
					'label' => 'Position',
					'tags' => 'NAME="widgetanyw_positon"',
					'options' => $this->positionlangs,
					'value' => '',
				),

				'pages' => array(
					'type' => 'custom',
					'label' => 'Pages',
					'html' => $pages_html,
				),

				'content' => array(
					'type' => 'textarea',
					'label' => 'Content',
					'tags' => 'NAME="widgetanyw_content"',
					'value' => $widget['content'],
					'rows' => 12,
				),
			),

			'buttons' => array(
				'ok' => array(
					'tags' => 'NAME="widgetanyw_save_button"',
					'label' => 'Save widget',
					'value' => '1',
				),
			),
		);

		return $qa_content;
	}

	function admin_form(&$qa_content)
	{
		$saved_msg = null;

		// activate plugin (create database table if it doesn't exist)
		if ( qa_clicked('widgetanyw_activate_button') )
		{
			$sql_create =
				'CREATE TABLE IF NOT EXISTS ^'.$this->pluginkey.' ( '.
				'`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT, '.
				'`title` varchar(30) NOT NULL, '.
				'`pages` varchar(800) NOT NULL, '.
				'`position` varchar(30) NOT NULL, '.
				'`ordering` smallint(5) unsigned NOT NULL, '.
				'`content` text NOT NULL, '.
				'PRIMARY KEY (`id`)'.
			' ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
			$result = qa_db_query_sub($sql_create);

			if ( $result === true )
			{
				qa_opt( $this->opt, '1' );
				$saved_msg = 'Plugin activated!';
			}
		}

		// button to set up plugin
		if ( qa_opt($this->opt) !== '1' )
		{
			return array(
				'fields' => array(
					array(
						'type' => 'custom',
						'error' => 'Widget Anywhere is not set up yet.',
					),
				),
				'buttons' => array(
					array(
						'label' => 'Set up',
						'tags' => 'name="widgetanyw_activate_button"',
					),
				),
			);
		}


		// plugin is active, so show list of current widgets
		$sql = 'SELECT id, title, pages, position FROM ^'.$this->pluginkey.' ORDER BY position, ordering';
		$result = qa_db_query_sub($sql);
		$widgets = qa_db_read_all_assoc($result);

		$urlbase = qa_path_to_root() . 'admin/' . $this->pluginkey;
		$custom = '<ul>'."\n";
		foreach ( $widgets as $w )
		{
			$custom .= '<li><a href="' . $urlbase . '?edit=' . $w['id'] . '"><b>' . $w['title'] . '</b></a> (' . $w['position'] . ')</li>'."\n";
		}
		$custom .= '</ul>'."\n";
		$custom .= '<p><a href="' . qa_path('admin/' . $this->pluginkey) . '">Add new widget</a></p>'."\n";


		return array(
			'ok' => $saved_msg,

			'fields' => array(
				array(
					'type' => 'custom',
					'html' => $custom,
				),
			),
		);
	}

}
