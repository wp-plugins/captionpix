<?php
define('CAPTIONPIX_UPGRADER', 'http://www.captionpix.com/updates.php'); 

class CaptionPixUpdater {

	private static $initialized=false;
	private static $licence;
	private static $upgrader;
	private static $version;
	private static $package;
	private static $expiry;
	private static $notice;
	private static $valid ;

	public static function get_licence($cache=true) {
    	if (!$cache || empty(self::$licence)) self::$licence = get_option("captionpix_licence");
    	return self::$licence;
	}
	
	public static function set_licence($new_licence) {
		self::$licence = empty($new_licence) ? '' : md5($new_licence);
    }

	public static function save_licence($new_licence) {
  		$old_licence = self::get_licence(false); //fetch old licence from database
		if ($new_licence != $old_licence) {
		    self::$initialized = false;
			self::set_licence($new_licence);
   			return update_option("captionpix_licence", self::$licence) ;	
   			}
   		else
   			return false;
	}

	public static function check_validity(){
    	if (self::get_licence()) {
    		if (!self::$initialized) self::get_updates(); 
    		return self::$valid;
    		}
    	else 
    		return false;
	}

  	public static function get_version(){
    	if (!self::$initialized) self::get_updates(); 
    	return self::$version;
   	}
   
 	public static function get_package(){
    	if (!self::$initialized) self::get_updates(); 
    	return self::$package;
   	}   

	public static function get_notice(){
    	if (!self::$initialized) self::get_updates(); 
    	return self::$notice;
   	}

 	public static function get_expiry(){
    	if (!self::$initialized) self::get_updates(); 
    	return self::$expiry;
   	}
	
	public static function get_updates($cache=true,$item='updates'){
   		return self::parse_updates(self::fetch_remote_or_cache($item,$cache));
	}
	
    private static function parse_updates($response) {
        	self::$initialized = true; 
 			if (is_array($response) && (count($response) >= 6)) {
    	        self::$valid = $response['valid_key']; 
    	        self::$version = $response['version']; 
    	        self::$package = $response['package'];  
    	        self::$notice = $response['notice']; 
    	        self::$expiry = $response['expiry']; 
    			return $response['updates'];
				}
			else {
    	        self::$valid = false; 
    	        self::$version = CAPTIONPIX_VERSION; 
    	        self::$package = 'http://wordpress.org/extend/plugins/'.CAPTIONPIX.CAPTIONPIX_VERSION.'.zip';  
    	        self::$notice = __("Unable to check for updates. Please try again."); 
    	        self::$expiry = ''; 			
				return array(); //return nothing
			}
    }

	private static function fetch_remote_or_cache($item,$cache=true){
		$transient = CAPTIONPIX.'_'.$item;
    	$values = $cache ? get_transient($transient) : false;
    	if ((false === $values)  || is_array($values)) {
    	    $raw_response = self::remote_call($item, $cache);
    	    $values = (is_array($raw_response) && array_key_exists('body',$raw_response)) ? $raw_response['body'] : false;
    	    set_transient($transient, $values, 86400); //cache for 24 hours
		}
		return false === $values ? false : unserialize(gzinflate(base64_decode($values)));
	}

	private static function remote_call($action, $cache=true){
        $options = array('method' => 'POST', 'timeout' => 3);
        $options['headers'] = array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option('blog_charset'),
            'User-Agent' => 'WordPress/' . get_bloginfo("version"),
            'Referer' => get_bloginfo("url")
        );
        $raw_response = wp_remote_request(self::get_upgrader($cache). '&act='.$action  , $options);
        if ( is_wp_error( $raw_response ) || 200 != $raw_response['response']['code']){
            return false;
        } else {
            return $raw_response;
        }
	}

	private static function get_upgrader($cache = true){
        global $wpdb;
        if (empty(self::$upgrader) || ($cache == false))
            self::$upgrader = CAPTIONPIX_UPGRADER. sprintf("?of=CaptionPix&key=%s&v=%s&wp=%s&php=%s&mysql=%s",
                urlencode(self::get_licence($cache)), urlencode(CAPTIONPIX_VERSION), urlencode(get_bloginfo("version")),
                urlencode(phpversion()), urlencode($wpdb->db_version()));

        return self::$upgrader;
	}
   
}
?>