<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	private $widgets = array();
	private $pluginkey = 'widgetanyw';
	private $opt = 'widgetanyw_active';

	/* Positions:
		head-tag
		header-before
		header-after
		q-item-before
		q-item-after
		*a-list-after-first
		a-list-after
	*/

	function doctype()
	{
		if ( qa_opt($this->opt) === '1' )
		{
			$sql = 'SELECT * FROM ^'.$this->pluginkey.' ORDER BY ordering';
			$widgets = qa_db_read_all_assoc( qa_db_query_sub($sql) );

			foreach ( $widgets as $wid )
			{
				$wid['pages'] = explode(',', @$wid['pages']);
				if ( $wid['pages'][0] == 'all' || in_array( $this->template, $wid['pages'] ) )
					$this->widgets[] = $wid;
			}

			// $this->_debug($this->widgets);
		}

		parent::doctype();
	}

	function head_custom()
	{
		parent::head_custom();

		// position inside <head> tag
		$this->_output_widget('head-tag');
	}

	function header()
	{
		// position before header
		$this->_output_widget('header-before');

		parent::header();

		// position after header
		$this->_output_widget('header-after');
	}

	function q_view($q_view)
	{
		// position before question
		$this->_output_widget('q-item-before');

		parent::q_view($q_view);

		// position after question
		$this->_output_widget('q-item-after');
	}

	function a_list($a_list)
	{
		// TODO: position after first answer?

		parent::a_list($a_list);

		// position after all answers
		$this->_output_widget('a-list-after');
	}

	function sidebar()
	{
		// position at top of sidepanel
		$this->_output_widget('sidepanel-top');

		parent::sidebar();
	}

	function feed()
	{
		parent::feed();

		// position at bottom of sidepanel
		$this->_output_widget('sidepanel-bottom');
	}

	/*function widgets($region, $place)
	{
		if (count(@$this->content['widgets'][$region][$place]))
		{
			$this->output('<DIV CLASS="qa-widgets-'.$region.' qa-widgets-'.$region.'-'.$place.'">');

			foreach ($this->content['widgets'][$region][$place] as $module)
			{
				$this->output('<DIV CLASS="qa-widget-'.$region.' qa-widget-'.$region.'-'.$place.'">');
				$module->output_widget($region, $place, $this, $this->template, $this->request, $this->content);
				$this->output('</DIV>');
			}

			$this->output('</DIV>', '');
		}
	}*/


	// outputs all widgets for specified position
	private function _output_widget( $pos )
	{
		foreach ( $this->widgets as $wid )
		{
			if ( $wid['position'] === $pos )
				$this->output( $wid['content'] );
		}
	}

	// testing function
	private function _debug($s)
	{
		echo '<pre align="left">' . print_r($s,true) . '</pre>';
	}

}
