<?php
/*
	Plugin Name: Widget Anywhere
	Plugin URI:
	Plugin Description: Allow the creation of a module to be placed on any page(s) in any position(s). Use it for special instructions when asking questions or common scripts such as Google Adsense or Analytics.
	Plugin Version: 0.9
	Plugin Date: 2012-05-01
	Plugin Author: Scott Vivian
	Plugin Author URI: http://codelair.co.uk/
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.4
	Plugin Update Check URI:
*/

if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
	header('Location: ../../');
	exit;
}


qa_register_plugin_module('page', 'qa-widget-anywhere.php', 'qa_widget_anywhere', 'Widget Anywhere');
qa_register_plugin_layer('qa-wa-layer.php', 'Widget Anywhere');
