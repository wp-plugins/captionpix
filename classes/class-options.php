<?php
class Captionpix_Options {

	private static $defaults = array(
	    'theme'=> 'crystal',
	    'align' => 'left',
	    'framebackground' => '',
	    'frameborder' => '',
	    'framebordercolor' => '',    	
	    'framebordersize' => '',
	    'framecolor' => '',
	    'framesize'=> '',
	    'marginbottom' => '10',	
	    'marginside' => '15',
	    'margintop' => '7',
	    'nostyle' => '',
	    'width' => '300',
	    'imgsrc' => '',
		'imglink' => '',	
		'imglinkrel' => '',
		'imglinkclass' => '',
	    'imgtitle' => '',
	    'imgalt' => '',
	    'imgborder' => 'none',	
	    'imgbordercolor' => '',
	    'imgbordersize' => '',    
	    'imgmargin' => '0',	
	    'imgpadding' => '0',
	    'captionalign' => 'center',
	    'captionclass' => '',
	    'captionfontcolor' => '#FFFFFF',	
	    'captionfontfamily' => 'inherit',
	    'captionfontsize' => '12',
	    'captionfontstyle' => 'normal',
	    'captionfontweight' => 'normal',
	    'captionpaddingleft' => '10',
	    'captionpaddingright' => '10',
	    'captionpaddingtop' => '10',
	    'captionpaddingbottom' => '5',
	    'captionmaxwidth' => '',
	    'captiontext' => '',
	    'autocaption' => 'none'
	    );

	private static $options = array();

    private static function get_defaults() {
		return self::$defaults;
    }

	public static function get_options ($cache = true) {
		if ($cache && (count(self::$options) > 0)) return self::$options;
		$defaults = self::get_defaults();
		$options = get_option('captionpix_options');
		self::$options = empty($options) ? $defaults : wp_parse_args($options, $defaults); 
   		return self::$options;
	}

	public static function get_option($option_name) {
	    $options = self::get_options();
	    if ($option_name && $options && array_key_exists($option_name,$options)) 
	    	return $options[$option_name];
	    else
	        return false;
	}

	public static function save_options ($options) {
		$result = update_option('captionpix_options',$options);
		self::get_options(false); //update cache
		return $result;
	}
}
?>