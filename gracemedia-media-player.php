<?php
	/*
		Plugin Name: GraceMedia Videos
		Plugin URI: http://gracemedia.tv/plugin/
		Description: Grab your best uploads from GraceMedia to display directly on your blog inside it's own Media Player embedded with a Playlist.
		Author: Curtis Crewe
		Version: 1.0
		Author URI: http://curtiscrewe.co.uk/
		License: GPL2
	*/
	
	if(!class_exists('WP_GraceMedia_Videos')) {
		
		require('templates/files/update-notifier.php');
		
		class WP_GraceMedia_Videos {
			
			private $table;
			
			public function __construct() {
				global $wpdb;
				$this->table = $wpdb->prefix."gm_videos"; 
				
				add_action('admin_menu', array(&$this, 'add_menu_page'));
				add_action('init', array(&$this, 'loadPlayerJS'));
				add_shortcode('gm-playlist', array(&$this, 'playlist_handle'));
				wp_register_style('gracemedia_style', plugins_url('templates/css/gm.css', __FILE__) );
				wp_register_style('fancybox_style', plugins_url('fancybox1/jquery.fancybox-1.3.4.css', __FILE__) );
				wp_register_script('gm_videojs', plugins_url('jwplayer/jwplayer.html5.js' , __FILE__ ), array('jquery'));
				wp_register_script('gm_swfobject', plugins_url('jwplayer/jwplayer.js' , __FILE__ ));
			}
			
			public function loadPlayerJS() {
				wp_enqueue_script('gm_videojs');
				wp_enqueue_script('gm_swfobject');
			}
			
			public function add_menu_page() {
				$page_title = "Add Videos";
				$menu_title = "GM Videos";
				$capability = "manage_options";
				$menu_slug = "gracemedia-media-player/gracemedia-admin.php";
				$function = "";
				$icon_url = plugins_url('gracemedia-media-player/images/icon.png');
				$position = "66";
				
				add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position);
				add_submenu_page($menu_slug, __('Manage Videos'), 'Manage Videos', 'manage_options', 'gracemedia-media-player/gracemedia-view.php');
			}
			
			
			public function playlist_handle($atts) {
				extract(shortcode_atts(array(
					'width' => '641',
					'height' => '350',
				), $atts));

				$output = $this->gm_playlist($width, $height);
				return $output;
			}
			
			public function gm_playlist($width, $height) {
				global $wpdb;
				$myVideos = $wpdb->get_results("SELECT * FROM `$this->table`");
				$player = "
					<div id='gm_player' style='position:absolute;width:".$width.";height:".$height.";'>
						<h2>Loading Media Player.....</h2>
					</div>
					<script type='text/javascript'>
						jQuery(document).ready(function($) {
							jwplayer('gm_player').setup({
								html5player: '".plugins_url('jwplayer/jwplayer.html5.js' , __FILE__ )."',
								width: '".$width."',
								height: '".$height."',
								wmode: 'opaque',
								stretching: 'fill',
								skin: '".plugins_url('jwplayer/skin/NewTubeDark.xml' , __FILE__ )."',
								dock: {
									position: 'left'
								},
    								playlist: [";
								foreach($myVideos as $video) {
        								$player.= "{";
        								$player.= "file: 'http://gracemedia.tv/vault/".$video->filename."',";
        								$player.= "image: '".$video->image."',";
        								$player.= "title: '".$video->title."',";
        								$player.= "description: '".$video->description."'";
        								$player.= "},";

    								}
								$player.="],
								listbar: {
									position: 'bottom',
									size: 80
								}
							});
						});
					</script>
				";
				return $player;
			}
			
			public static function deactivate() {
				global $wpdb;
				$wpdb->query("DROP TABLE $wpdb->prefix.'gm_videos'");
			}
			
			public static function activate() {
				global $wpdb;
				
				$sql = "CREATE TABLE $wpdb->prefix.'gm_videos' (
  					id int(11) NOT NULL AUTO_INCREMENT,
  				 	date_added datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  					title VARCHAR(255) NOT NULL,
  					videoId int(11) NOT NULL,
  					thumbnail VARCHAR(55) DEFAULT '' NOT NULL,
  					filename VARCHAR(255) DEFAULT '' NOT NULL,
  					length VARCHAR(255) DEFAULT '' NOT NULL,
  					description TEXT DEFAULT '' NOT NULL,
  					image VARCHAR(255) DEFAULT '' NOT NULL,
  					UNIQUE KEY id (id)
  				);";
				require_once(ABSPATH.'wp-admin/includes/upgrade.php' );
				dbDelta($sql);
			}
			
		}
		
		register_activation_hook(__FILE__, array('WP_GraceMedia_Videos', 'activate'));
		register_activation_hook(__FILE__, array('WP_GraceMedia_Videos', 'deactivate'));
		
		$wp_gracemedia_videos = new WP_GraceMedia_Videos();
		
	}
?>