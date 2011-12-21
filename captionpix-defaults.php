<?php
/*
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com
Copyright &copy; 2010-2011 &nbsp; Russell Jamieson
*/

define('CAPTIONPIX_DEFAULTS', 'captionpix_defaults');

class captionpix_defaults {

    private static $initialized = false;
    private static $slug = 'captionpix_defaults';
    private static $parenthook  = CAPTIONPIX;
    private static $screen_id;

	static function init() {
	    if (self::$initialized) return true;
		self::$initialized = true;
	    self::$screen_id = self::$parenthook.'_page_' . self::$slug;
		add_filter('screen_layout_columns', array(CAPTIONPIX_DEFAULTS, 'screen_layout_columns'), 10, 2);
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
		add_submenu_page(self::get_parenthook(), __('CaptionPix Default Settings'), __('Settings'), 'manage_options', 
			self::get_slug(), array(CAPTIONPIX_DEFAULTS,'options_panel'));
		add_action('load-'.$screen_id, array(CAPTIONPIX_DEFAULTS, 'load_page'));
		//add_action('admin_head-'.$screen_id, array(CAPTIONPIX_DEFAULTS, 'load_style'));
		//add_action('admin_footer-'.$screen_id, array(CAPTIONPIX_DEFAULTS, 'load_script'));		
		add_action('admin_footer-'.$screen_id, array(CAPTIONPIX_DEFAULTS, 'toggle_postboxes'));
	}

	static function load_style() {
    	echo ('<link rel="stylesheet" id="'.CAPTIONPIX_ADMIN.'" href="'.CAPTIONPIX_PLUGIN_URL.'/captionpix-defaults.css?ver='.CAPTIONPIX_PLUGIN_URL.'" type="text/css" media="all" />');
 	}

	static function load_script() {
    	echo('<script type="text/javascript" src="'.CAPTIONPIX_PLUGIN_URL.'/captionpix-defaults.js?ver='.CAPTIONPIX_VERSION.'"></script>');    
	}	

	static function load_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');	
		add_meta_box('captionpix-general', __('Display Options',CAPTIONPIX), array(CAPTIONPIX_DEFAULTS, 'general_panel'), self::get_screen_id(), 'normal', 'core');
		add_meta_box('captionpix-help', __('Help',CAPTIONPIX), array(CAPTIONPIX_DEFAULTS, 'help_panel'), self::get_screen_id(), 'side', 'core');
		global $current_screen;
		add_contextual_help( $current_screen,
			'<h3>CaptionPix</h3><p>Here you can set the default plugin settings.</p>'. 
			'<p><a href="'.CAPTIONPIX_HOME.'tutorials" rel="external">Getting Started with CaptionPix</a></p>');	
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

    static function use_cache() {
		return !(array_key_exists('options_update',$_POST) && isset($_POST['options_update'])) ;    
    }

	static function save() {
		check_admin_referer(CAPTIONPIX_DEFAULTS);
		$old_options = captionpix_get_options(false); //fetch old options from database
  		$new_options = array();
  		$options = explode(',', stripslashes($_POST['page_options']));
  		if ($options) {
    		foreach ($options as $option) {
       			$option = trim($option);
          		$old_value = $old_options[$option];
    			$new_options[$option] = trim(stripslashes($_POST[$option]));
    		} //end for

   			$updates =  update_option("captionpix_options", $new_options) ;
  		    $class="updated fade";
   			if ($updates) 
       			$message = __("CaptionPix Settings saved.",CAPTIONPIX_ADMIN);
   			else
       			$message = __("No CaptionPix settings were changed since last update.",CAPTIONPIX_ADMIN);
  		} else {
  		    $class="error";
       		$message= __("CaptionPix settings not found!",CAPTIONPIX_ADMIN);
  		}
  		return '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
	}

	static function general_panel($post, $metabox) {		
		$options = captionpix_get_options(self::use_cache());	
		$themes = CaptionPixThemeFactory::get_theme_names();
		$theme= $options['theme'];
		$s= sprintf('<select name="%1$s" id="%1$s"><option value="">%2$s</option>','theme',__('Please select'));
	    for ($i=0; $i<count($themes); $i++) $s .= '<option value="'.$themes[$i] .'">'.ucwords($themes[$i]) . '</option>';
	  	$s .= '</select>';
	    $theme_list = str_replace('value="'.$theme.'"', 'selected="selected" value="'.$theme.'"', $s);
		
		$align_none = $options['align']=="none"?'selected="selected"':'';
		$align_center = $options['align']=="center"?'selected="selected"':'';
		$align_left = $options['align']=="left"?'selected="selected"':'';
		$align_right = $options['align']=="right"?'selected="selected"':'';
		print <<< GENERAL_PANEL
<h4>Default Theme</h4>
<p>The theme controls the formatting of the frame, image and caption by setting attributes such as colors, font, margins, padding, etc.</p>
<p>If you supply the theme here, the plugin will remember it so you do not need to supply it for every image.</p>
<label for="width">Theme: </label> {$theme_list}
<h4>Default Image Width</h4>
<p>The width is used to set the size of the image. If you generally want to wrap a paragraph of text around the image then choose a value 
that is around 50% of the width of the content section of your page. This is typically be around 300 pixels.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every image.</p>
<label for="width">Image Width: </label><input type="text" name="width" id="width" size="4" maxlength="4" value="{$options['width']}" /> pixels
<h4>Default Image Alignment</h4>
<p>Set this value if you typically want to center the image or float the image to the left or right of the text.</p>
<label for="align">Image Alignment: </label><select name="align" id="align">
<option {$align_none} value="none">None</option>
<option {$align_center} value="center">Center </option>
<option {$align_left} value="left">Left</option>
<option {$align_right} value="right">Right</option>
</select>
<h4>Default Side Margin</h4>
<p>The side margin is used to create white space between the image and text paragraph. (This will be on the right of the image if the 
image is aligned to the left, and on the left of the image if the image is aligned to the right).</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every image.</p>
<label for="marginside">Side Margin: </label><input name="marginside" type="text" id="marginside" size="4" maxlength="3" value="{$options['marginside']}" /> pixels
<h4>Default Top Margin</h4>
<p>The Top Margin is used to align the top of image with the top of the text paragraph.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every image.</p>
<label for="margintop">Top Margin: </label><input name="margintop" type="text" id="margintop" size="4" maxlength="3" value="{$options['margintop']}" /> pixels	
<h4>Default Bottom Margin</h4>
<p>The Bottom Margin create white space between the bottom of the image and the paragraph text.</p>
<p>If you supply it here, the plugin will remember it so you do not need to supply it for every image.</p>
<label for="marginbottom">Bottom Margin: </label><input name="marginbottom" type="text" id="marginbottom" size="4" maxlength="3" value="{$options['marginbottom']}" /> pixels	
GENERAL_PANEL;
	}

	static function advanced_panel($post, $metabox) {		
		$options = captionpix_get_options($this->use_cache());		
		$auto_none = $options['autocaption']=="none"?'selected="selected"':'';
		$auto_title = $options['autocaption']=="title"?'selected="selected"':'';
		$auto_alt = $options['autocaption']=="alt"?'selected="selected"':'';
		$auto_post = $options['autocaption']=="post"?'selected="selected"':'';
		print <<< ADVANCED_PANEL
<h4>Auto-captioning</h4>
<p>Set auto-captioning if you want to caption the photos automatically based on their title attribute, the alt tag or the post name.</p>
<p>Captions will only applied on pages with single posts and on the home page.</p>
<label for="autocaption">Auto-captioning: </label><select name="autocaption" id="autocaption">
<option {$auto_none} value="none">None</option>
<option {$auto_title} value="title">Use Image Title as Caption</option>
<option {$auto_alt} value="alt">Use Image Alt as Caption</option>
<option {$auto_post} value="post">Use Post Title as Caption</option>
</select>
ADVANCED_PANEL;
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


	function options_panel() {
 		global $screen_layout_columns;		
 		if (isset($_POST['options_update'])) echo self::save();
 		$this_url = $_SERVER['REQUEST_URI']; 		
?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <h2>CaptionPix Default Settings</h2>
		<p>For help on gettting the best from CaptionPix visit the <a href="http://www.captionpix.com/">CaptionPix Plugin Home Page</a></p>
        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes(self::get_screen_id(), 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form id="captionpix_options"  method="post" action="<?php echo $this_url; ?>" >
			<?php do_meta_boxes(self::get_screen_id(), 'normal', null); ?>
			<p class="submit">
			<input type="submit"  class="button-primary" name="options_update" value="Save Changes" />
			<input type="hidden" name="page_options" value="defaults,theme,width,align,marginside,margintop,marginbottom" />
			<?php wp_nonce_field(CAPTIONPIX_DEFAULTS); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			</p>
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
<?php
	}  

}
?>