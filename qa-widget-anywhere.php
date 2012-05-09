<?php

class qa_widget_anywhere
{
	private $directory;
	private $urltoroot;
	private $dbtable = 'widgetanyw';

	private $positionlang = array(
		'head-tag' => 'Inside <head> tag',
		'header-before' => 'Before header',
		'header-after' => 'After header',
		'q-item-before' => 'Before question text',
		'q-item-after' => 'After question text',
		'a-list-after' => 'After answer list',
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
		return $request == 'admin/'.$this->dbtable;
	}

	function process_request($request)
	{
		if ( qa_get_logged_in_level() < QA_USER_LEVEL_SUPER )
			return;

		$qa_content = qa_content_prepare();
		$qa_content['title'] = 'Widget Anywhere';

		if ( qa_clicked('wdaw_save_button') )
		{
			// TODO: save widget
			$qa_content['form'] = array( 'ok' => 'Thinking about saving...' );
			return $qa_content;
		}

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
			$sql = 'SELECT * FROM ^'.$this->dbtable.' WHERE id=#';
			$result = qa_db_query_sub($sql, $editid);
			$widget = qa_db_read_one_assoc($result);
		}

		$qa_content['custom'] = '';

		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
			'style' => 'wide',
// 			'title' => 'Form title',

			'fields' => array(
				'title' => array(
					'label' => 'Title',
					'tags' => 'NAME="wdaw_title"',
					'value' => $widget['title'],
				),

				'content' => array(
					'type' => 'custom',
					'label' => 'Pages',
// 					'tags' => 'NAME="wdaw_pages"',
					'value' => '<b>some test content</b><br><input type="checkbox">',
				),

				'content' => array(
					'type' => 'textarea',
					'label' => 'Content',
					'tags' => 'NAME="wdaw_content"',
					'value' => $widget['content'],
					'rows' => 12,
				),
			),

			'buttons' => array(
				'ok' => array(
					'tags' => 'NAME="wdaw_save_button"',
					'label' => 'Save widget',
					'value' => '1',
				),
			),
		);

		return $qa_content;
	}

	function init_queries($tableslc)
	{
		$tablename = qa_db_add_table_prefix($this->dbtable);

		if ( !in_array($tablename, $tableslc) )
		{
			// qa_opt( 'wdaw_active', '1' );

			// TODO: index position, ordering and any other necessary fields
			return 'CREATE TABLE IF NOT EXISTS ^'.$this->dbtable.' ( '.
				'`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT, '.
				'`title` varchar(30) NOT NULL, '.
				'`pages` varchar(800) NOT NULL, '.
				'`position` varchar(30) NOT NULL, '.
				'`ordering` smallint(5) unsigned NOT NULL, '.
				'`content` text NOT NULL, '.
				'PRIMARY KEY (`id`)'.
			' ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		}

		return null;
	}

	function admin_form(&$qa_content)
	{
		if ( qa_opt('wdaw_active') !== '1' )
		{
			return array(
				'fields' => array(
					array(
						'type' => 'custom',
						'html' => '<p>Widget Anywhere is not set up.</p>',
					),
				),
			);
		}

		$saved_msg = null;

		if ( qa_clicked('wdaw_save_button') )
		{
			$saved_msg = 'Clicked the button :)';
		}

		$sql = 'SELECT id, title, pages, position FROM ^'.$this->dbtable.' ORDER BY position, ordering';
		$result = qa_db_query_sub($sql);
		$widgets = qa_db_read_all_assoc($result);

		$urlbase = qa_path_to_root() . 'admin/' . $this->dbtable;

		$custom = '<ul>';
		foreach ( $widgets as $w )
		{
			$custom .= '<li><a href="' . $urlbase . '?edit=' . $w['id'] . '"><b>' . $w['title'] . '</b></a> (' . $w['position'] . ')</li>';
		}

		$custom .= '</ul>';
		$custom .= '<p><a href="' . $urlbase . '">Add new widget</a></p>';


		return array(
			'ok' => $saved_msg,

			'fields' => array(
				array(
					'type' => 'custom',
					'html' => $custom,
				),
			),

			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'name="wdaw_save_button"',
				),
			),
		);
	}

}
