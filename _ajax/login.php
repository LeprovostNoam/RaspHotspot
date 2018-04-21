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

if(($_POST['username'] == $_idents['admin_user']) && ($_idents['admin_pass'] == md5($_POST['password']))){
	$_SESSION['identity'] = $_POST['username'].'@'.md5($_POST['password']);
	echo '<script>location.reload();</script>';
}else{
	echo '<script>$("#alert_admin_modal").html("<div class=\"alert alert-danger\">Invalid username or password</div>");</script>';
}
?>