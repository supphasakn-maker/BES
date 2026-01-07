<?php
	session_start();
	include_once "../../../config/define.php";
	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);
	
	$dbc = new dbc;
	$dbc->Connect();
	$os = new oceanos($dbc);
	
	$variable = $dbc->GetRecord("variable","*","name='aCompany_info'");
	$st = json_decode(base64_decode($variable['value']),true);
?>

<div class="modal fade" id="dialog_setting" data-backdrop="static">
  	<div class="modal-dialog">
		<form id="form_setting" class="form-horizontal" role="form" onsubmit="fn.app.setting.company.save();return false;">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Edit Organization</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtName" placeholder="Group Name" value="<?php echo $st['org_name']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Tax ID</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtTax" placeholder="TaxID" value="<?php echo $st['tax_id']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtAddress" class="col-sm-2 control-label">Address</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtAddress" placeholder="Address" value="<?php echo $st['address']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Phone</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtPhone" placeholder="Phone Number" value="<?php echo $st['phone']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Fax</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtFax" placeholder="Fax Number" value="<?php echo $st['fax']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Email</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtEmail" placeholder="Email" value="<?php echo $st['email']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Website</label>
					<div class="col-sm-10">
						<input type="website" class="form-control" name="txtWebsite" placeholder="Website" value="<?php echo $st['website']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Branch</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtBranch" placeholder="Branch" value="<?php echo $st['branch']?>">
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
<?php
	$dbc->Close();
?>