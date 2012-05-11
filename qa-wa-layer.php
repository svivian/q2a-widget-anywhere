<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	private $widgets = array();
	private $pluginkey = 'widgetanyw';
	private $opt = 'widgetanyw_active';

	function doctype()
	{
		// TODO: grab all widgets for this template from database
		// SELECT * FROM ^widanywhere WHERE pages LIKE '%{template}%'
		// explode(pages);

		if ( qa_opt($this->opt) === '1' )
		{
			$sql = 'SELECT * FROM ^'.$this->pluginkey.' WHERE pages="all" OR pages LIKE $';
			$result = qa_db_query_sub($sql, '%'.$this->template.'%');
			$this->widgets = qa_db_read_all_assoc($result);
		}

		parent::doctype();
	}

	function head_custom()
	{
		parent::head_custom();

		// TODO: position inside <head> tag
	}

	function q_view($q_view)
	{
		// TODO: position before question

		parent::q_view($q_view);

		// TODO: position after question
		foreach ( $this->widgets as $w )
		{
			if ( $w['position'] == 'test' )
			{
			
			}
		}
		
	}

	function a_list($a_list)
	{
		// TODO: position after first answer?

		parent::a_list($a_list);

		// TODO: position after all answers
	}

	// testing function
	private function _debug($s)
	{
		echo '<pre align="left">' . print_r($s,true) . '</pre>';
	}

}
