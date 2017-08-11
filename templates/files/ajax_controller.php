<?php
	
	switch($_GET['ajaxAction']) {
	
		/*
		* Returns the list of ID's added to playlist already
		*/
		
		case "getIds":
			
			require_once($_GET['cfg']);
			
			global $wpdb;
			$Ids = array();
			$table_name = $wpdb->prefix . "gm_videos";
			$idList = $wpdb->get_results("SELECT `videoId` FROM `$table_name`");
			foreach($idList as $ID) {
				$Ids[] = $ID->videoId;
			}
			echo json_encode(array('IdList' => $Ids));
		break;	
		
		/*
		* Adds the Video ID to the WP database
		*/
		
		case "AddId":
			
			require_once($_GET['cfg']);
			
			global $wpdb;
			$table_name = $wpdb->prefix . "gm_videos";
			$date = date("y-m-d h:i:s");
		
			
			$insert = $wpdb->query($wpdb->prepare("
				INSERT INTO
					`$table_name`
				SET
					`date_added` = '$date', `title` = '%s', `videoId` = '%f', `thumbnail` = '%s', `length` = '%s', `description` = '%s', `filename` = '%s', `image` = '%s'
				", $_GET['title'], $_GET['id'], $_GET['thumb'], $_GET['length'], $_GET['description'], $_GET['filename'], $_GET['image'])
			);
			if($insert)
				echo "success";
			else 
				echo "error";
			
		break;
		
		/*
		* Removes video data from database
		*/
		
		case "removeId":
			
			require_once($_GET['cfg']);
			global $wpdb;
			$table_name = $wpdb->prefix."gm_videos";
			
			
			
			$result = $wpdb->delete($table_name, array('id' => $wpdb->escape($_GET['id'])));
			
			if($result)
				echo "success";
			else
				echo "error";
			
		break;

	}
	
?>