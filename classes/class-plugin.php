<?php

class Captionpix_Plugin {
	public static function init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'class-options.php');
		require_once($dir . 'class-updater.php');
		require_once($dir . 'class-theme-factory.php');
		require_once($dir . 'class-public.php');
		CaptionPix::init();
	}

	public static function admin_init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'class-admin.php');
		require_once($dir . 'class-tooltip.php');
		require_once($dir . 'class-licence.php');
		require_once($dir . 'class-defaults.php');
		require_once($dir . 'class-themes.php');
		Captionpix_Admin::init();	
		Captionpix_Licence::init();	
		Captionpix_Defaults::init();			
		Captionpix_Themes::init();	
	}
}
