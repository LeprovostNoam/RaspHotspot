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
function RPiVersion() {
  $revisions = array(
    '0002' => 'Model B Revision 1.0',
    '0003' => 'Model B Revision 1.0 + ECN0001',
    '0004' => 'Model B Revision 2.0 (256 MB)',
    '0005' => 'Model B Revision 2.0 (256 MB)',
    '0006' => 'Model B Revision 2.0 (256 MB)',
    '0007' => 'Model A',
    '0008' => 'Model A',
    '0009' => 'Model A',
    '000d' => 'Model B Revision 2.0 (512 MB)',
    '000e' => 'Model B Revision 2.0 (512 MB)',
    '000f' => 'Model B Revision 2.0 (512 MB)',
    '0010' => 'Model B+',
    '0013' => 'Model B+',
    '0011' => 'Compute Module',
    '0012' => 'Model A+',
    'a01041' => 'a01041',
    'a21041' => 'a21041',
    '900092' => 'PiZero 1.2',
    '900093' => 'PiZero 1.3',
    '9000c1' => 'PiZero W',
    'a02082' => 'Pi 3 Model B',
    'a22082' => 'Pi 3 Model B'
  );
  exec('cat /proc/cpuinfo', $cpuinfo_array);
  $rev = trim(array_pop(explode(':',array_pop(preg_grep("/^Revision/", $cpuinfo_array)))));
  if (array_key_exists($rev, $revisions)) {
    return $revisions[$rev];
  } else {
    return 'Unknown Pi';
  }
}



  // hostname
  exec("hostname -f", $hostarray);
  $hostname = $hostarray[0];

  // uptime
  $uparray = explode(" ", exec("cat /proc/uptime"));
  $seconds = round($uparray[0], 0);
  $minutes = $seconds / 60;
  $hours   = $minutes / 60;
  $days    = floor($hours / 24);
  $hours   = floor($hours   - ($days * 24));
  $minutes = floor($minutes - ($days * 24 * 60) - ($hours * 60));
  $uptime= '';
  if ($days    != 0) { $uptime .= $days    . ' day'    . (($days    > 1)? 's ':' '); }
  if ($hours   != 0) { $uptime .= $hours   . ' hour'   . (($hours   > 1)? 's ':' '); }
  if ($minutes != 0) { $uptime .= $minutes . ' minute' . (($minutes > 1)? 's ':' '); }

  // mem used
  $memused_status = "primary";
  exec("free -m | awk '/Mem:/ { total=$2 ; used=$3 } END { print used/total*100}'", $memarray);
  $memused = floor($memarray[0]);
  if     ($memused > 90) { $memused_status = "danger";  }
  elseif ($memused > 75) { $memused_status = "warning"; }
  elseif ($memused >  0) { $memused_status = "success"; }

  // cpu load
  $cores   = exec("grep -c ^processor /proc/cpuinfo");
        $loadavg = exec("awk '{print $1}' /proc/loadavg");
  $cpuload = floor(($loadavg * 100) / $cores);
  if     ($cpuload > 90) { $cpuload_status = "danger";  }
  elseif ($cpuload > 75) { $cpuload_status = "warning"; }
  elseif ($cpuload >  0) { $cpuload_status = "success"; }

  $current_version = file_get_contents("../_inc/version.txt"); 
  $last_version = file_get_contents("https://raw.githubusercontent.com/LeprovostNoam/RaspHotspot/master/_inc/version.txt"); 
?>
<div id="page_alert"></div>
<div class="row">
	<div class="col-lg-6">
		<h4>System Information:</h4>
		<div class="info-item">Hostname: <?php echo $hostname ?></div>
		<div class="info-item">Pi Revision: <?php echo RPiVersion() ?></div>
		<div class="info-item">Uptime: <?php echo $uptime ?></div></br>
		<div class="info-item">Memory Used</div>
		<div class="progress">
			<div class="progress-bar progress-bar-<?php echo $memused_status ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $memused ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $memused ?>%;"><?php echo $memused ?>% </div>
		</div>
		<div class="info-item">CPU Load</div>
		<div class="progress">
			<div class="progress-bar progress-bar-<?php echo $cpuload_status ?> progress-bar-striped active"role="progressbar"aria-valuenow="<?php echo $cpuload ?>" aria-valuemin="0" aria-valuemax="100"style="width: <?php echo $cpuload ?>%;"><?php echo $cpuload ?>%</div>
		</div>

        <button type="button" class="btn btn-outline btn-danger ajax_disabled" onclick="hard_reboot()" id="button_hard_reboot">Hard reboot</button>
		<button type="button" class="btn btn-outline btn-danger ajax_disabled" onclick="shutdown()" id="button_shutdown">Shutdown</button>
		<button type="button" class="btn btn-outline btn-success ajax_disabled" onclick="page_load('system');">Refresh</button>
		<hr/>
		<h4>Panel Update:</h4>
		<div class="info-item">Current version: <?php echo $current_version; ?></div>
		<?php if($last_version != "" and $last_version != null){ ?>
		<div class="info-item">Last version: <?php echo $last_version; ?></div>
		<br>
		<?php if($current_version == $last_version){ ?>
			RaspHotspot is up to date
		<?php }else{?>
			<center>
				An update for RaspHotspot is available, click on the button below to update
				<button type="button" class="btn btn-success btn-sm" onclick="update_modal()">Update now</button>
			</center>
		<?php } ?>
		<?php } ?>
	</div>
	<div class="col-lg-6">
	<h4>Configure Auth:</h4>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Current password</label>
				<input type="password" class="form-control" id="current_password">
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Username</label>
				<input type="text" class="form-control" id="username" value="<?php echo $_session_idents[0]; ?>">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">New password</label>
				<input type="password" class="form-control" id="new_password">
			</div>
		</div>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="code">Retype password</label>
				<input type="password" class="form-control" id="new_password2">
			</div>
		</div>
		<button type="button" class="btn btn-outline btn-success btn-block ajax_disabled" onclick="save_auths_settings()" id="button_save_auths_settings">Save auths settings</button>
	</div>
</div>
    