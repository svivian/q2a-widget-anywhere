<?php
/*
	Plugin Name: Widget Anywhere
	Plugin URI: https://github.com/svivian/q2a-widget-anywhere
	Plugin Description: Add custom HTML on any page(s) in a variety of locations. Useful for Google Adsense, Analytics, adding special instructions for asking questions, and so on.
	Plugin Version: 1.3.1
	Plugin Date: 2015-07-30
	Plugin Author: Scott Vivian
	Plugin Author URI: http://codelair.com/
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: https://raw.githubusercontent.com/svivian/q2a-widget-anywhere/master/qa-plugin.php

	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

	More about this license: http://www.gnu.org/licenses/gpl.html
*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
}


qa_register_plugin_module('page', 'qa-widget-anywhere.php', 'qa_widget_anywhere', 'Widget Anywhere');
qa_register_plugin_layer('qa-wa-layer.php', 'Widget Anywhere');
