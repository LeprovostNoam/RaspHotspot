<?php
/**
* Rasphostpot
*
* RaspHotspot is a free responsive wifi hotspot control interface for raspberry pi
* Github: https://github.com/LeprovostNoam/RaspHotspot
* (c) copyright Leprovost Noam
* License: https://github.com/LeprovostNoam/RaspHotspot/blob/master/LICENSE
**/
$_current_version = file_get_contents("_inc/version.txt"); 
include_once( '_inc/init.php' );
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>RaspHotspot</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="/assets/css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="/assets/css/style.css">
	<link rel="stylesheet" href="/assets/font-awesome-4.7.0/css/font-awesome.min.css">
  </head>
  <body>

    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="index.php" class="navbar-brand"><i class="fa fa-wifi"></i> RaspHotspot <span class="label label-success">v<?php echo $_current_version; ?></span></a>     
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <ul class="nav navbar-nav navbar-right">
            <li>
				
				<?php if(!$_session_check){ ?>
					<a data-toggle="modal" data-target="#admin_login" href="#">Log in</a>
				<?php }else{ ?>
					<a href="logout">Log out (<?php echo $_session_idents[0]; ?>)</a>
				<?php } ?>
			</li>
          </ul>

        </div>
      </div>
    </div>


    <div class="container">
		<div class="page-header" id="banner">
	  <div class="row">
          <div class="col-lg-12">
            <div class="well bs-component" id="main_content">
				<ul class="nav nav-tabs">
					
					<li class="nav-item <?php if($_GET['page'] == "dashboard"){ echo "active";} ?>">
						<a class="nav-link" data-toggle="tab" href="#dashboard" onclick="page_load('dashboard');">Dashboard</a>
					</li>
					<?php if($_session_check){ ?>
						<?php if ( RASPI_HOTSPOT_ENABLED ){ ?>
							<li class="nav-item <?php if($_GET['page'] == "hostapd"){ echo "active";} ?>">
								<a class="nav-link" data-toggle="tab" href="#hostapd"  onclick="page_load('hostapd');">Hotspot</a>
							</li>
						<?php } ?>
						<?php if ( RASPI_DHCP_ENABLED ){ ?>
							<li class="nav-item <?php if($_GET['page'] == "dhcp"){ echo "active";} ?>">
								<a class="nav-link" data-toggle="tab" href="#dhcp"  onclick="page_load('dhcp');">DHCP</a>
							</li>
						<?php } ?>
						<?php if ( RASPI_DHCP_ENABLED ){ ?>
							<li class="nav-item <?php if($_GET['page'] == "system"){ echo "active";} ?>">
								<a class="nav-link" data-toggle="tab" href="#system"  onclick="page_load('system');">System</a>
							</li>
						<?php } ?>
					<?php }?>
				</ul>
				<div id="admin_page_content"></div>
            </div>
          </div>
        </div>
		</div>
      <footer>
        <div class="row">
          <div class="col-lg-12">

            <ul class="list-unstyled">
              <li class="pull-right"><a href="#top">
			  Back to top
			  </a></li>
            </ul>
            <p>Developed by <a href="//leprovost.pro">Leprovost Noam</a></p>
          </div>
        </div>

      </footer>


    </div>
	<div class="modal fade" id="admin_login" tabindex="-1" role="dialog" aria-labelledby="admin_login" aria-hidden="true">
	  <div class="modal-dialog" role="document">  
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Admin - Log in</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body">
			<div id="alert_admin_modal"></div>
			<br>
			<form class="bs-component">
              <div class="form-group">
                <label class="col-form-label col-form-label-lg" for="inputLarge">Username</label>
                <input class="form-control form-control-lg" type="text" value="admin" id="input_admin_modal_username">
              </div>
			  <div class="form-group">
                <label class="col-form-label col-form-label-lg" for="inputLarge">Password (Default: admin)</label>
                <input class="form-control form-control-lg" type="password" id="input_admin_modal_password">
              </div>
            </form>
		  </div>
		  <div class="modal-footer">
			<a class="btn btn-success ajax_disabled" id="button_admin_modal" onclick="login()">Log in</a>
		  </div>
		</div>
	  </div>
	</div>
	<div class="modal fade" id="update_modal" tabindex="-1" role="dialog" aria-labelledby="update" aria-hidden="true">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title">Update</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
		  </div>
		  <div class="modal-body" id="update_modal_body">
			<div id="alert_admin_modal"></div>
			<br>
			<form class="bs-component">
				<div class="form-group">
                    <label for="update_modal_license_textarea">GNU General Public License v3.0</label>
                    <textarea class="form-control" id="update_modal_license_textarea" rows="3" style="height:500px;">Loading.. please wait</textarea>
                </div>
            </form>
		  </div>
		  <div class="modal-footer" id="update_modal_footer">
			<a class="btn btn-success ajax_disabled" id="button_update_modal" onclick="launch_update()">Launch update</a>
		  </div>
		</div>
	  </div>
	</div>
	<div id="ajax_root"></div>

    <script src="/assets/js/jquery-3.2.1.min.js"></script>

    <script src="/assets/js/bootstrap.min.js"></script>



	<script>
	function login(){
		var username = $("#input_admin_modal_username").val();
		var password = $("#input_admin_modal_password").val();
		$("#button_admin_modal").html("Checking.. <i class=\"fa fa-circle-o-notch fa-spin\"></i>");
		$("#button_admin_modal").attr("disabled", true);
		$(".ajax_disabled").attr("disabled", true);
		$.ajax({
			url : '_ajax/login.php',
			type : 'POST',
			dataType : 'text',
			data: 'username=' + username + '&password=' + password,
			success : function(code, state){
				$("#button_admin_modal").html("Log in");
				$("#button_admin_modal").attr("disabled", false);
				$(".ajax_disabled").attr("disabled", false);
				$("#ajax_root").html(code);
				$("#ajax_root").html("");
			},
			error : function(result, state, error){
				$("#button_admin_modal").html("Log in");
				$("#button_admin_modal").attr("disabled", false);
				$(".ajax_disabled").attr("disabled", false);
			}

		});
	}
	function hard_reboot(){
		$("#button_hard_reboot").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Hard reboot");
		$("#button_hard_reboot").attr("disabled", true);
		$(".ajax_disabled").attr("disabled", true);
		$("#main_content").html("<div class=\"alert alert-success\">System is rebooting..</div>");
		$.ajax({ 
			url : '_ajax/hard_reboot.php',
			type : 'POST',
			dataType : 'text',
			data: 'action=reboot',
			success : function(code, state){
				$("#button_hard_reboot").html("Hard reboot");
				$("#button_hard_reboot").attr("disabled", false);
				$(".ajax_disabled").attr("disabled", false);
				$("#ajax_root").html(code);
				$("#ajax_root").html("");
			},
			error : function(result, state, error){
				$("#button_hard_reboot").html("Hard reboot");
				$("#button_hard_reboot").attr("disabled", false);
				$(".ajax_disabled").attr("disabled", false);
			}

		});
	}
	function shutdown(){
		$("#button_hard_reboot").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Shutdown");
		$("#button_hard_reboot").attr("disabled", true);
		$(".ajax_disabled").attr("disabled", true);
		$("#main_content").html("<div class=\"alert alert-success\">Shutting down the system..</div>");
		$.ajax({ 
			url : '_ajax/shutdown.php',
			type : 'POST',
			dataType : 'text',
			data: 'action=reboot',
			success : function(code, state){
				$("#button_hard_reboot").html("Shutdown");
				$("#button_hard_reboot").attr("disabled", false);
				$(".ajax_disabled").attr("disabled", false);
				$("#ajax_root").html(code);
				$("#ajax_root").html("");
			},
			error : function(result, state, error){
				$("#button_hard_reboot").html("Shutdown");
				$("#button_hard_reboot").attr("disabled", false);
				$(".ajax_disabled").attr("disabled", false);
			}

		});
	}
	function save_hotspot_settings(){
		var ssid = $("#ssid").val();
		var hw_mode = $("#hw_mode").val();
		var channel = $("#channel").val();
		var wpa = $("#wpa").val();
		var wpa_pairwise = $("#wpa_pairwise").val();
		var wpa_passphrase = $("#wpa_passphrase").val();
		$("#button_save_hotspot_settings").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Save hotspot settings");
		$("#button_save_hotspot_settings").attr("disabled", true);
		$(".ajax_disabled").attr("disabled", true);
		setTimeout(function(){
			$.ajax({ 
				url : '_ajax/hotspot_settings.php',
				type : 'POST',
				dataType : 'text',
				data: 'action=save&ssid=' + ssid + '&hw_mode=' + hw_mode + '&channel=' + channel + '&wpa=' + wpa + '&wpa_pairwise=' + wpa_pairwise + '&wpa_passphrase=' + wpa_passphrase,
				success : function(code, state){
					$("#button_save_hotspot_settings").html("Save hotspot settings");
					$("#button_save_hotspot_settings").attr("disabled", false);
					$(".ajax_disabled").attr("disabled", false);
					$("#ajax_root").html(code);
					$("#ajax_root").html("");
				},
				error : function(result, state, error){
					$("#button_save_hotspot_settings").html("Save hotspot settings");
					$("#button_save_hotspot_settings").attr("disabled", false);
					$(".ajax_disabled").attr("disabled", false);
				}

			});
		}, 1000);
	}
	function save_dhcp_settings(){
		var RangeStart = $("#RangeStart").val();
		var RangeEnd = $("#RangeEnd").val();
		var RangeLeaseTime = $("#RangeLeaseTime").val();
		var RangeLeaseTimeUnits = $("#RangeLeaseTimeUnits").val();
		$("#button_save_dhcp_settings").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Save DHCP settings");
		$("#button_save_dhcp_settings").attr("disabled", true);
		$(".ajax_disabled").attr("disabled", true);
		setTimeout(function(){
			$.ajax({ 
				url : '_ajax/dhcp_settings.php',
				type : 'POST',
				dataType : 'text',
				data: 'action=save&RangeStart=' + RangeStart + '&RangeEnd=' + RangeEnd + '&RangeLeaseTime=' + RangeLeaseTime + '&RangeLeaseTimeUnits=' + RangeLeaseTimeUnits,
				success : function(code, state){
					$("#button_save_dhcp_settings").html("Save DHCP settings");
					$("#button_save_dhcp_settings").attr("disabled", false);
					$(".ajax_disabled").attr("disabled", false);
					$("#ajax_root").html(code);
					$("#ajax_root").html("");
				},
				error : function(result, state, error){
					$("#button_save_dhcp_settings").html("Save DHCP settings");
					$("#button_save_dhcp_settings").attr("disabled", false);
					$(".ajax_disabled").attr("disabled", false);
				}

			});
		}, 1000);
	}
	function save_auths_settings(){
		var current_password = $("#current_password").val();
		var username = $("#username").val();
		var new_password = $("#new_password").val();
		var new_password2 = $("#new_password2").val();
		$("#button_save_auths_settings").html("<i class=\"fa fa-circle-o-notch fa-spin\"></i> Save auths settings");
		$("#button_save_auths_settings").attr("disabled", true);
		$(".ajax_disabled").attr("disabled", true);
		setTimeout(function(){
			$.ajax({ 
				url : '_ajax/auths_settings.php',
				type : 'POST',
				dataType : 'text',
				data: 'action=save&current_password=' + current_password + '&username=' + username + '&new_password=' + new_password + '&new_password2=' + new_password2,
				success : function(code, state){
					$("#button_save_auths_settings").html("Save auths settings");
					$("#button_save_auths_settings").attr("disabled", false);
					$(".ajax_disabled").attr("disabled", false);
					$("#ajax_root").html(code);
					$("#ajax_root").html("");
				},
				error : function(result, state, error){
					$("#button_save_auths_settings").html("Save auths settings");
					$("#button_save_auths_settings").attr("disabled", false);
					$(".ajax_disabled").attr("disabled", false);
				}

			});
		}, 1000);
	}
	function update_modal(){
		$.ajax({
			url : '_ajax/license.php',
			type : 'POST',
			dataType : 'text',
			data: 'open=true',
			success : function(code, state){
				$("#update_modal_license_textarea").val(code);
			},
			error : function(result, state, error){
			}

		});

		$('#update_modal').modal({
			show: 'true'
		}); 
	}
	function launch_update(){
		var body = "";
		$("#update_modal_footer").html("");
		body += "<div class=\"alert alert-success\">Update in progress...</div>";
		body += "<div class=\"progress\"><div id=\"update_modal_progressbar\" class=\"progress-bar progress-bar-success progress-bar-striped active\" role=\"progressbar\" aria-valuenow=\"10\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\">0% </div></div>";
		$("#update_modal_body").html(body);
		$.ajax({
			url : '_ajax/update.php',
			type : 'POST',
			dataType : 'text',
			data: 'open=true',
			success : function(code, state){
				$("#ajax_root").html(code);
			},
			error : function(result, state, error){
			}

		});
		update_progress(0);
	}
	function update_progress(progress){
		if(progress < 100){
			progress = progress+1;
			$("#update_modal_progressbar").html(progress + "%");
			$("#update_modal_progressbar").css("width", progress + "%");
			setTimeout(function(){
				update_progress(progress);
			}, 100);
		}
		if(progress == 100){
			window.location.href = "index.php?page=system";
		}
	}
	function page_load(page){

		$("#admin_page_content").html("<center><i class=\"text-success fa fa-circle-o-notch fa-spin fa-3x fa-fw\"></i></center>");
		$.ajax({
			url : '_pages/' + page + '.php',
			type : 'POST',
			dataType : 'text',
			data: 'open=true',
			success : function(code, state){
				$("#admin_page_content").html(code);
			},
			error : function(result, state, error){
			}

		});
		var state = {
		  "canBeAnything": true
		};
		history.pushState(state, "RaspHotspot", "/index.php?page=" + page);
	}
	page_load('<?php echo $_GET['page']; ?>');
	</script>
  </body>
</html>
