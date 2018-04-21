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
if(!$_session_check){
	die();
}
if(isset($_POST['action'])){
	if($_POST['action'] == "save"){
		if($_POST['current_password'] != "" AND $_POST['username'] != "" AND $_POST['new_password'] != "" AND $_POST['current_password'] != "" AND $_POST['new_password2'] != null AND $_POST['username'] != null AND $_POST['new_password'] != null AND $_POST['new_password2'] != null){
			if($_session_idents[1] == md5($_POST['current_password'])){
				if($_POST['new_password'] == $_POST['new_password2']){
					if ($tmp_file = fopen('/var/www/html/includes/idents.php', 'w')) {
						fwrite($tmp_file, '<?php $_idents = array(\'admin_user\' => \''.$_POST['username'].'\',\'admin_pass\' => \''.md5($_POST['new_password']).'\');?>'.PHP_EOL);
						unset($_idents['admin_user']);
						session_destroy();
						echo '<script>window.location.href = "index.php";</script>';
					}else{
						echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">Unable to save auths settings.</div>");</script>';
					}
				}else{
					echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">Please retype your password.</div>");</script>';
				}
			}else{
				echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">The current password is not correct.</div>");</script>';
			}
		}else{
			echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">Please fill in all fields.</div>");</script>';
		}
	}
	
	
}