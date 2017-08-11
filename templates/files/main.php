<?php
	wp_enqueue_style('gracemedia_style');
	wp_enqueue_style('fancybox_style');
	wp_enqueue_script('jquery.fancybox.js', plugins_url('gracemedia-media-player/fancybox1/jquery.fancybox-1.3.4.pack.js'));
	wp_enqueue_script('custom-script', plugins_url('gracemedia-media-player/templates/js/custom.js'), array('jquery'));
?>
<div class="wrap">
	<div class="banner">
		<img src="<?=plugins_url('gracemedia-media-player/images/new_gm_banner.png');?>" height="184" width="1060">
	</div>
	
	<div id="ajaxResponse"></div>
	
	<form class="widefat add">
		<input type="text" id="username" placeholder="Enter GraceMedia username to find Videos!" />
		<input type="submit" value="Submit" class="gm-button blue" id="submit" />
	</form>
	
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
					<span>Views</span>
				</th>
				<th>
					<span>Description</span>
				</th>
				<th>
					<span>Actions</span>
				</th>
			</tr>
		</thead>
		
		<tbody id="results">
			<tr class="no-rows">
				<td colspan="5">Enter a username above to find users media!</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	var pluginURL = '<?=plugins_url(); ?>';
	var configURL = '<?=ABSPATH;?>wp-config.php';
</script>