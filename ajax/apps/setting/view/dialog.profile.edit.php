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
	$address = $dbc->GetRecord("address","*","contact=".$contact['id']);
?>
<div class="modal fade" id="dialog_edit_profile" data-backdrop="static">
  	<div class="modal-dialog modal-lg">
		<form id="form_editprofile" class="form-horizontal" role="form" onsubmit="fn.app.setting.profile.edit();return false;">
		<input type="hidden" name="txtUserID" value="<?php echo $user['id'];?>">
		<input type="hidden" name="txtContactID" value="<?php echo $contact['id'];?>">
		<input type="hidden" name="txtAddressID" value="<?php echo $address['id'];?>">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Edit User</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Username</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="txtUsername" name="txtUsername" value="<?php echo $user['name'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Password</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Leave blank is No Change">
					</div>
				</div>
				
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Name</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="txtFirst" name="txtFirst" placeholder="Firstname" value="<?php echo $contact['name'];?>">
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" id="txtSurname" name="txtSurname" placeholder="Surname" value="<?php echo $contact['surname'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Title</label>
					<div class="col-sm-2">
						<select id="cbbTitle" name="cbbTitle" class="form-control">
							<option<?php if($contact['title']=="Mr.")echo" selected";?>>Mr.</option>
							<option<?php if($contact['title']=="Mrs.")echo" selected";?>>Mrs.</option>
							<option<?php if($contact['title']=="Miss.")echo" selected";?>>Miss.</option>
							<option<?php if($contact['title']=="Ms.")echo" selected";?>>Ms.</option>
						</select>
					</div>
					<label class="col-sm-1 control-label">Gender</label>
					<div class="col-sm-2">
						<select id="cbbGender" name="cbbGender" class="form-control">
							<option value="male"<?php if($contact['gender']=="male")echo" selected";?>>Male</option>
							<option value="female"<?php if($contact['gender']=="female")echo" selected";?>>Female</option>
						</select>
					</div>
					<label for="txtName" class="col-sm-1 control-label">DOB</label>
					<div class="col-sm-4">
						<input type="date" class="form-control" id="txtDOB" name="txtDOB" placeholder="Date of Birth" value="<?php echo $contact['dob'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Group</label>
					<div class="col-sm-5">
						<select id="cbbGroup" name="cbbGroup" class="form-control">
						<?php
							$sql= "SELECT * FROM groups";
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								$selected = $line['id']==$user['gid']?" selected":"";
								echo "<option value='$line[id]'$selected>$line[name]</option>";
							}
						?>
						</select>
					</div>
					<label class="col-sm-1 control-label">Nick</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="txtNick" name="txtNick" placeholder="Nickname" value="<?php echo $contact['nickname'];?>">
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label">Phone</label>
					<div class="col-sm-4">
						<input type="tel" class="form-control" id="txtPhone" name="txtPhone" placeholder="Phone" value="<?php echo $contact['phone'];?>">
					</div>
					<label class="col-sm-1 control-label">Mobile</label>
					<div class="col-sm-5">
						<input type="tel" class="form-control" id="txtMobile" name="txtMobile" placeholder="Mobile" value="<?php echo $contact['mobile'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">E-Mail</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="txtEmail" name="txtEmail" placeholder="E-Mail" value="<?php echo $contact['email'];?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Address</label>
					<div class="col-sm-10">
						<textarea name="txtAddress" rows="2" class="form-control"><?php echo $address['address'];?></textarea>
					</div>
				</div>
	
				<div class="form-group">
					<label class="col-sm-2 control-label">Country</label>
					<div class="col-sm-10">
						<select id="cbbCountry" name="cbbCountry" class="form-control">
						<?php
							$sql= "SELECT * FROM countries";
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								$selected = $line['id']==$address['country']?" selected":"";
								echo "<option value='$line[id]'$selected>$line[name]</option>";
							}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Province</label>
					<div class="col-sm-4">
						<select id="cbbCity" name="cbbCity" class="form-control">
						<?php
							$sql= "SELECT * FROM cities WHERE country =  ".$address['country'];
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								$selected = $line['id']==$address['city']?" selected":"";
								echo "<option value='$line[id]'$selected>$line[name]</option>";
							}
						?>
						</select>
					</div>
					<label class="col-sm-2 control-label">District</label>
					<div class="col-sm-4">
						<select id="cbbDistrict" name="cbbDistrict" class="form-control">
						<?php
							$sql= "SELECT * FROM districts WHERE city =  ".$address['city'];
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								$selected = $line['id']==$address['district']?" selected":"";
								echo "<option value='$line[id]'$selected>$line[name]</option>";
							}
						?>
						</select>
						
					</div>
					
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Subdistrict</label>
					<div class="col-sm-4">
						<select id="cbbSubdistrict" name="cbbSubdistrict" class="form-control">
						<?php
							$sql= "SELECT * FROM subdistricts WHERE district =  ".$address['district'];
							$rst = $dbc->Query($sql);
							while($line = $dbc->Fetch($rst)){
								$selected = $line['id']==$address['subdistrict']?" selected":"";
								echo "<option value='$line[id]'$selected>$line[name]</option>";
							}
						?>
						</select>
					</div>
					<label class="col-sm-2 control-label">Postal</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" id="txtPostal" name="txtPostal" placeholder="Zip Code" value="<?php echo $address['postal']?>">
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