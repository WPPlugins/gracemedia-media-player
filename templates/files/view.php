<?php
	wp_enqueue_style('gracemedia_style');
	wp_enqueue_style('fancybox_style');
	wp_enqueue_script('jquery.fancybox.js', plugins_url('gracemedia-media-player/fancybox1/jquery.fancybox-1.3.4.pack.js'));
	wp_enqueue_script('custom-script', plugins_url('gracemedia-media-player/templates/js/custom.js'), array('jquery'));
	
	$mediaTable = $wpdb->prefix . "gm_videos";
	$myVideos = $wpdb->get_results("SELECT * FROM `$mediaTable`");
?>
<div class="wrap">
	<div class="banner">
		<img src="<?=plugins_url('gracemedia-media-player/images/new_gm_banner.png');?>" height="184" width="1060">
	</div>
	
	<div id="ajaxResponse"></div>
	
	<table class="widefat fixed media" cellspacing="0">
		<thead>
			<tr>
				<th style='padding-left:5px;'>
					<span>Title</span>
				</th>
				<th>
					<span>Length</span>
				</th>
				<th>
					<span>Description</span>
				</th>
				<th>
					<span>Added</span>
				</th>
				<th>
					<span>Actions</span>
				</th>
			</tr>
		</thead>
		
		<tbody>
			<?
				if($myVideos) {
					foreach($myVideos as $video) {
						echo '<tr>';
							echo '<td>'.$video->title.'</td>';
							echo '<td>'.$video->length.'</td>';
							echo '<td>'.$video->description.'</td>';
							echo '<td>'.date(get_option('date_format'), strtotime($video->date_added)).'</td>';
							echo '<td><button class="removeId button" id="removefromPlaylist" data-id="'.$video->id.'">Delete</button> <a class="video button" href="http://www.gracemedia.tv/vault/'.$video->filename.'">Watch</a></td>';
						echo '</tr>';
					}
				} else {
					echo '<tr class="no-rows">';
						echo '<td colspan="5">No Media found!</td>';
					echo '</tr>';
				}
			?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var pluginURL = '<?=plugins_url(); ?>';
	var configURL = '<?=ABSPATH;?>wp-config.php';
</script>