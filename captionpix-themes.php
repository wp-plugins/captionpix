<?php
/*
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com
Copyright &copy; 2010-2011 &nbsp; Russell Jamieson
*/

define('CAPTIONPIX_THEMES', 'captionpix_themes');

class captionpix_themes {

    private static $initialized = false;
    private static $slug = 'captionpix_themes';
    private static $parenthook  = CAPTIONPIX;
    private static $screen_id;

	static function init() {
	    if (self::$initialized) return true;
		self::$initialized = true;
	    self::$screen_id = self::$parenthook.'_page_' . self::$slug;
		add_filter('screen_layout_columns', array(CAPTIONPIX_THEMES, 'screen_layout_columns'), 10, 2);
	}

    static function get_slug() {
		return self::$slug;
	}

    static function get_parenthook(){
		return self::$parenthook;
	}

    static function get_screen_id(){
		return self::$screen_id;
	}

 	static function get_url($id='', $noheader = false) {
		return admin_url('admin.php?page='.self::$slug);
	}


	static function screen_layout_columns($columns, $screen) {
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == self::get_screen_id()) {
				$columns[self::get_screen_id()] = 2;
			}
		}
		return $columns;
	}

	static function admin_menu() {
		self::init();
		$screen_id = self::get_screen_id();
		add_submenu_page(self::get_parenthook(), __('CaptionPix Themes'), __('Themes'), 'manage_options', self::get_slug(), array(CAPTIONPIX_THEMES,'controller'));
		add_action('load-'.$screen_id, array(CAPTIONPIX_THEMES, 'load_page'));
		add_action('admin_head-'.$screen_id, array(CAPTIONPIX_THEMES, 'load_style'));
		//add_action('admin_footer-'.$screen_id, array(CAPTIONPIX_THEMES, 'load_script'));		
		add_action('admin_footer-'.$screen_id, array(CAPTIONPIX_THEMES, 'toggle_postboxes'));
	}

	static function load_style() {
    	echo ('<link rel="stylesheet" id="'.CAPTIONPIX_ADMIN.'" href="'.CAPTIONPIX_PLUGIN_URL.'/captionpix-themes.css?ver='.CAPTIONPIX_PLUGIN_URL.'" type="text/css" media="all" />');
 	}

	static function load_script() {
    	echo('<script type="text/javascript" src="'.CAPTIONPIX_PLUGIN_URL.'/captionpix-themes.js?ver='.CAPTIONPIX_VERSION.'"></script>');    
	}	

	static function load_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');	
		add_meta_box('captionpix-free-themes', __('Free CaptionPix Themes',CAPTIONPIX), array(CAPTIONPIX_THEMES, 'free_panel'), self::get_screen_id(), 'normal', 'core');
		add_meta_box('captionpix-bonus-themes', __('Free Licensed CaptionPix Themes',CAPTIONPIX), array(CAPTIONPIX_THEMES, 'bonus_panel'), self::get_screen_id(), 'normal', 'core');
		add_meta_box('captionpix-pro', __(' CaptionPix Pro Themes',CAPTIONPIX), array(CAPTIONPIX_THEMES, 'pro_panel'), self::get_screen_id(), 'normal', 'core');
		add_meta_box('captionpix-help', __('CaptionPix',CAPTIONPIX), array(CAPTIONPIX_THEMES, 'help_panel'), self::get_screen_id(), 'side', 'core');
		global $current_screen;
		add_contextual_help( $current_screen,
			'<h3>CaptionPix</h3><p>Here you can get your FREE CaptionPix License Key.</p>'. 
			'<p><a href="'.CAPTIONPIX_HOME.'tutorials" target="_blank">Getting Started with CaptionPix</a></p>');	
	}


    static function toggle_postboxes() {
    ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo self::get_screen_id(); ?>');
		});
		//]]>
	</script>
	<?php
    }

   static function screenshot($theme) {
		$images = 'http://images.captionpix.com/themes/';
   		return '<a href="'.$images.$theme.'-big.jpg" rel="thickbox-free" class="thickbox"><img src="'.$images.$theme.'.jpg" title="'.
				$theme.' Theme" alt="Screenshot of '.$theme.' theme" /></a>';
   }

   static function captioned_screenshot($theme,$group) {
		$images = 'http://images.captionpix.com/themes/';
		$title = ucwords($theme.' theme');
   		$attr = array('theme' => 'crystal', 'width'=>'220', 'float'=>'center', 'imgsrc'=> $images.$theme.'.jpg',
   			'imglink'=> $images.$theme.'-big.jpg', 'imglinkrel'=> 'thickbox-'.$group, 'imglinkclass'=>'thickbox', 
   			'imgtitle'=> $title, 'imgalt' => 'Screenshot of '.$title, 'captiontext' => $title);
   		return captionpix::display($attr);
   }

	static function free_panel($post, $metabox) {

		$themes = CaptionPixThemeFactory::get_themes_in_set('free');
	    $themelist = '';
	    foreach ($themes as $theme) $themelist .= '<li>'.self::captioned_screenshot($theme,'free').'</li>';
		print <<< FREE_PANEL
<p>The following themes are available to all users in the current version of CaptionPix.</p>
<p>Click the image for a larger example of how the theme looks with text wrapped around it.</p>
<ul class="cpix-thumbnails">
{$themelist}
</ul>
FREE_PANEL;
	}	

	static function bonus_panel($post, $metabox) {

		$themes = CaptionPixThemeFactory::get_themes_in_set('licensed');
	    $themelist = '';
	    foreach ($themes as $theme) $themelist .= '<li>'.self::captioned_screenshot($theme,'licensed').'</li>';
		print <<< BONUS_PANEL
<p>The following themes are available to users who register and install the FREE licence.</p>
<p>Click the image for a larger example of how the theme looks with text wrapped around it.</p>
<ul class="cpix-thumbnails">
{$themelist}
</ul>
BONUS_PANEL;
	}	

	static function pro_panel($post, $metabox) {
		print <<< PRO_PANEL
<p>In the next month we will be launching CaptionPix Pro which is the premium version of CaptionPix.</p>
For an annual membership fee we offer support and bonus features:
<ul class="cpix-benefits">
<li>Support Forum</li>
<li>Themes club membership with access to all our themes</li>
<li>Ability to load your own themes</li>
</ul>
PRO_PANEL;
	}	



	static function help_panel($post, $metabox) {
		$home = CAPTIONPIX_HOME;
		print <<< HELP_PANEL
<p><img src="http://images.captionpix.com/layout/captionpix-logo.jpg" alt="CaptionPix Image Captioning Plugin" /></p>
<ul>
<li><a target="_blank" href="{$home}">CaptionPix Plugin Home Page</a></li>
<li><a target="_blank" href="{$home}instructions/">How To Use CaptionPix</a></li>
<li><a target="_blank" href="{$home}tutorials/">FREE CaptionPix Tutorials</a></li>
<li><a target="_blank" href="{$home}help/">CaptionPix Help</a></li>
</ul>
HELP_PANEL;
	}	


	static function controller() {
 		global $screen_layout_columns;		
?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <h2>CaptionPix Themes</h2>
        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes(self::get_screen_id(), 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form method="post" id="slickr_flickr_options">
			<?php wp_nonce_field(CAPTIONPIX_THEMES); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php do_meta_boxes(self::get_screen_id(), 'normal', null); ?>
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
<?php
	}  

}
?>