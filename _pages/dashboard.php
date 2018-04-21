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

  exec( 'ip a s ' . RASPI_WIFI_CLIENT_INTERFACE , $return );
  exec( 'iwconfig ' . RASPI_WIFI_CLIENT_INTERFACE, $return );

  $strWlan0 = implode( " ", $return );
  $strWlan0 = preg_replace( '/\s\s+/', ' ', $strWlan0 );

  preg_match( '/link\/ether ([0-9a-f:]+)/i',$strWlan0,$result ) || $result[1] = 'No MAC Address Found';
  $strHWAddress = $result[1];
  preg_match_all( '/inet ([0-9.]+)/i',$strWlan0,$result ) || $result[1] = 'No IP Address Found';
  $strIPAddress = '';
  foreach($result[1] as $ip) {
      $strIPAddress .= $ip." ";
  }
  preg_match_all( '/[0-9.]+\/([0-3][0-9])/i',$strWlan0,$result ) || $result[1] = 'No Subnet Mask Found';
  $strNetMask = '';
  foreach($result[1] as $netmask) {
    $strNetMask .= long2ip(-1 << (32 -(int)$netmask))." ";
  }

  $ip_api = file_get_contents('https://api.ipify.org?format=json');
  $internet_access = false;
  if($ip_api != null and $ip_api != ""){
	  $internet_access = true;
	  $public_ip = json_decode($ip_api)->ip;
  }
  ?>
<div id="page_alert">
<?php if(strpos( $strWlan0, "UP" ) !== false) { ?>
	<div class="alert alert-success">Interface is up</div>
<?php }else{ ?>
	<div class="alert alert-danger">Interface is down</div>
<?php } ?>
</div>
<div class="row">
    <div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-body">
				<h4>Interface Information</h4>
				<div class="info-item">Interface Name: <?php echo RASPI_WIFI_CLIENT_INTERFACE ?></div>
				<div class="info-item">Gateway Address: <?php echo $strIPAddress ?></div>
				<div class="info-item">Subnet Mask: <?php echo $strNetMask ?></div>
				<div class="info-item">Mac Address: <?php echo $strHWAddress ?></div>
			</div>
        </div>
	</div>

    <div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-body">
				<h4>Internet Access</h4>
				<div class="info-item">State: <?php if($internet_access == true){?><span class="label label-success">Internet OK</span><?php }else{ ?><span class="label label-danger">No internet</span><?php } ?></div>
				<div class="info-item">Public IP: <?php if($internet_access == true){ echo $public_ip;}else{ echo '/'; } ?></div>
			</div>
        </div>
	</div>
	<div class="col-lg-12">
		<button type="button" class="btn btn-outline btn-success ajax_disabled"  onclick="page_load('dashboard');">Refresh</button></center>
	</div>
</div>