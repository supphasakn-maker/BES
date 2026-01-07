<?php
	session_start();
	include_once "../../config/define.php";
	include_once "../../include/db.php";
	include_once "../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$server_name = "http://".SERVER_MARKET."/api/get_version.php";
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $server_name);
	curl_setopt($ch, CURLOPT_HEADER, 0);            // No header in the result 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result   
	
	$raw_data = curl_exec($ch);
	curl_close($ch);
	
	$data = json_decode($raw_data,true);
	
	$build = $os->load_variable("iBuild","number");
	
	if($data['build'] > $build){
		$updated = false;
	}else{
		$updated = true;
	}
	
	
?>
<div class="modal fade" id="dialog_update" data-backdrop="static">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Update System</h4>
      		</div>
		    <div class="modal-body">
			<?php
			if($updated){
				echo "No need to updated ?";
			}else{
				echo "The new version is available! Please Click Update to process?";
				
			}
				
				
			?>
		    </div>
			<div class="modal-footer">
				<?php
					if(!$updated){
						echo '<button type="button" class="btn btn-warning" onclick="fn.system.update()">Update</button>';
					}
				?>
				<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
			</div>
	  	</div>
	</div>
</div>
<?php
	$dbc->Close();
?>