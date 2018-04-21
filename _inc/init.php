<?php 
/**
* Rasphostpot
*
* RaspHotspot is a free responsive wifi hotspot control interface for raspberry pi
* Github: https://github.com/LeprovostNoam/RaspHotspot
* (c) copyright Leprovost Noam
* License: https://github.com/LeprovostNoam/RaspHotspot/blob/master/LICENSE
**/
@session_start();
include_once( 'config.php' );
include_once( 'idents.php' );
include_once( 'functions.php' );

$output = $return = 0;
$_session_check = false;
if(isset($_SESSION['identity'])){
	$_session_idents = explode("@", $_SESSION['identity']);
	if(($_idents['admin_user'] == $_session_idents[0]) && ($_idents['admin_pass'] == $_session_idents[1])){
		$_session_check = true;
	}
}

if (empty($_SESSION['csrf_token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['csrf_token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}
$csrf_token = $_SESSION['csrf_token'];
if(!isset($_GET['page'])){
	$_GET['page'] = "dashboard";
}

$current_version = file_get_contents("../_inc/version.txt");  