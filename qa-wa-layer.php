<?php

class qa_html_theme_layer extends qa_html_theme_base
{

	function doctype()
	{
		// TODO: grab all widgets for this template from database
		// SELECT * FROM ^widanywhere WHERE pages LIKE '%{template}%'
		// explode(pages);


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
