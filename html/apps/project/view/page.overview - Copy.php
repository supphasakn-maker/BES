<?php
	global $os;
	$datetime = $os->LoadSetting('datetime');
	$no_friend = $this->dbc->GetCount("users","gid=". $this->auth['gid']);
	$no_user = $this->dbc->GetCount("users");
	
?>
<meta name="google-signin-scope" content="profile email">
		<meta name="google-signin-client_id" content="<?php echo GOOGLE_CLIENT_ID;?>">
		<script src="https://apis.google.com/js/platform.js" async defer></script>
<div class="row">
	<div class="col-lg-6 col-xl-3 order-lg-1 order-xl-1">
		<!-- profile summary -->
		<div class="card mb-g rounded-top">
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="d-flex flex-column align-items-center justify-content-center p-4">
						<a href="javascript:;" onclick="fn.app.engine.file.dialog_file('profile',<?php echo $this->auth['id'];?>)">
							<img id="myProfilePhoto" src="<?php echo $this->auth['avatar'];?>" onerror="this.src='img/default/user.png'" class="rounded-circle shadow-2 img-thumbnail" alt="">
						</a>
						<h5 class="mb-0 fw-700 text-center mt-3">
							<?php echo $this->auth['display'];?>
							<small class="text-muted mb-0"><?php echo $this->auth['group'];?></small>
						</h5>
						<div class="mt-4 text-center">
						<?php
		
							if($this->auth['contact']['skype']!=null)
								echo '<a href="javascript:void(0);" class="fs-xl" style="color:#00AFF0"><i class="fab fa-skype"></i></a>';
							if($this->auth['contact']['facebook']!=null)
								echo '<a href="javascript:void(0);" class="fs-xl" style="color:#3b5998"><i class="fab fa-facebook"></i></a>';
							if($this->auth['contact']['google']!=null)
								echo '<a href="javascript:void(0);" class="fs-xl" style="color:#db3236"><i class="fab fa-google-plus"></i></a>';
						?>
						</div>
					</div>
				</div>
				<div class="col-6">
					<div class="text-center py-3">
						<h5 class="mb-0 fw-700">
							<?php echo $no_friend;?>
							<small class="text-muted mb-0">In Group</small>
						</h5>
					</div>
				</div>
				<div class="col-6">
					<div class="text-center py-3">
						<h5 class="mb-0 fw-700">
							<?php echo $no_user;?>
							<small class="text-muted mb-0">Users</small>
						</h5>
					</div>
				</div>
				<div class="col-12">
					<div class="p-3 text-center">
						<a href="javascript:;" onclick="fn.app.profile.dialog_setting(<?php echo $this->auth['id'];?>)" class="btn btn-block btn-warning font-weight-bold">Setting</a>
						<a href="javascript:;" onclick="fn.app.profile.dialog_setquote(<?php echo $this->auth['id'];?>)" class="btn btn-block btn-info font-weight-bold">SetQuote</a>
						<a href="javascript:;" onclick="fn.app.accctrl.user.dialog_edit(<?php echo $this->auth['id'];?>)" class="btn btn-block btn-dark font-weight-bold">Edit</a>
					</div>
				</div>
			</div>
			<div class="row no-gutters row-grid">
				<div class="col-12">
					<div class="p-3 text-center">
						
						
				
						
					</div>
				</div>
			</div>
		</div>
		
		<div class="card mb-g mt-5">
			<div class="card-header">Dianotics Tool</div>
			<div class="card-body pb-0 px-4">
				<div class="pb-3 pt-2 border-top-0 border-left-0 border-right-0 text-muted">
					<button type="button" class="btn btn-warning" onclick="fn.app.profile.mail.dialog_sendmail()">E-mail Testing</button>
				</div>
			</div>
		</div>
		<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
			<div class="">
				<h3 class="display-4 d-block l-h-n m-0 fw-500">
					<?php echo $os->getBrowser() ?>
					<small class="m-0 l-h-n">Browser</small>
				</h3>
			</div>
			<i class="fal fa-globe position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
		</div>
		<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
			<div class="">
				<h3 class="display-4 d-block l-h-n m-0 fw-500">
					<?php echo $os->getOS() ?>
					<small class="m-0 l-h-n">Operation System</small>
				</h3>
			</div>
			<i class="fal fa-window position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
		</div>
		<div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
			<div class="">
				<h3 class="display-4 d-block l-h-n m-0 fw-500">
					<?php echo $os->get_client_ip() ?>
					<small class="m-0 l-h-n">IP Address</small>
				</h3>
			</div>
			<i class="fal fa-cloud position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1" style="font-size:6rem"></i>
		</div>
		
		
		
	</div>
	<div class="col-lg-12 col-xl-9 order-lg-3 order-xl-2">
		
		<div class="card border mb-g">
			<div class="card-body pl-4 pt-4 pr-4 pb-0">
				<div class="d-flex flex-column">
					<div class="border-0 flex-1 position-relative shadow-top">
						<div class="pt-2 pb-1 pr-0 pl-0 rounded-0 position-relative" tabindex="-1">
							<span class="profile-image rounded-circle d-block position-absolute" style="background-image:url('img/demo/avatars/avatar-admin.png'); background-size: cover;"></span>
							<div class="pl-5 ml-5">
								<textarea class="form-control border-0 p-0 fs-xl" rows="4" placeholder="What's on your mind Codex?..."></textarea>
							</div>
						</div>
					</div>
					<div class="height-8 d-flex flex-row align-items-center flex-wrap flex-shrink-0">
						<a href="javascript:void(0);" class="btn btn-icon fs-xl width-1 mr-1" data-toggle="tooltip" data-original-title="More options" data-placement="top">
							<i class="fal fa-ellipsis-v-alt color-fusion-300"></i>
						</a>
						<a href="javascript:void(0);" class="btn btn-icon fs-xl mr-1" data-toggle="tooltip" data-original-title="Attach files" data-placement="top">
							<i class="fal fa-paperclip color-fusion-300"></i>
						</a>
						<a href="javascript:void(0);" class="btn btn-icon fs-xl mr-1" data-toggle="tooltip" data-original-title="Insert photo" data-placement="top">
							<i class="fal fa-camera color-fusion-300"></i>
						</a>
						<button class="btn btn-info shadow-0 ml-auto">Post</button>
					</div>
				</div>
			</div>
		</div>
		<?php
			$today = time();
		?>
		<div class="card border mb-g">
			<div class="card-header">Configuration Preview</div>
			<div class="card-body">
				<table class="table">
					<thead>
						<tr>
							<th>Content</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
						<tr><td>ISO-8601</td><td><?php echo date("c");?></td></tr>
						<tr><td>RFC 2822</td><td><?php echo date("c");?></td></tr>
						<tr><td>PHP Time</td><td><?php echo $today;?></td></tr>
						<tr><td>Full Date</td><td><?php echo date($datetime['ldate'], $today);?></td></tr>
						<tr><td>Full Time</td><td><?php echo date($datetime['ltime'], $today);?></td></tr>
						<tr><td>Short Date</td><td><?php echo date($datetime['sdate'], $today);?></td></tr>
						<tr><td>Short Time</td><td><?php echo date($datetime['stime'], $today);?></td></tr>
					</tbody>
				</table>
			</div>
			<div class="card-footer">This info is calculated by server system!</div>
		</div>
	
	</div>
	<div class="col-lg-6 col-xl-3 order-lg-2 order-xl-3">
		
	</div>
</div>
