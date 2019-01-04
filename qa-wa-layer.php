<?php
/*
	Question2Answer Widget Anywhere plugin
	Copyright (C) 2012 Scott Vivian
	License: https://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme_layer extends qa_html_theme_base
{
	private $wanyw_widgets = array();
	private $wanyw_key = 'widgetanyw';

	/* Positions:
		head-tag
		q-item-after
		full-top, full-high, full-low, full-bottom
		main-top, main-high, main-low, main-bottom
		side-top, side-high, side-low, side-bottom
	*/

	public function initialize()
	{
		// fetch all widgets into a basic list
		$sql = 'SELECT * FROM ^'.$this->wanyw_key.' ORDER BY ordering';
		$widgets = qa_db_read_all_assoc(qa_db_query_sub($sql));

		foreach ($widgets as $widget) {
			if (strlen($widget['pages']) === 0)
				continue;

			$widget['pages'] = explode(',', $widget['pages']);
			$show_all = $widget['pages'][0] == 'all';
			$show_tmpl = in_array($this->template, $widget['pages']);
			$show_custom = in_array('custom:'.$this->request, $widget['pages']);

			if ($show_all || $show_tmpl || $show_custom)
				$this->wanyw_widgets[] = $widget;
		}

		parent::initialize();
	}

	// most widgets now use a built-in location
	public function widgets($region, $place)
	{
		parent::widgets($region, $place);
		$this->wa_output_widget($region.'-'.$place);
	}

	public function head_custom()
	{
		parent::head_custom();

		// position inside <head> tag
		$this->wa_output_widget('head-tag');
	}

	public function q_view($q_view)
	{
		parent::q_view($q_view);

		// position after question
		$this->wa_output_widget('q-item-after');
	}

	// outputs all widgets for specified position
	private function wa_output_widget($pos)
	{
		foreach ($this->wanyw_widgets as $widget) {
			if ($widget['position'] === $pos)
				$this->output($widget['content']);
		}
	}
}
