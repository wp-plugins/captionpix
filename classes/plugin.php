<?php
$dir = dirname(__FILE__) . '/';
require_once($dir . 'options.php');
require_once($dir . 'updater.php');
require_once($dir . 'theme-factory.php');
require_once($dir . 'public.php');
if (is_admin()) {
	require_once($dir . 'admin.php');
	require_once($dir . 'licence.php');
	require_once($dir . 'defaults.php');
	require_once($dir . 'themes.php');
}

class CaptionPixPlugin {
	public static function init() {
		add_action( 'wp_loaded', array('authorsure_options','wordpress_allow_arel') );
		CaptionPix::init();
		if (is_admin()) {
			CaptionPixAdmin::init();	
			CaptionPixLicence::init();	
			CaptionPixDefaults::init();			
			CaptionPixThemes::init();	
		}
	}
}
?>