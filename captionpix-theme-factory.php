<?php

class CaptionPixThemeFactory {

	private static $themes = array();
  	private static $themesets = array();
	
	private static $defaults = array(
	
 		'crystal' => array('framecolor' => 'transparent',
 			'imgborder'=>'none', 'imgbordercolor'=>'', 'imgbordersize'=>'0', 'imgmargin'=>'0', 'imgpadding'=>'0',
 			'captionfontcolor' => '#000000', 'captionfontfamily' => 'Arial', 'captionfontsize'=>'13', 'captionfontstyle'=>'italic',
 			'captionpaddingtop' => '5','captionpaddingbottom' => '5',
   			'nooverrides' => 'theme'),		
   			
		'wp-caption' => array( 'align' => 'left','framesize='=>'6', 'captionclass' => 'nostyle')

		);

	public static function get_theme_names() {
    	return array_keys(self::get_themes());
	}

	public static function get_themes_in_set($myset) {
		if (count(self::$themesets) == 0) self::refresh_themesets();
		if (is_array(self::$themesets) && (count(self::$themesets) > 0)) 
			return array_keys(self::$themesets,$myset);
 		else
 			return array();
 	}

	public function get_theme($theme_name) {
    	$themes = self::get_themes();
   	 	if ($theme_name && $themes && array_key_exists($theme_name,$themes))
        	return $themes[$theme_name];
    	else
        	return self::get_default_theme();
	}

 	private static function get_default_theme() {
 		return self::$defaults['crystal'];
    }
    
	private static function get_themes ($cache = true) {
   		if (!$cache || (count(self::$themes) == 0)) self::refresh_themes($cache);
   		return self::$themes;
   	}

	private static function refresh_themes ($cache=true) {
		$themes = self::$defaults;
   		$more_themes = CaptionPixUpdater::get_updates($cache,'updates');
   		if (is_array($more_themes) && (count($more_themes) > 0)) $themes = array_merge($more_themes,$themes);
        foreach ($themes as $key => $theme) { //allow plugin to determine image file locations, local, amazon s3, cdn
			if (array_key_exists('framebackground',$theme)) $themes[$key]['framebackground'] = str_replace('CAPTIONPIX_FRAMES_URL',CAPTIONPIX_FRAMES_URL,$theme['framebackground']);
			if (array_key_exists('frameborder',$theme)) $themes[$key]['frameborder'] = str_replace('CAPTIONPIX_BORDERS_URL',CAPTIONPIX_FRAMES_URL,$theme['frameborder']);
		}
   		self::$themes = $themes; //update static value
	}

	private static function refresh_themesets ($cache=true) {
   		self::$themesets = 	CaptionPixUpdater::get_updates($cache,'themesets');
	}

}
?>