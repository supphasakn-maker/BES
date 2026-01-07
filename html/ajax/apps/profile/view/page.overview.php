<?php
	global $os;
	$datetime = $os->LoadSetting('datetime');
	$no_friend = $this->dbc->GetCount("os_users","gid=". $this->auth['gid']);
	$no_user = $this->dbc->GetCount("os_users");
?>
<div class="row gutters-sm">
	<div class="col-md-4 mb-3">
		<div class="card">
			<div class="card-body">
				<div class="d-flex flex-column align-items-center text-center">
					<img src="<?php echo $this->auth['avatar'];?>" alt="Admin" class="rounded-circle" width="150">
					<div class="mt-3">
						<h4><?php echo $this->auth['display'];?></h4>
						<p class="text-secondary mb-1"><?php echo $this->auth['group'];?></p>
						<p class="text-muted font-size-sm"><?php echo $this->auth['address']['fulladdress'];?></p>
						<button class="btn btn-primary" onclick="fn.app.engine.file.dialog_file('contact',<?php echo $this->auth['contact']['id'];?>)">Change Photo</button>
						<button class="btn btn-outline-primary" onclick="fn.app.accctrl.user.dialog_edit(<?php echo $this->auth['id'];?>)">Edit</button>
					</div>
				</div>
			</div>
		</div>
		<div class="card mt-3">
			<ul class="list-group list-group-flush">
				
				<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
					<h6 class="mb-0"><i class="mr-2 icon-inline" data-feather="message-circle"></i>Skype</h6>
					<span class="text-secondary"><?php echo $this->auth['contact']['skype'];?></span>
				</li>
			
				<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
					<h6 class="mb-0"><i class="mr-2 icon-inline text-danger" data-feather="instagram"></i>Google</h6>
					<span class="text-secondary"><?php echo $this->auth['contact']['google'];?></span>
				</li>
				<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
					<h6 class="mb-0"><i class="mr-2 icon-inline text-primary" data-feather="facebook"></i>Facebook</h6>
					<span class="text-secondary"><?php echo $this->auth['contact']['facebook'];?></span>
				</li>
			</ul>
		</div>
	</div>
	<div class="col-md-8">
		<div class="card mb-3">
			<div class="card-body">
				<div class="row">
					<div class="col-sm-3">
						<h6 class="mb-0">Full Name</h6>
					</div>
					<div class="col-sm-9 text-secondary">
						<?php echo $this->auth['display'];?>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<h6 class="mb-0">Email</h6>
					</div>
					<div class="col-sm-9 text-secondary">
						<?php echo $this->auth['email'];?>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<h6 class="mb-0">Phone</h6>
					</div>
					<div class="col-sm-9 text-secondary">
						<?php echo $this->auth['phone'];?>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<h6 class="mb-0">Mobile</h6>
					</div>
					<div class="col-sm-9 text-secondary">
						<?php echo $this->auth['mobile'];?>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-3">
						<h6 class="mb-0">Address</h6>
					</div>
					<div class="col-sm-9 text-secondary">
						<?php echo $this->auth['address']['fulladdress'];?>
					</div>
				</div>
			</div>
		</div>
		<div class="row gutters-sm">
			<div class="col-sm-6 mb-3">
				<div class="card h-100">
					<div class="card-body">
					<?php $today = time();?>
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
				</div>
			</div>
			<div class="col-sm-6 mb-3">
				<div class="card h-100 shadow-none bg-gray-300">
					<div class="card-body">
						<h6 class="d-flex align-items-center mb-3"><i class="material-icons text-warning mr-2">rss_feed</i>Recent Activities</h6>
						<div class="timeline timeline-left font-size-sm">
						<?php
						$sql = "SELECT * FROM os_logs WHERE user_type=0 AND user=".$this->auth['id']." ORDER BY id DESC LIMIT 0,5";
						$rst = $this->dbc->Query($sql);
						while($log = $this->dbc->Fetch($rst)){
							
							echo '<div class="timeline-container left">';
								echo '<div class="popover bs-popover-right popover-static">';
									echo '<div class="arrow"></div>';
									echo '<div class="popover-body text-muted">';
										echo '<a href="javascript:void(0)" class="text-body">'.$this->auth['name'].'</a> '.$log['action'].' [ '.$log['location'].' ]';
										echo '<div class="small">'.$os->TimeElapsed(strtotime($log['datetime'])).' ago</div>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
							
						}
						?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>