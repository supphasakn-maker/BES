<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$user = $dbc->GetRecord("users","*","id=".$_SESSION['auth']['user_id']);
	$contact = $dbc->GetRecord("contacts","*","id=".$user['contact']);
	$setting = json_decode($user['setting'],true);
	
	if(!isset($setting['personalize'])){
		$setting['personalize'] = array(
			"qoute" => base64_encode(""),
			"explain" => base64_encode(""),
		);
	}
	
	$personalize = $setting['personalize'];
?>
<div class="modal fade" id="dialog_edit_profile" data-backdrop="static">
  	<div class="modal-dialog modal-lg">
		<form id="form_editprofile" class="form-horizontal" role="form" onsubmit="fn.app.setting.profile.detail.edit();return false;">
		<input type="hidden" name="txtUserID" value="<?php echo $user['id'];?>">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Edit Personlization</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Your Qoute</label>
					<div class="col-sm-10">
						<textarea name="txtQoute" rows="2" class="form-control"><?php echo base64_decode($personalize['qoute']);?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Explain about you</label>
					<div class="col-sm-10">
						<textarea name="txtExplain" rows="4" class="form-control"><?php echo base64_decode($personalize['explain']);?></textarea>
					</div>
				</div>
		    </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
	  	</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->