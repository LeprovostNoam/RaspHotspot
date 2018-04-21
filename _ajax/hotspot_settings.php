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
		$arrHostapdConf = parse_ini_file('/etc/raspap/hostapd.ini');

		$arrConfig = array();
		$arrSecurity = array( 1 => 'WPA', 2 => 'WPA2',3=> 'WPA+WPA2');
		$arrEncType = array('TKIP' => 'TKIP', 'CCMP' => 'CCMP', 'TKIP CCMP' => 'TKIP+CCMP');
		$arrChannel = array('a','b','g');
		$good_input = true;
		$alert_message = "";
		if (!(array_key_exists($_POST['wpa'], $arrSecurity) && array_key_exists($_POST['wpa_pairwise'], $arrEncType) && in_array($_POST['hw_mode'], $arrChannel))) {
			$alert_message = "Attempting to set hostapd config with wpa='".$_POST['wpa']."', wpa_pairwise='".$_POST['wpa_pairwise']."' and hw_mode='".$_POST['hw_mode'];
			$good_input = false;
		}
		if (strlen($_POST['ssid']) == 0 || strlen($_POST['ssid']) > 32) {
			$alert_message = "SSID must be between 1 and 32 characters";
			$good_input = false;
		}
		if (strlen($_POST['wpa_passphrase']) < 8 || strlen($_POST['wpa_passphrase']) > 63) {
			$alert_message = "WPA passphrase must be between 8 and 63 characters";
			$good_input = false;
		}
		
		if($good_input){
			if ($tmp_file = fopen('/tmp/hostapddata', 'w')) {
				fwrite($tmp_file, 'driver=nl80211'.PHP_EOL);
				fwrite($tmp_file, 'ctrl_interface='.RASPI_HOSTAPD_CTRL_INTERFACE.PHP_EOL);
				fwrite($tmp_file, 'ctrl_interface_group=0'.PHP_EOL);
				fwrite($tmp_file, 'beacon_int=100'.PHP_EOL);
				fwrite($tmp_file, 'auth_algs=1'.PHP_EOL);
				fwrite($tmp_file, 'wpa_key_mgmt=WPA-PSK'.PHP_EOL);

				fwrite($tmp_file, 'ssid='.$_POST['ssid'].PHP_EOL);
				fwrite($tmp_file, 'channel='.$_POST['channel'].PHP_EOL);
				fwrite($tmp_file, 'hw_mode='.$_POST['hw_mode'].PHP_EOL);
				fwrite($tmp_file, 'wpa_passphrase='.$_POST['wpa_passphrase'].PHP_EOL);
				fwrite($tmp_file, 'interface=wlan0'.PHP_EOL);
				fwrite($tmp_file, 'wpa='.$_POST['wpa'].PHP_EOL);
				fwrite($tmp_file, 'wpa_pairwise='.$_POST['wpa_pairwise'].PHP_EOL);
				fclose($tmp_file);

				system( "sudo cp /tmp/hostapddata " . RASPI_HOSTAPD_CONFIG, $return );
				
				if( $return == 0 ) {
					$result = shell_exec("sudo /sbin/reboot");
					echo '<script>$("#page_alert").html("<div class=\"alert alert-success\">Settings saved, you will be disconnected from the wifi network.</div>");</script>';
				} else {
					echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">Unable to save wifi hotspot settings.</div>");</script>';
				}
			} else {
				echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">Unable to save wifi hotspot settings.</div>");</script>';
			}
		
		}else{
			echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">'.$alert_message.'</div>");</script>';
		}
	}
}