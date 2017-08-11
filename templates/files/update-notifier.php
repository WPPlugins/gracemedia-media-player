<?php
/*
	Replace GM and GM by your plugin prefix to prevent conflicts between plugins using this script.
*/

// Constants for the plugin name, folder and remote XML url
define( 'GM_NOTIFIER_PLUGIN_NAME', 'GraceMedia Videos' ); // The plugin name
define( 'GM_NOTIFIER_PLUGIN_SHORT_NAME', '' ); // The plugin short name, only if needed to make the menu item fit. Remove this if not needed
define( 'GM_NOTIFIER_PLUGIN_FOLDER_NAME', 'gracemedia-media-player' ); // The plugin folder name
define( 'GM_NOTIFIER_PLUGIN_FILE_NAME', 'gracemedia-media-player.php' ); // The plugin file name
define( 'GM_NOTIFIER_PLUGIN_XML_FILE', 'http://gracemedia.tv/plugin/notify.xml' ); // The remote notifier XML file containing the latest version of the plugin and changelog
define( 'GM_PLUGIN_NOTIFIER_CACHE_INTERVAL', 600 ); // The time interval for the remote XML cache in the database (21600 seconds = 6 hours)

// Adds an update notification to the WordPress Dashboard menu
function GM_update_plugin_notifier_menu() {  
	if (function_exists('simplexml_load_string')) { // Stop if simplexml_load_string funtion isn't available
	    $xml 			= GM_get_latest_plugin_version(GM_PLUGIN_NOTIFIER_CACHE_INTERVAL); // Get the latest remote XML file on our server
		$plugin_data 	= get_plugin_data(WP_PLUGIN_DIR . '/' . GM_NOTIFIER_PLUGIN_FOLDER_NAME . '/' . GM_NOTIFIER_PLUGIN_FILE_NAME); // Read plugin current version from the style.css

		if( (string)$xml->latest > (string)$plugin_data['Version']) { // Compare current plugin version with the remote XML version
			if(defined('GM_NOTIFIER_PLUGIN_SHORT_NAME')) {
				$menu_name = GM_NOTIFIER_PLUGIN_SHORT_NAME;
			} else {
				$menu_name = GM_NOTIFIER_PLUGIN_NAME;
			}
			add_dashboard_page( GM_NOTIFIER_PLUGIN_NAME . ' Plugin Updates', $menu_name . ' <span class="update-plugins count-1"><span class="update-count">New Updates</span></span>', 'administrator', 'GM-plugin-update-notifier', 'GM_update_notifier');
		}
	}	
}
add_action('admin_menu', 'GM_update_plugin_notifier_menu');  



// Adds an update notification to the WordPress 3.1+ Admin Bar
function GM_update_notifier_bar_menu() {
	if (function_exists('simplexml_load_string')) { // Stop if simplexml_load_string funtion isn't available
		global $wp_admin_bar, $wpdb;

		if ( !is_super_admin() || !is_admin_bar_showing() || !is_admin() ) // Don't display notification in admin bar if it's disabled or the current user isn't an administrator
		return;

		$xml 		= GM_get_latest_plugin_version(GM_PLUGIN_NOTIFIER_CACHE_INTERVAL); // Get the latest remote XML file on our server
		$plugin_data 	= get_plugin_data(WP_PLUGIN_DIR . '/' . GM_NOTIFIER_PLUGIN_FOLDER_NAME . '/' .GM_NOTIFIER_PLUGIN_FILE_NAME); // Read plugin current version from the main plugin file

		if( (string)$xml->latest > (string)$plugin_data['Version']) { // Compare current plugin version with the remote XML version
			$wp_admin_bar->add_menu( array( 'id' => 'plugin_update_notifier', 'title' => '<span>' . GM_NOTIFIER_PLUGIN_NAME . ' <span id="ab-updates">New Updates</span></span>', 'href' => get_admin_url() . 'index.php?page=GM-plugin-update-notifier' ) );
		}
	}
}
add_action( 'admin_bar_menu', 'GM_update_notifier_bar_menu', 1000 );



// The notifier page
function GM_update_notifier() { 
	$xml 			= GM_get_latest_plugin_version(GM_PLUGIN_NOTIFIER_CACHE_INTERVAL); // Get the latest remote XML file on our server
	$plugin_data 	= get_plugin_data(WP_PLUGIN_DIR . '/' . GM_NOTIFIER_PLUGIN_FOLDER_NAME . '/' .GM_NOTIFIER_PLUGIN_FILE_NAME); // Read plugin current version from the main plugin file ?>

	<style>
		.update-nag { display: none; }
		#instructions {max-width: 670px;}
		h3.title {margin: 30px 0 0 0; padding: 30px 0 0 0; border-top: 1px solid #ddd;}
	</style>

	<div class="wrap">

		<div id="icon-tools" class="icon32"></div>
		<h2><?php echo GM_NOTIFIER_PLUGIN_NAME ?> Plugin Updates</h2>
	    <div id="message" class="updated below-h2"><p><strong>There is a new version of the <?php echo GM_NOTIFIER_PLUGIN_NAME; ?> plugin available.</strong> You have version <?php echo $plugin_data['Version']; ?> installed. Update to version <?php echo $xml->latest; ?></p></div>
		
		<div id="instructions">
		    <h3>Update Download and Instructions</h3>
		    <p><strong>Please note:</strong> make a <strong>backup</strong> of the Plugin inside your WordPress installation folder <strong>/wp-content/plugins/<?php echo GM_NOTIFIER_PLUGIN_FOLDER_NAME; ?>/</strong></p>
		    <p>Instructions to Update</p>
		    <p><strong>DELETE</strong> the '<?php echo GM_NOTIFIER_PLUGIN_FOLDER_NAME; ?>' folder located inside /wp-content/plugins/</p>
		    <p>Go ahead and upload the newly updated files to /wp-content/plugins/</p>
		    <p>Your plugin is now at the latest version!</p>
		</div>
	    
	    <h3 class="title">Changelog</h3>
	    <?php echo $xml->changelog; ?>

	</div>
    
<?php } 



// Get the remote XML file contents and return its data (Version and Changelog)
// Uses the cached version if available and inside the time interval defined
function GM_get_latest_plugin_version($interval) {
	$notifier_file_url = GM_NOTIFIER_PLUGIN_XML_FILE;	
	$db_cache_field = 'notifier-cache';
	$db_cache_field_last_updated = 'notifier-cache-last-updated';
	$last = get_option( $db_cache_field_last_updated );
	$now = time();
	// check the cache
	if ( !$last || (( $now - $last ) > $interval) ) {
		// cache doesn't exist, or is old, so refresh it
		if( function_exists('curl_init') ) { // if cURL is available, use it...
			$ch = curl_init($notifier_file_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			$cache = curl_exec($ch);
			curl_close($ch);
		} else {
			$cache = file_get_contents($notifier_file_url); // ...if not, use the common file_get_contents()
		}

		if ($cache) {			
			// we got good results	
			update_option( $db_cache_field, $cache );
			update_option( $db_cache_field_last_updated, time() );
		} 
		// read from the cache file
		$notifier_data = get_option( $db_cache_field );
	}
	else {
		// cache file is fresh enough, so read from it
		$notifier_data = get_option( $db_cache_field );
	}

	// Let's see if the $xml data was returned as we expected it to.
	// If it didn't, use the default 1.0 as the latest version so that we don't have problems when the remote server hosting the XML file is down
	if( strpos((string)$notifier_data, '<notifier>') === false ) {
		$notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><changelog></changelog></notifier>';
	}

	// Load the remote XML data into a variable and return it
	$xml = simplexml_load_string($notifier_data); 

	return $xml;
}

?>