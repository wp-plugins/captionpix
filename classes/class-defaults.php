<?php
class Captionpix_Defaults {
    const CODE = 'captionpix'; //prefix ID of CSS elements

    private static $parenthook  = CAPTIONPIX;
    private static $slug = 'captionpix_defaults';
    private static $screen_id;
    private static $keys;
	private static $tips = array(
            'theme' => array('heading' => 'Theme', 'tip' => 'The theme controls the formatting of the frame, image and caption by setting attributes such as colors, font, margins, padding, etc.'),
			'width' => array('heading' => 'Width', 'tip' => '<p>The width is used to set the size of the image. If you generally want to wrap a paragraph of text around the image then choose a value that is around 50% of the width of the content section of your page. This is typically be around 300 pixels.</p><p class="description">If you want the site to be mobile responsive and have images appear at the maximum possible size for the device then leave the width field blank.</b></p>'),
	        'align' => array('heading' => 'Alignment', 'tip' => 'Set this value if you typically want to center the image or float the image to the left or right of the text.'),
			'marginside' => array('heading' => 'Side Margin', 'tip' => 'The side margin is used to create white space between the image and text paragraph. (This will be on the right of the image if the image is aligned to the left, and on the left of the image if the image is aligned to the right).'),
			'margintop' => array('heading' => 'Top Margin', 'tip' => 'The Top Margin is used to align the top of image with the top of the text paragraph.'),
			'marginbottom' => array('heading' => 'Bottom Margin', 'tip' => 'The Bottom Margin adds white space between the bottom of the image and the paragraph text.'),
	);
	private static $tooltips;


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

    private static function get_keys(){
		return self::$keys;
	}

	static function admin_menu() {
		$screen_id = self::get_screen_id();
		add_submenu_page(self::get_parenthook(), __('CaptionPix Default Settings'), __('Settings'), 'manage_options', 
			self::get_slug(), array(__CLASS__,'options_panel'));
		add_action('load-'.$screen_id, array(__CLASS__, 'load_page'));
	}

	static function screen_layout_columns($columns, $screen) {
		if (!defined( 'WP_NETWORK_ADMIN' ) && !defined( 'WP_USER_ADMIN' )) {
			if ($screen == self::get_screen_id()) {
				$columns[self::get_screen_id()] = 2;
			}
		}
		return $columns;
	}

	static function load_page() {
 		$message =  isset($_POST['options_update']) ? self::save() : '';	
		$options = Captionpix_Options::get_options(self::use_cache());	
		$callback_params = array ('options' => $options, 'message' => $message);
		add_meta_box('captionpix-intro', __('Intro',CAPTIONPIX), array(__CLASS__, 'intro_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('captionpix-general', __('Display Defaults',CAPTIONPIX), array(__CLASS__, 'general_panel'), self::get_screen_id(), 'normal', 'core', $callback_params);
		add_meta_box('captionpix-help', __('Help',CAPTIONPIX), array(__CLASS__, 'help_panel'), self::get_screen_id(), 'side', 'core', $callback_params);
		add_filter('screen_layout_columns', array(__CLASS__, 'screen_layout_columns'), 10, 2);
	    self::$keys = array_keys(self::$tips);	
		self::$tooltips = new DIY_Tooltip(self::$tips);
		add_action('admin_enqueue_scripts', array(__CLASS__,'enqueue_styles'));
		add_action('admin_enqueue_scripts', array(__CLASS__,'enqueue_scripts'));
		$current_screen = get_current_screen();
		if (method_exists($current_screen,'add_help_tab')) {
    		$current_screen->add_help_tab( array(
        		'id' => 'captionpix_instructions_tab',
        		'title'	=> __('CaptionPix Defaults'),
        		'content' => '<h3>CaptionPix</h3><p>Here you can set the default plugin settings.</p><p><a href="'.CAPTIONPIX_HOME.'tutorials" rel="external">Getting Started with CaptionPix</a></p>'));
		}
	}

	static function enqueue_styles() {
		wp_enqueue_style(self::CODE.'-admin', plugins_url('styles/admin.css',dirname(__FILE__)), array(),CAPTIONPIX_VERSION);
		wp_enqueue_style(self::CODE.'-tooltip', plugins_url('styles/tooltip.css',dirname(__FILE__)), array(),CAPTIONPIX_VERSION);
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

    static function use_cache() {
		return !(array_key_exists('options_update',$_POST) && isset($_POST['options_update'])) ;    
    }

	static function save() {
		check_admin_referer(__CLASS__);
		$old_options = Captionpix_Options::get_options(false); //fetch old options from database
  		$new_options = array();
  		$options = explode(',', stripslashes($_POST['page_options']));
  		if ($options) {
    		foreach ($options as $option) {
       			$option = trim($option);
          		$old_value = $old_options[$option];
    			$new_options[$option] = trim(stripslashes($_POST[$option]));
    		} //end for

   			$updates =  Captionpix_Options::save_options ($new_options) ;
  		    $class="updated fade";
   			if ($updates) 
       			$message = __("CaptionPix Settings saved.",CAPTIONPIX);
   			else
       			$message = __("No CaptionPix settings were changed since last update.",CAPTIONPIX);
  		} else {
  		    $class="error";
       		$message= __("CaptionPix settings not found!",CAPTIONPIX);
  		}
  		return '<div id="message" class="' . $class .' "><p>' . $message. '</p></div>';
	}

	public static function intro_panel($post,$metabox){	
		$message = $metabox['args']['message'];	 	
		print <<< INTRO_PANEL
<p>For help on gettting the best from CaptionPix visit the <a href="http://www.captionpix.com/">CaptionPix Plugin Home Page</a></p>
<p>If you supply a value for one of the settings below, the plugin will remember it so you do not need to supply it for every image.</p>
<p>Note also that you can override any default value you set here by specifying a value on each image you caption.</p>
<p>For example, you may use the <em>Crystal</em> theme as the default caption style but perhaps use the <em>Chunky</em> style on a particular image by specifying <code>theme="chunky"</code> in the shortcode</p>
{$message}
INTRO_PANEL;
	}

	static function general_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
		$themes = Captionpix_Theme_Factory::get_theme_names();
		$theme= $options['theme'];
		$s= sprintf('<select name="%1$s" id="%1$s"><option value="">%2$s</option>','theme',__('Please select'));
	    for ($i=0; $i<count($themes); $i++) $s .= '<option value="'.$themes[$i] .'">'.str_replace(' ','-',ucwords(str_replace('-',' ',$themes[$i]))) . '</option>';
	  	$s .= '</select>';
	    $theme_list = str_replace('value="'.$theme.'"', 'selected="selected" value="'.$theme.'"', $s);
		
		$align_none = $options['align']=="none"?'selected="selected"':'';
		$align_center = $options['align']=="center"?'selected="selected"':'';
		$align_left = $options['align']=="left"?'selected="selected"':'';
		$align_right = $options['align']=="right"?'selected="selected"':'';
		$tip1 = self::$tooltips->tip('theme');
		$tip2 = self::$tooltips->tip('width');
		$tip3 = self::$tooltips->tip('marginside');
		$tip4 = self::$tooltips->tip('margintop');
		$tip5 = self::$tooltips->tip('marginbottom');
		$tip6 = self::$tooltips->tip('align');
		print <<< GENERAL_PANEL
<label>{$tip1}</label> {$theme_list}<br/>
<label>{$tip2}</label><input type="text" name="width" id="width" size="4" maxlength="4" value="{$options['width']}" /> pixels<br/>
<label>{$tip3}</label><input name="marginside" type="text" id="marginside" size="4" maxlength="3" value="{$options['marginside']}" /> pixels<br/>
<label>{$tip4}</label><input name="margintop" type="text" id="margintop" size="4" maxlength="3" value="{$options['margintop']}" /> pixels<br/>
<label>{$tip5}</label><input name="marginbottom" type="text" id="marginbottom" size="4" maxlength="3" value="{$options['marginbottom']}" /> pixels<br/>	
<label>{$tip6}</label><select name="align" id="align">
<option {$align_none} value="none">None</option>
<option {$align_center} value="center">Center </option>
<option {$align_left} value="left">Left</option>
<option {$align_right} value="right">Right</option>
</select><br/>
GENERAL_PANEL;
	}

	static function advanced_panel($post, $metabox) {		
		$options = $metabox['args']['options'];	 	
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

	static function options_panel() {
		$keys = implode(',',self::get_keys());
 		$this_url = $_SERVER['REQUEST_URI']; 		
		$title = sprintf('<h2 class="title">%1$s</h2>', __('CaptionPix Defaults'));
?>
<div class="wrap">
    <?php echo $title; ?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes(self::get_screen_id(), 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form id="captionpix_options"  method="post" action="<?php echo $this_url; ?>" >
			<?php do_meta_boxes(self::get_screen_id(), 'normal', null); ?>
			<p class="submit">
			<input type="submit"  class="button-primary" name="options_update" value="Save Changes" />
			<input type="hidden" name="page_options" value="<?php echo $keys; ?>" />			
			<?php wp_nonce_field(__CLASS__); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			</p>
			</form>
 			</div>
        </div>
        <br class="clear"/>
    </div>
</div>
<?php
	}  

}
