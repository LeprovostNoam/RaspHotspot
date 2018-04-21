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
		$config = 'interface='.$_POST['interface'].PHP_EOL .'dhcp-range='.$_POST['RangeStart'].','.$_POST['RangeEnd'].',255.255.255.0,'.$_POST['RangeLeaseTime'].''.$_POST['RangeLeaseTimeUnits'];
		exec( 'echo "'.$config.'" > /tmp/dhcpddata',$temp );
		system( 'sudo cp /tmp/dhcpddata '. RASPI_DNSMASQ_CONFIG, $return );
		if( $return == 0 ) {
			echo '<script>$("#page_alert").html("<div class=\"alert alert-success\">Settings saved, you will be disconnected from the wifi network.</div>");</script>';
			$result = shell_exec("sudo /sbin/reboot");
		}else{
			echo '<script>$("#page_alert").html("<div class=\"alert alert-danger\">Unable to save DHCP settings.</div>");</script>';
		}
	}
	
}