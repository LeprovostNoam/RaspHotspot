<?php
/**
* Rasphostpot
*
* RaspHotspot is a free responsive wifi hotspot control interface for raspberry pi
* Github: https://github.com/LeprovostNoam/RaspHotspot
* (c) copyright Leprovost Noam
* License: https://github.com/LeprovostNoam/RaspHotspot/blob/master/LICENSE
**/
include_once( '../_inc/init.php' );

if($_session_check){
	echo '<script>$("#main_content").html("<div class=\"alert alert-success\">Shuting down the system..</div>");</script>';

	echo '<script>$(".ajax_disabled").attr("disabled", true);</script>'; 
	$result = shell_exec("sudo shutdown -h now");
}