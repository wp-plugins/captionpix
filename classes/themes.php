<?php
class CaptionpixThemes {

    private static $parenthook  = CAPTIONPIX;
    private static $slug = 'captionpix_themes';
    private static $screen_id;

	static function init() {
	    self::$screen_id = self::$parenthook.'_page_' . self::$slug;
		add_action('admin_menu',array(__CLASS__, 'admin_menu'));		
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


	static function admin_menu() {
		$screen_id = self::get_screen_id();
		add_submenu_page(self::get_parenthook(), __('CaptionPix Themes'), __('Themes'), 'manage_options', self::get_slug(), array(__CLASS__,'controller'));
		add_action('load-'.$screen_id, array(__CLASS__, 'load_page'));
	}

	static function load_page() {
		add_action('admin_enqueue_scripts', array(__CLASS__,'enqueue_styles'));
		add_action('admin_enqueue_scripts', array(__CLASS__,'enqueue_scripts'));
		add_meta_box('captionpix-free-themes', __('Free CaptionPix Themes',CAPTIONPIX), array(__CLASS__, 'free_panel'), self::get_screen_id(), 'normal', 'core');
		add_meta_box('captionpix-bonus-themes', __('Free Licensed CaptionPix Themes',CAPTIONPIX), array(__CLASS__, 'bonus_panel'), self::get_screen_id(), 'normal', 'core');
		$current_screen = get_current_screen();
		add_contextual_help( $current_screen,
			'<h3>CaptionPix</h3><p>Here you can get your FREE CaptionPix License Key.</p>'. 
			'<p><a href="'.CAPTIONPIX_HOME.'tutorials" rel="external">Getting Started with CaptionPix</a></p>');	
	}


	static function enqueue_styles() {
		wp_enqueue_style('captionpix-admin',CAPTIONPIX_PLUGIN_URL.'/styles/admin.css', array(), CAPTIONPIX_VERSION );
		wp_enqueue_style('captionpix-themes',CAPTIONPIX_PLUGIN_URL.'/styles/themes.css', array(), CAPTIONPIX_VERSION );
	}

	static function enqueue_scripts() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');	
		add_action('admin_footer-'.self::get_screen_id(), array(__CLASS__, 'toggle_postboxes'));
	}

    static function toggle_postboxes() {
    	$hook = self::get_screen_id();
    	print <<< TOGGLE_POSTBOXES
<script type="text/javascript">
//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles('{$hook}');
		});
//]]>
</script>
TOGGLE_POSTBOXES;
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
   		return CaptionPix::display($attr);
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
		$url= $_SERVER['REQUEST_URI'];
        $refresh = array_key_exists('refresh',$_GET);
        if ($refresh) {
        	$cache = false;
        	CaptionPixUpdater::update($cache); //update cache with latest entitlements as a licensed user
			}
		else {
			$cache = true;
			$url .= "&refresh=true";
			}
        $themes = CaptionPixThemeFactory::get_themes_in_set('licensed',$cache);
	    $themelist = '';
	    foreach ($themes as $theme) $themelist .= '<li>'.self::captioned_screenshot($theme,'licensed').'</li>';
		print <<< BONUS_PANEL
<p>The following themes are available to users who register and install the FREE licence.</p>
<p>Click the image for a larger example of how the theme looks with text wrapped around it.</p>
<ul class="cpix-thumbnails">
{$themelist}
</ul>
<p>New themes will appear here automatically within 24 hours of being released.  However, if you have been notified of a release of new 
themes today then you should click to see the latest <a rel="nofollow" href="{$url}">CaptionPix themes.</a></p>
BONUS_PANEL;
	}	


	static function help_panel($post, $metabox) {
		$home = CAPTIONPIX_HOME;
		print <<< HELP_PANEL
<p><img src="http://images.captionpix.com/layout/captionpix-logo.jpg" alt="CaptionPix Image Captioning Plugin" /></p>
<ul>
<li><a href="{$home}" rel="external">CaptionPix Plugin Home Page</a></li>
<li><a href="{$home}instructions/" rel="external">How To Use CaptionPix</a></li>
<li><a href="{$home}tutorials/" rel="external">FREE CaptionPix Tutorials</a></li>
<li><a href="{$home}help/" rel="external">CaptionPix Help</a></li>
</ul>
HELP_PANEL;
	}	

	static function controller() {
 		$this_url = $_SERVER['REQUEST_URI']; 	
 		global $screen_layout_columns;		
?>
<div class="wrap">
    <div id="poststuff" class="metabox-holder">
        <h2 class="title">CaptionPix Themes</h2>
        <div id="post-body">
            <div id="post-body-content">
			<form id="caption_themes" method="post" action="<?php echo $this_url; ?>">
			<?php do_meta_boxes(self::get_screen_id(), 'normal', null); ?>
			<fieldset>
			<?php wp_nonce_field(__CLASS__); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			</fieldset>			
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
</div>
<?php
	}  

}
?>