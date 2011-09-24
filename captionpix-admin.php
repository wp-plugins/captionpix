<?php
/*
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com
Copyright &copy; 2010-2011 &nbsp; Russell Jamieson
*/
require_once (dirname(__FILE__).'/captionpix-licence.php');
require_once (dirname(__FILE__).'/captionpix-defaults.php');
require_once (dirname(__FILE__).'/captionpix-themes.php');

add_action('init', 'captionpix_admin_init');
add_filter('plugin_action_links','captionpix_plugin_action_links', 10, 2 );
add_filter('screen_layout_columns', 'captionpix_screen_layout_columns', 10, 2);

	function captionpix_plugin_action_links( $links, $file ) {
		if ( CAPTIONPIX_PATH == $file ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page=captionpix' ) . '">Resources</a>';
			if (is_array($links)) array_unshift( $links, $settings_link );
		}
		return $links;
	}

	function captionpix_screen_layout_columns($columns, $screen) {
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == self::get_screen_id()) {
				$columns[self::get_screen_id()] = 2;
			}
		}
		return $columns;
	}

	function captionpix_admin_init() {
		add_action('admin_menu', 'captionpix_admin_menu');
		add_action('admin_menu', array('captionpix_licence','admin_menu'));
		add_action('admin_menu', array('captionpix_defaults','admin_menu'));
		add_action('admin_menu', array('captionpix_themes','admin_menu'));
	}
	
	function captionpix_admin_menu() {
		add_menu_page('CaptionPix', 'CaptionPix', 'manage_options', 
			CAPTIONPIX, 'captionpix_resources_panel',CAPTIONPIX_PLUGIN_URL.'/images/captionpix-icon.png' );
	}

	function captionpix_resources_panel() {
    	$licence_url = captionpix_licence::get_url(); 
    	$defaults_url = captionpix_defaults::get_url(); 
    	$themes_url = captionpix_themes::get_url(); 
    	$home_url = CAPTIONPIX_HOME;
    	print <<< ADMIN_PANEL
<div class="wrap">
<h2>CaptionPix Resources</h2>
<img src="http://images.captionpix.com/layout/captionpix-logo.jpg" alt="CaptionPix Image Captioning Plugin" style="float:left;padding:10px 30px;" />
<p>To get your FREE license go to <a href="{$licence_url}">License</a></p>
<p>To set up your CaptionPix plugin defaults go to <a href="{$defaults_url}">Settings</a></p>
<p>To see the available CaptionPix themes go to <a href="{$themes_url}">Themes</a></p>
<p>For plugin features and capabilities go to <a href="{$home_url}">{$home_url}</a></p>
<p>For plugin tutorials go to <a href="{$home_url}tutorials">{$home_url}tutorials</a></p>
<p>For help go to <a href="{$home_url}getting-help">{$home_url}help</a></p>
</div>
ADMIN_PANEL;
	}

?>