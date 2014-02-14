<?php

class CaptionPixPlugin {
	public static function init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'options.php');
		require_once($dir . 'updater.php');
		require_once($dir . 'theme-factory.php');
		require_once($dir . 'public.php');
		CaptionPix::init();
	}

	public static function admin_init() {
		$dir = dirname(__FILE__) . '/';
		require_once($dir . 'admin.php');
		require_once($dir . 'tooltip.php');
		require_once($dir . 'licence.php');
		require_once($dir . 'defaults.php');
		require_once($dir . 'themes.php');
		CaptionPixAdmin::init();	
		CaptionPixLicence::init();	
		CaptionPixDefaults::init();			
		CaptionPixThemes::init();	
	}
}

add_action ('init',  array('CaptionPixPlugin', 'init'),0);
if (is_admin()) add_action ('init',  array('CaptionPixPlugin', 'admin_init'),0);
?>