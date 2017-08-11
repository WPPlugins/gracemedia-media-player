<?php
	if(is_admin()){

    		if((isset($_GET['act'])) && ($_GET['act']!="")) {$page = $_GET['act'];} else {$page = "main";}
    		
    		//Load the page template
    		if(is_file(dirname(__FILE__) . '/templates/files/'.$page.'.php')) {
    			require dirname(__FILE__) . '/templates/files/'.$page.'.php';
    		} else {
    			require dirname(__FILE__) . '/templates/files/main.php';
    		}

	}
	
?>