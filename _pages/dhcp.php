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

  exec( 'pidof dnsmasq | wc -l',$dnsmasq );
  $dnsmasq_state = ($dnsmasq[0] > 0);

  exec( 'cat '. RASPI_DNSMASQ_CONFIG, $return );
  $conf = ParseConfig($return);
  $arrRange = explode( ",", $conf['dhcp-range'] );
  $RangeStart = $arrRange[0];
  $RangeEnd = $arrRange[1];
  $RangeMask = $arrRange[2];
  preg_match( '/([0-9]*)([a-z])/i', $arrRange[3], $arrRangeLeaseTime );

  $hselected = '';
  $mselected = '';
  $dselected = '';

  switch( $arrRangeLeaseTime[2] ) {
    case "h":
      $hselected = " selected";
    break;
    case "m":
      $mselected = " selected";
    break;
    case "d":
      $dselected = " selected";
    break;
  }

  ?>
<div id="page_alert">
<?php  if(!$dnsmasq_state ) { ?>
	<div class="alert alert-danger">Dnsmasq is not running</div>
<?php }else{ ?>
	<div class="alert alert-success">Dnsmasq is running</div>
<?php } ?>
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Starting IP Address</label>
				<input type="text" class="form-control"id="RangeStart" value="<?php echo $RangeStart; ?>" />
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Ending IP Address</label>
				<input type="text" class="form-control" id="RangeEnd" value="<?php echo $RangeEnd; ?>" />
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Lease Time</label>
				<input type="text" class="form-control" id="RangeLeaseTime" value="<?php echo $arrRangeLeaseTime[1]; ?>" />
			</div>
			<div class="col-md-12">
				<label for="code">Interval</label>
				<select id="RangeLeaseTimeUnits" class="form-control" ><option value="m" <?php echo $mselected; ?>>Minute(s)</option><option value="h" <?php echo $hselected; ?>>Hour(s)</option><option value="d" <?php echo $dselected; ?>>Day(s)</option><option value="infinite">Infinite</option></select> 
			</div>
		</div>
		<br><br>
		<button type="button" class="btn btn-outline btn-success ajax_disabled" id="button_save_dhcp_settings" onclick="save_dhcp_settings()">Save DHCP settings</button>
	</div>
	<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-heading">Active DHCP leases</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
							<tr>
								<th>Expire time</th>
								<th>MAC Address</th>
								<th>IP Address</th>
								<th>Host name</th>
								<th>Client ID</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							<?php
							exec( 'cat ' . RASPI_DNSMASQ_LEASES, $leases );
							foreach( $leases as $lease ) {
							$lease_items = explode(' ', $lease);
							foreach( $lease_items as $lease_item ) {
								echo '<td>' . $lease_item . '</td>';
							}
							echo '</tr>';
							};
							?>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>