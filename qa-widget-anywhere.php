<?php

class qa_widget_anywhere
{
	private $directory;
	private $urltoroot;

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

	function init_queries($tableslc)
	{
		$tablename = qa_db_add_table_prefix('widanywhere');

		if ( !in_array($tablename, $tableslc) )
		{
			// TODO: index position, ordering and any other necessary fields
			return 'CREATE TABLE IF NOT EXISTS ^widanywhere ('.
				'`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,'.
				'`title` varchar(30) NOT NULL,'.
				'`pages` varchar(800) NOT NULL,'.
				'`position` varchar(30) NOT NULL,'.
				'`ordering` smallint(5) unsigned NOT NULL,'.
				'`content` text NOT NULL,'.
				'PRIMARY KEY (`id`)'.
			') ENGINE=InnoDB DEFAULT CHARSET=utf8';
		}

		return null;
	}

	function admin_form(&$qa_content)
	{
		$saved_msg = null;
		$wdaw_1 = '<b>some test html</b>';

		if ( qa_clicked('wdaw_save_button') )
		{
			$saved_msg = 'Clicked the button :)';
		}


		$sql = 'SELECT id, title, pages, position FROM ^widanywhere ORDER BY position, ordering';
		$result = qa_db_query_sub($sql);
		$widgets = qa_db_read_all_assoc($result);

		$custom = '';
		foreach ( $widgets as $w )
		{
			$custom .= '<p><a href="#"><b>' . $w['title'] . '</b></a> - at <em>' . $w['position'] . '</em> on <tt>' . $w['pages'] . '</tt></p>';
		}


		return array(
			'ok' => $saved_msg,

			'fields' => array(
// 				array(
// 					'id' => 'wdaw_content_1',
// 					'label' => 'HTML content',
// 					'tags' => 'name="wdaw_content_1"',
// 					'value' => $wdaw_1,
// 					'type' => 'textarea',
// 					'rows' => 10,
// 				),
//
// 				array(
// 					'id' => 'wdaw_position_1',
// 					'label' => 'Position',
// 					'tags' => 'name="wdaw_position_1"',
// 					'type' => 'select',
// 					'options' => qa_admin_place_options(),
// 					'value' => '',
// 				),
//
// 				array(
// 					'id' => 'wdaw_pages_1',
// 					'label' => 'Show on these pages',
// 					'tags' => 'name="wdaw_pages_1"',
// 					'type' => 'select',
// 					'options' => $this->templatelangkeys,
// 					'value' => '',
// 				),

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
