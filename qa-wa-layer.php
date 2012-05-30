<?php
/*
	Question2Answer Widget Anywhere plugin, v1.0
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	private $widgets = array();
	private $pluginkey = 'widgetanyw';
	private $opt = 'widgetanyw_active';

	/* Positions:
		head-tag
		q-item-after
		full-top, full-high, full-low, full-bottom
		main-top, main-high, main-low, main-bottom
		side-top, side-high, side-low, side-bottom

		[old]
		header-before    -> full-top
		header-after     -> full-high
		q-item-before    -> main-high
		a-list-after     -> main-low
		sidepanel-top    -> side-top
		sidepanel-bottom -> side-bottom
	*/

	function doctype()
	{
		if ( qa_opt($this->opt) === '1' )
		{
			// fetch all widgets into a basic list [TODO: maybe use position as array key]
			$sql = 'SELECT * FROM ^'.$this->pluginkey.' ORDER BY ordering';
			$widgets = qa_db_read_all_assoc( qa_db_query_sub($sql) );

			foreach ( $widgets as $wid )
			{
				$wid['pages'] = explode(',', @$wid['pages']);
				if ( $wid['pages'][0] == 'all' || in_array( $this->template, $wid['pages'] ) )
					$this->widgets[] = $wid;
			}
		}

		parent::doctype();
	}

	// most widgets now use a built-in location
	function widgets( $region, $place )
	{
		parent::widgets( $region, $place );
		$this->_output_widget( $region.'-'.$place );
	}

	function head_custom()
	{
		parent::head_custom();

		// position inside <head> tag
		$this->_output_widget('head-tag');
	}

	function q_view($q_view)
	{
		// position before question [replaced by `title-after`]
		// $this->_output_widget('q-item-before');

		parent::q_view($q_view);

		// position after question
		$this->_output_widget('q-item-after');
	}

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
