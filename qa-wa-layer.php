<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	private $widgets = array();
	private $dbtable = 'widgetanyw';

	function doctype()
	{
		// TODO: grab all widgets for this template from database
		// SELECT * FROM ^widanywhere WHERE pages LIKE '%{template}%'
		// explode(pages);

		if ( qa_opt('wdaw_active') === '1' )
		{
			$sql = 'SELECT * FROM ^'.$this->dbtable.' WHERE pages LIKE "%question%"';
			$result = qa_db_query_sub($sql);
			$this->widgets = qa_db_read_all_assoc($result);
		}

		parent::doctype();
	}

	function head_custom()
	{
		parent::head_custom();

		// TODO: position inside <head> tag
	}

	function q_view()
	{
		// TODO: position just before question

		parent::q_view();

		// TODO: position just after question
	}

	function a_list()
	{
		// TODO: position after first answer?

		parent::a_list();

		// TODO: position just after answers
	}


}
