<?php
$captionpix_defaults = array(
    'theme'=> 'crystal',
    'align' => 'left',
    'framebackground' => '',
    'frameborder' => '',
    'framebordercolor' => '',    
    'framebordersize' => '',
    'framecolor' => '',
    'framesize'=> '',
    'marginbottom' => '0',
    'marginside' => '15',
    'margintop' => '7',
    'nooverrides' => '',
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
    'captionpaddingtop' => '10',
    'captionpaddingbottom' => '5',
    'captionmaxwidth' => '',
    'captiontext' => '',
    'autocaption' => 'none'
    );

$captionpix_options = array();

function captionpix_get_options ($cache = true) {
   global $captionpix_options;
   global $captionpix_defaults;
   if ($cache && (count($captionpix_options) > 0)) return $captionpix_options;

   $caption_options = array();
   $options = get_option("captionpix_options");
   if (empty($options)) {
      $captionpix_options = $captionpix_defaults;
   } else {
     foreach ($options as $key => $option) if (isset($options[$key]) )  $caption_options[$key] = $option;
     $captionpix_options = wp_parse_args( $caption_options, $captionpix_defaults);
   }
   return $captionpix_options;
}

function captionpix_get_option($option_name) {
    $options = captionpix_get_options();
    if ($option_name && $options && array_key_exists($option_name,$options))
        return $options[$option_name];
    else
        return false;
}
?>