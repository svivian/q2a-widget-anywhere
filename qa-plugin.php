<?php
/*
	Plugin Name: Widget Anywhere
	Plugin URI: https://github.com/svivian/q2a-widget-anywhere
	Plugin Description: Add custom HTML on any page(s) in a variety of locations. Useful for Google Adsense, Analytics, adding special instructions for asking questions, and so on.
	Plugin Version: 1.2
	Plugin Date: 2012-05-01
	Plugin Author: Scott Vivian
	Plugin Author URI: http://codelair.co.uk/
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI:
*/

if ( !defined('QA_VERSION') )
{
	header('Location: ../../');
	exit;
}


qa_register_plugin_module('page', 'qa-widget-anywhere.php', 'qa_widget_anywhere', 'Widget Anywhere');
qa_register_plugin_layer('qa-wa-layer.php', 'Widget Anywhere');
