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
	
	if(!isset($setting['email'])){
		$setting['email'] = array(
			"email" => $contact['email'],
			"in" => array(
				"type" => 'imap',
				"server" => '',
				"username" => '',
				"password" => '',
				"security" => 'none',
				"port" => 143
			),
			"samesetting" => true,
			"out" => array(
				"type" => 'smtp',
				"server" => '',
				"username" => '',
				"password" => '',
				"security" => 'none',
				"port" => 25
			)
		);
	}
	
	$email = $setting['email'];
?>



<div class="modal fade" id="dialog_mailsetting" data-backdrop="static">
  	<div class="modal-dialog">
		<form id="form_mailsetting" class="form-horizontal" role="form" onsubmit="fn.app.setting.profile.mail.save_setting();return false;">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>E-mail Setting</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label for="txtEmail" class="col-sm-2 control-label">Email</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="txtEmail" placeholder="Your Email" value="<?php echo $email['email'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="cbbType" class="col-sm-2 control-label">Type</label>
					<div class="col-sm-10">
						<select class="form-control" name="cbbType">
							<option value="imap"<?php echo $email['in']['type']=="imap"?" selected":"";?>>IMAP</option>
							<option value="pop"<?php echo $email['in']['type']=="pop"?" selected":"";?>>POP3</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="txtIncoming" class="col-sm-4 control-label">Incoming Server</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="txtIncoming" placeholder="Title" value="<?php echo $email['in']['server'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtUsername" class="col-sm-2 control-label">Username</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="txtUsername" name="txtUsername" placeholder="Username" value="<?php echo $email['in']['username'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtPassword" class="col-sm-2 control-label">Password</label>
					<div class="col-sm-10">
						<input type="password" class="form-control" id="txtPassword" name="txtPassword" placeholder="Your Password" value="<?php echo $email['in']['password'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="cbbSecurity" class="col-sm-2 control-label">Security</label>
					<div class="col-sm-6">
						<select class="form-control" name="cbbSecurity">
							<option value="none"<?php echo $email['in']['security']=="none"?" selected":"";?>>None</option>
							<option value="ssl"<?php echo $email['in']['security']=="ssl"?" selected":"";?>>SSL/TLS</option>
						</select>
					</div>
					<label for="txtIncomingPort" class="col-sm-2 control-label">Port</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="txtIncomingPort" name="txtIncomingPort" value="<?php echo $email['in']['port'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtOutgoing" class="col-sm-4 control-label">SMTP Server</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="txtOutgoing" placeholder="Outgoing Server" value="<?php echo $email['out']['server'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label"></label>
					<div class="col-sm-10">
						<div class="checkbox">
							<input id="chkAuth" name="chkAuth" value="on" type="checkbox"<?php echo $email['samesetting']?" checked":"";?>>
							<label for="chkAuth"> Save As Incomming Server </label>
						</div>
					</div>
				</div>
				<div class="gSame form-group" style="<?php echo $email['samesetting']?"display: none;":"";?>">
					<label for="txtOutUsername" class="col-sm-2 control-label">Username</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="txtOutUsername" name="txtOutUsername" placeholder="Username" value="<?php echo $email['out']['username'];?>">
					</div>
				</div>
				<div class="gSame form-group" style="<?php echo $email['samesetting']?"display: none;":"";?>">
					<label for="txtOutPassword" class="col-sm-2 control-label">Password</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="txtOutPassword" name="txtOutPassword" placeholder="Your Password" value="<?php echo $email['out']['password'];?>">
					</div>
				</div>
				<div class="form-group">
					<label for="cbbSMTPSecurity" class="col-sm-2 control-label">SMTP</label>
					<div class="col-sm-6">
						<select class="form-control" name="cbbSMTPSecurity">
							<option value="none"<?php echo $email['out']['security']=="none"?" selected":"";?>>None</option>
							<option value="ssl"<?php echo $email['out']['security']=="ssl"?" selected":"";?>>SSL/TLS</option>
						</select>
					</div>
					<label for="txtOutgoingPort" class="col-sm-2 control-label">Port</label>
					<div class="col-sm-2">
						<input type="text" class="form-control" id="txtOutgoingPort" name="txtOutgoingPort" value="<?php echo $email['out']['port'];?>">
					</div>
				</div>
		    </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning pull-left" onclick="fn.app.setting.profile.mail.testserver()">Test Server</button>
			
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