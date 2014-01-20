<?php
class CaptionpixAdmin {

    private static $screen_id;
    
    static function get_screen_id(){
		return self::$screen_id;
	}

	static function init() {
		add_filter('plugin_action_links',array(__CLASS__,'plugin_action_links'), 10, 2 );
		add_action('admin_menu',array(__CLASS__, 'admin_menu'));
		add_action('admin_print_styles',array(__CLASS__, 'style_icon'));
	}
	
	static function plugin_action_links( $links, $file ) {
		if ( CAPTIONPIX_PATH == $file ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page=captionpix' ) . '">Resources</a>';
			if (is_array($links)) array_unshift( $links, $settings_link );
		}
		return $links;
	}

	public static function style_icon() {
		print <<< STYLES
<style type="text/css">
#adminmenu .menu-icon-generic.toplevel_page_captionpix div.wp-menu-image:before { content: '\\f128'; }
</style>
STYLES;
	}

	static function admin_menu() {
		add_menu_page('CaptionPix', 'CaptionPix', 'manage_options', CAPTIONPIX, array(__CLASS__,'resources_panel') );
		$intro = sprintf('Intro (v%1$s)', CAPTIONPIX_VERSION);				
		add_submenu_page(CAPTIONPIX, CAPTIONPIX_FRIENDLY_NAME, $intro, 'manage_options', CAPTIONPIX,array(__CLASS__,'resources_panel') );
		add_action('load-'.self::get_screen_id(), array(__CLASS__, 'load_page'));				
	}

	static function load_page() {
		add_filter('screen_layout_columns', array(__CLASS__, 'screen_layout_columns'), 10, 2);
	}

	static function screen_layout_columns($columns, $screen) {
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == self::get_screen_id()) {
				$columns[self::get_screen_id()] = 2;
			}
		}
		return $columns;
	}

	static function resources_panel() {
    	$licence_url = CaptionPixLicence::get_url(); 
    	$defaults_url = CaptionPixDefaults::get_url(); 
    	$themes_url = CaptionPixThemes::get_url(); 
    	$logo_url = CAPTIONPIX_IMAGES_URL . '/captionpix-logo.jpg';
    	$home_url = CAPTIONPIX_HOME;
    	print <<< ADMIN_PANEL
<div class="wrap">
<h2>CaptionPix Resources</h2>
<img src="{$logo_url}" alt="CaptionPix Image Captioning Plugin" style="float:left;padding:10px 30px;" />
<p>To get your FREE license go to <a href="{$licence_url}">License</a></p>
<p>To set up your CaptionPix plugin defaults go to <a href="{$defaults_url}">Settings</a></p>
<p>To see the available CaptionPix themes go to <a href="{$themes_url}">Themes</a></p>
<p>For plugin features and capabilities go to <a href="{$home_url}">{$home_url}</a></p>
<p>For plugin tutorials go to <a href="{$home_url}tutorials">{$home_url}tutorials</a></p>
<p>For help go to <a href="{$home_url}getting-help">{$home_url}help</a></p>
</div>
ADMIN_PANEL;
	}
}
?>