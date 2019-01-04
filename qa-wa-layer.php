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
	private $wanyw_opt = 'widgetanyw_active';

	/* Positions:
		head-tag
		q-item-after
		full-top, full-high, full-low, full-bottom
		main-top, main-high, main-low, main-bottom
		side-top, side-high, side-low, side-bottom
	*/

	public function doctype()
	{
		if (qa_opt($this->wanyw_opt) === '1') {
			// fetch all widgets into a basic list
			$sql = 'SELECT * FROM ^'.$this->wanyw_key.' ORDER BY ordering';
			$widgets = qa_db_read_all_assoc(qa_db_query_sub($sql));

			foreach ($widgets as $wid) {
				$wid['pages'] = explode(',', @$wid['pages']);
				$show_all = $wid['pages'][0] == 'all';
				$show_tmpl = in_array($this->template, $wid['pages']);
				$show_custom = in_array('custom:'.$this->request, $wid['pages']);

				if ($show_all || $show_tmpl || $show_custom)
					$this->wanyw_widgets[] = $wid;
			}
		}

		parent::doctype();
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
		foreach ($this->wanyw_widgets as $wid) {
			if ($wid['position'] === $pos)
				$this->output($wid['content']);
		}
	}
}
