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
	echo '<script>window.location.href = "index.php";</script>';
	die();
}
  $arrHostapdConf = parse_ini_file('/etc/raspap/hostapd.ini');

  $arrConfig = array();
  $arrChannel = array('a','b','g');
  $arrSecurity = array( 1 => 'WPA', 2 => 'WPA2',3=> 'WPA+WPA2');
  $arrEncType = array('TKIP' => 'TKIP', 'CCMP' => 'CCMP', 'TKIP CCMP' => 'TKIP+CCMP');



  exec( 'cat '. RASPI_HOSTAPD_CONFIG, $return );
  exec( 'pidof hostapd | wc -l', $hostapdstatus);


  foreach( $return as $a ) {
    if( $a[0] != "#" ) {
      $arrLine = explode( "=",$a) ;
      $arrConfig[$arrLine[0]]=$arrLine[1];
    }
  };
  ?>
<div id="page_alert">
<?php if( $hostapdstatus[0] == 0 ) { ?>
	<div class="alert alert-danger">HostAPD is not running</div>
<?php }else{ ?>
	<div class="alert alert-success">HostAPD is running</div>
<?php } ?>
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">SSID</label>
				<input type="text" class="form-control" id="ssid" value="<?php echo $arrConfig['ssid']; ?>" />
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Wireless Mode</label>
				<?php SelectorOptions('hw_mode', $arrChannel, $arrConfig['hw_mode']); ?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Channel</label>
				<?php SelectorOptions('channel', range(1, 14), intval($arrConfig['channel'])) ?>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Security type</label>
				<?php SelectorOptions('wpa', $arrSecurity, $arrConfig['wpa']); ?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Encryption Type</label>
				<?php SelectorOptions('wpa_pairwise', $arrEncType, $arrConfig['wpa_pairwise']); ?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">PSK</label>
				<input type="text" class="form-control" id="wpa_passphrase" value="<?php echo $arrConfig['wpa_passphrase'] ?>" />
			</div>
		</div>
	</div>
</div>
<div class="tab-content">
	<button type="button" class="btn btn-outline btn-success ajax_disabled" id="button_save_hotspot_settings" onclick="save_hotspot_settings()">Save hotspot settings</button>
</div>
