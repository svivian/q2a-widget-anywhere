<?php
/*
	Question2Answer Widget Anywhere plugin
	Copyright (C) 2012 Scott Vivian

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: https://www.gnu.org/licenses/gpl.html
*/

if (!defined('QA_VERSION')) {
	header('Location: ../../');
	exit;
}


qa_register_plugin_module('page', 'qa-widget-anywhere.php', 'qa_widget_anywhere', 'Widget Anywhere');
qa_register_plugin_layer('qa-wa-layer.php', 'Widget Anywhere');
qa_register_plugin_phrases('qa-wa-lang-*.php', 'widgetanywhere');
