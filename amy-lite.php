<?php
/*
Plugin Name: A.M.Y. Ad Management
Plugin URI: http://calendarscripts.info/amy-lite.html
Description: Manage advertisers and ads in your Wordpress blog.
Author: CalendarScripts.info
Version: 1.3.3
Author URI: http://calendarscripts.info
*/ 

/*  This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'AMYLITE_PATH', dirname( __FILE__ ) );
define( 'AMYLITE_RELATIVE_PATH', dirname( plugin_basename( __FILE__ )));
define( 'AMYLITE_URL', plugin_dir_url( __FILE__ ));

require(AMYLITE_PATH.'/campaigns.php');
require(AMYLITE_PATH.'/ads.php');
require(AMYLITE_PATH.'/plans.php');
require(AMYLITE_PATH.'/zones.php');
require(AMYLITE_PATH.'/packages.php');
require(AMYLITE_PATH.'/rotator.php');
require(AMYLITE_PATH.'/controllers/shortcodes.php');

// function to conditionally add DB fields
function amylite_add_db_fields($fields, $table) {
		global $wpdb;
		
		// check fields
		$table_fields = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
		$table_field_names = array();
		foreach($table_fields as $f) $table_field_names[] = $f->Field;		
		$fields_to_add=array();
		
		foreach($fields as $field) {
			 if(!in_array($field['name'], $table_field_names)) {
			 	  $fields_to_add[] = $field;
			 } 
		}
		
		// now if there are fields to add, run the query
		if(!empty($fields_to_add)) {
			 $sql = "ALTER TABLE `$table` ";
			 
			 foreach($fields_to_add as $cnt => $field) {
			 	 if($cnt > 0) $sql .= ", ";
			 	 $sql .= "ADD $field[name] $field[type]";
			 } 
			 
			 $wpdb->query($sql);
		}
}

function amylite_redirect($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}

function amylite_menu() {  
  add_menu_page(__('A.M.Y. Lite', 'amylite'), __('A.M.Y. Lite', 'amylite'), 'manage_options', 'amylite', 'amylite_main');
  add_submenu_page('amylite', __('Manage Campaigns', 'amylite'), __('Manage Campaigns', 'amylite'), 'manage_options', "amylite_campaigns", "amylite_campaigns");
  add_submenu_page('amylite',__('Manage Ads', 'amylite'), __('Manage Ads', 'amylite'), 'manage_options', "amylite_ads", "amylite_ads");
  add_submenu_page('amylite',__('Pricing Plans', 'amylite'), __('Pricing Plans', 'amylite'), 'manage_options', "amylite_plans", "amylite_plans");
  add_submenu_page('amylite',__('Ad Zones', 'amylite'), __('Ad Zones', 'amylite'), 'manage_options', "amylite_zones", "amylite_zones");
  add_submenu_page(null, __('Packages', 'amylite'), __('Packages', 'amylite'), 'manage_options', "amylite_packages", "amylite_packages");  
  
  if(!current_user_can('manage_options')) {
	  // user menu
	   add_menu_page(__('My Ad Campaigns', 'amylite'), __('My Ad Campaigns', 'amylite'), 'read', 'amylite_campaigns', 'amylite_campaigns');
	   add_submenu_page('amylite_campaigns', __('Manage Campaigns', 'amylite'), __('Manage Campaigns', 'amylite'), 'read', "amylite_campaigns", "amylite_campaigns");
	  add_submenu_page('amylite_campaigns',__('Manage Ads', 'amylite'), __('Manage Ads', 'amylite'), 'read', "amylite_ads", "amylite_ads");
	  add_submenu_page(null, __('Packages', 'amylite'), __('Packages', 'amylite'), 'read', "amylite_my_packages", "amylite_packages");  
	}
}

function amylite_init() {	
	global $wpdb;	
	define('AMYLITE_ADS',$wpdb->prefix. "amylite_ads");
	define('AMYLITE_ADS_CAMPAIGNS',$wpdb->prefix. "amylite_ads_campaigns");
	define('AMYLITE_CAMPAIGNS',$wpdb->prefix. "amylite_campaigns");
	define('AMYLITE_PACKAGES',$wpdb->prefix. "amylite_packages");
	define('AMYLITE_PRICINGS',$wpdb->prefix. "amylite_pricings");
	define('AMYLITE_PRICING_ZONES',$wpdb->prefix. "amylite_pricing_zones");
	define('AMYLITE_ZONES',$wpdb->prefix. "amylite_zones");
	
	$old_version = get_option('amylite_db_version');	
	if($old_version != 1.3) amylite_activate(true);
	
	add_shortcode('amylite-zone', array('AmyLiteShortcodes', 'show_zone'));
}

function amylite_activate($update = false) {
    global $wpdb, $user_ID;
    $old_version = get_option('amylite_db_version');
    if(!$update) amylite_init();
    $wpdb->show_errors = true;
        
    if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_ADS."'") != AMYLITE_ADS) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_ADS . " (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
                  `code` text NOT NULL,                                    
                  `label` varchar(255) NOT NULL,                  
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}
    
    if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_ADS_CAMPAIGNS."'") != AMYLITE_ADS_CAMPAIGNS) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_ADS_CAMPAIGNS . " (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
                  `ad_id` int(10) unsigned NOT NULL,                                   
                  `campaign_id` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}
    
    if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_CAMPAIGNS."'") != AMYLITE_CAMPAIGNS) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_CAMPAIGNS . " (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
                  `name` varchar(255) NOT NULL,                               
                  `date` date NOT NULL,                  
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}
    
    if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_PACKAGES."'") != AMYLITE_PACKAGES) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_PACKAGES . " (
				    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `campaign_id` int(10) unsigned NOT NULL,                      
                      `zone_id` int(10) unsigned NOT NULL,
                      `pricing_id` int(10) unsigned NOT NULL,
                      `date_start` date NOT NULL,
                      `date_end` date NOT NULL,                      
                      `amount` float NOT NULL,        
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}
    
     if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_PRICINGS."'") != AMYLITE_PRICINGS) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_PRICINGS . " (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
                  `name` varchar(255) NOT NULL,
                  `description` text NOT NULL,
                  `price` float NOT NULL,    
                  `code`  varchar(255) NOT NULL,
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}
    
    if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_PRICING_ZONES."'") != AMYLITE_PRICING_ZONES) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_PRICING_ZONES . " (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
                  `pricing_id` int(10) unsigned NOT NULL,
                   `zone_id` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}
    
    if($wpdb->get_var("SHOW TABLES LIKE '".AMYLITE_ZONES."'") != AMYLITE_ZONES) {            	  		
			$sql = "CREATE TABLE " . AMYLITE_ZONES . " (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,                  
                  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
                  `description` text CHARACTER SET latin1 NOT NULL,
                  `width` int(10) unsigned NOT NULL,
                  `height` int(10) unsigned NOT NULL,
                  PRIMARY KEY (`id`)                  
				);";						
			$wpdb->query($sql);
	}   
	
	amylite_add_db_fields( array(
		array("name" => "user_id", "type"=>" INT UNSIGNED NOT NULL DEFAULT 0")
	),
	AMYLITE_CAMPAIGNS );
	
	amylite_add_db_fields( array(
		array("name" => "is_pending", "type"=>" TINYINT UNSIGNED NOT NULL DEFAULT 0")
	),
	AMYLITE_PACKAGES );
	
	// assign all unassigned campaigns to admin		
	$wpdb->query($wpdb->prepare("UPDATE ".AMYLITE_CAMPAIGNS." SET user_id=%d WHERE user_id=0", $user_ID));
	    
    update_option( "amylite_db_tables", 1 );
    update_option("amylite_db_version", 1.3);    
}

function amylite_main() {
    // main help and settings
    ?>
    <div class="wrap">
    <h1>Using A.M.Y. Lite</h1>
    
    <p>A.M.Y. Lite is simplified and Wordpress-based version of the standalone <a href="http://calendarscripts.info/ad-management-software.html" target="_blank">A.M.Y. Ad Management Yoga</a> software for managing and displaying ads on your site. The full version is a standalone PHP software and supports multiple ad types, advertisers registration and control panel, online payments through Paypal, ad priority, reports and many more features. <a href="http://calendarscripts.info/ad-management-software.html" target="_blank">Check A.M.Y. here</a>.</p>
    
    <h2>Ad Campaigns</h2>
    <p>Ad campaigns are collections of ads. You can activate packages for each campaign so the ads in it will be rotated in the zone which the package is assigned to. </p>
    
    <h2>Manage Ads</h2>
    <p>You can create unlimited number of ads and assign them to a campaign. Only one ad is shown at a time (ads in active campaigns are rotated randomly in the ad zone). In the ad code you can put HTML or javascript code, so assigning Adsense-like ads is also possible.</p>
    
    <h2>Pricing plans</h2>
    <p>This is what you'll charge your advertisers if you sell direct ads. The duration in days defines for how long a campaign will be active when a package with the selected pricing plan is activated for it. The price in A.M.Y. Lite is for your reference (unlike in the Pro version where it's used for the payment buttons).</p>
    
    <h2>Ad Zones</h2>
    <p>You can define any number of zones where ads will appear on your blog. For example "Header", "Sidebar", "Footer" etc. </p>
    </div>
    <?php
}

register_activation_hook(__FILE__,'amylite_activate');
add_action('admin_menu', 'amylite_menu');
add_action('init', 'amylite_init');