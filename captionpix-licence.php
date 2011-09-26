<?php
/*
Author: Russell Jamieson
Author URI: http://www.russelljamieson.com
Copyright &copy; 2010-2011 &nbsp; Russell Jamieson
*/

define('CAPTIONPIX_LICENCE', 'captionpix_licence');

class captionpix_licence {

    private static $initialized = false;
    private static $slug = 'captionpix_license';
    private static $parenthook  = CAPTIONPIX;
    private static $screen_id;

	static function init() {
	    if (self::$initialized) return true;
		self::$initialized = true;
	    self::$screen_id = self::$parenthook.'_page_' . self::$slug;
		add_filter('screen_layout_columns', array(CAPTIONPIX_LICENCE, 'screen_layout_columns'), 10, 2);
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
		add_submenu_page(self::get_parenthook(), __('CaptionPix License'), __('License'), 'manage_options', self::get_slug(), array(CAPTIONPIX_LICENCE,'controller'));
		add_action('load-'.$screen_id, array(CAPTIONPIX_LICENCE, 'load_page'));
		add_action('admin_head-'.$screen_id, array(CAPTIONPIX_LICENCE, 'load_style'));
		add_action('admin_footer-'.$screen_id, array(CAPTIONPIX_LICENCE, 'load_script'));		
		add_action('admin_footer-'.$screen_id, array(CAPTIONPIX_LICENCE, 'toggle_postboxes'));
	}

	static function load_style() {
    	echo ('<link rel="stylesheet" id="'.CAPTIONPIX_ADMIN.'" href="'.CAPTIONPIX_PLUGIN_URL.'/captionpix-licence.css?ver='.CAPTIONPIX_PLUGIN_URL.'" type="text/css" media="all" />');
 	}

	static function load_script() {
    	echo('<script type="text/javascript" src="'.CAPTIONPIX_PLUGIN_URL.'/captionpix-licence.js?ver='.CAPTIONPIX_VERSION.'"></script>');    
	}	

	static function load_page() {
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');	
		add_meta_box('captionpix-licence', __('CaptionPix License',CAPTIONPIX), array(CAPTIONPIX_LICENCE, 'licence_panel'), self::get_screen_id(), 'normal', 'core');
		add_meta_box('captionpix-request', __('Free License Key',CAPTIONPIX), array(CAPTIONPIX_LICENCE, 'request_panel'), self::get_screen_id(), 'side', 'core');
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


	static function save() {
		check_admin_referer(CAPTIONPIX_LICENCE);
       	if (CaptionPixUpdater::save_licence(trim(stripslashes($_POST['licence'])))) {
       		$message = __("CaptionPix License saved.",CAPTIONPIX_ADMIN);
 			CaptionPixUpdater::get_updates(false); //update cache with new entitlements as a licensed user
 		} else
       		$message = __("CaptionPix License has not changed.",CAPTIONPIX_ADMIN);
  		return '<div id="message" class="updated fade"><p>' . $message. '</p></div>';
	}

	static function licence_panel($post, $metabox) {		
		$home = CAPTIONPIX_HOME;	
		$is_pro = false;
		$key_status_indicator ='';
		$notice ='';
		$licence = CaptionPixUpdater::get_licence(false);
		if (! empty($licence)) {
   			$is_valid = CaptionPixUpdater::check_validity();
   			$key_status_indicator = "<img src='" . CAPTIONPIX_PLUGIN_URL ."/images/".($is_valid ? "tick" : "cross").".png'/>";
 			$notice = CaptionPixUpdater::get_notice();
 		}
        $readonly = $is_valid ? '' : 'readonly="readonly" class="readonly"';
		print <<< LICENCE_PANEL

<h4>How To Get A FREE CaptionPix License</h4>

<p>To get a license key: </p>
<ol>
<li>Fill out the form on the right</li>
<li>The first email we'll send will contain a link which you need to click, to confirm</li>
<li>Once you've done that we'll send a second email containing your license key</li>
<li>Copy and paste the license key into the box below and click on the save changes button</li>
<li>You're done!</li>
<li>Get back to us with any problems by visiting <a href=" http://www.captionpix.com/getting-help/">our support page</a></li>
</ol>
<label for="licence">License Key: </label><input type="password" name="licence" id="licence"  style="width:320px" value="{$licence}" />&nbsp;{$key_status_indicator}
<p class="cpix-notice">{$notice}</p>

LICENCE_PANEL;
	}

	static function request_panel($post, $metabox) {
		$home = CAPTIONPIX_HOME;
		$domain = parse_url(site_url(),PHP_URL_HOST);
		print <<< REQUEST_PANEL
<p><img src="http://images.captionpix.com/layout/get-free-license-key.png" alt="CaptionPix Free Licence Request" /></p>
<form id="captionpix_signup" name="captionpix_signup" method="post" action="{$home}"
onSubmit="return captionpix_validate_form(this);">
<input type="hidden" name="form_storm" value="submit"/>
<input type="hidden" name="destination" value="captionpix"/>
<input type="hidden" name="domain" value="{$domain}"/>
<label for="firstname">First Name<input id="firstname" name="firstname" type="text" value="" /></label><br/>
<label for="email">Your Email<input id="email" name="email" type="text" /></label><br/>
<label id="lsubject" for="subject">Subject<input id="subject" name="subject" type="text" /></label>
<input type="submit" value="" />
</form>
REQUEST_PANEL;
	}	

	static function controller() {
 		global $screen_layout_columns;		
 		if (isset($_POST['options_update'])) echo self::save();
    	$themes_url = captionpix_themes::get_url(); 
?>
    <div id="poststuff" class="metabox-holder has-right-sidebar">
        <h2>Obtaining a <span class="cpix-highlight">FREE</span> CaptionPix Licence Is A Good Idea!</h2>
		<p>CaptionPix is a FREE plugin and comes with a <span class="cpix-highlight">standard set of 6 theme designs</span> for image borders.</p>
		<p>If you sign up as a free licensed user we'll give you many <a href="<?php echo $themes_url; ?>">MANY MORE CaptionPix themes</a> as a thank you. By signing up you help to support the work we do by allowing us to contact you about our other plugins and tutorials.</p>

        <div id="side-info-column" class="inner-sidebar">
		<?php do_meta_boxes(self::get_screen_id(), 'side', null); ?>
        </div>
        <div id="post-body" class="has-sidebar">
            <div id="post-body-content" class="has-sidebar-content">
			<form method="post" id="slickr_flickr_options">
			<?php wp_nonce_field(CAPTIONPIX_LICENCE); ?>
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php do_meta_boxes(self::get_screen_id(), 'normal', null); ?>
			<p class="submit">
			<input type="submit"  class="button-primary" name="options_update" value="Save Changes" />
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