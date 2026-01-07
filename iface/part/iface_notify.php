<?php

	$oldest_show_date = 30;
	$noti = $dbc->GetRecord("os_notifications","COUNT(id)",'user = '.$os->auth['id'].' AND acknowledge IS NULL');
	$msg = $dbc->GetRecord("os_messages","COUNT(id)",'destination = '.$os->auth['id'].' AND opened IS NULL');
?>
				<li class="nav-item dropdown nav-notif">
					<a class="nav-link nav-link-faded nav-icon has-badge dropdown-toggle no-caret" href="#" data-toggle="dropdown" data-display="static">
						<i data-feather="bell"></i>
						<?php
						if(($noti[0]+$msg[0])>0){
							?>
							<span class="badge badge-pill badge-danger"><?php echo ($noti[0]+$msg[0])?></span>
							<?php
						}
						?>		
					</a>
					<div class="dropdown-menu dropdown-menu-right p-0">
						<div class="card">
							<div class="card-header bg-primary text-white">
								<i data-feather="bell" class="mr-2"></i><?php echo $noti[0]?> notifications + <?php echo $msg[0]?> messages
							</div>
							<div class="card-body p-0 pt-1">
								<div class="list-group list-group-sm list-group-flush">
								<?php
									$sql = "SELECT * FROM os_notifications 
									WHERE
										user=".$os->auth['id']." 
										AND   created > CURRENT_DATE - INTERVAL ".$oldest_show_date." DAY
									ORDER BY created DESC LIMIT 0,5";
									$rst = $dbc->Query($sql);
									while($noti = $dbc->Fetch($rst)){
										switch($noti['type']){
											case "notify":
												$class = "bg-warning";
												$icon = '<i data-feather="bell"></i>';
												break;
											case "alert":
												$class = "bg-danger";
												$icon = '<i data-feather="info"></i>';
												break;
											case "schedule":
												$class = "bg-primary";
												$icon = '<i data-feather="calendar"></i>';
												break;
										}
										echo '<a href="javascript:void(0)" onclick="fn.notify.dialog_view('.$noti['id'].')" class="list-group-item list-group-item-action">';
										echo '<div class="media">';
										echo '<span class="'.$class.' text-white btn-icon rounded-circle">'.$icon.'</span>';
										echo '<div class="media-body ml-2">';
												echo '<p class="mb-0">'.$noti['topic'].'</p>';
												echo '<small class="text-secondary">';
													echo '<i class="material-icons icon-inline mr-1">access_time</i>'.$os->TimeElapsed(strtotime($noti['created']))." ago";
												echo '</small>';
											echo '</div>';
											echo '</div>';
										echo '</a>';
									}
								?>
								
								</div>
							</div>
							<div class="card-footer justify-content-center">
								<a href="#apps/notify/index.php?view=notification">View more &rsaquo;</a>
							</div>
							<div class="card-body p-0 pt-1">
								<div class="list-group list-group-sm list-group-flush">
								<?php
									$sql = "SELECT * FROM os_messages 
									WHERE opened IS NULL OR (destination=".$os->auth['id']." AND created > CURRENT_DATE - INTERVAL ".$oldest_show_date." DAY) 
									ORDER BY created DESC LIMIT 0,5";
									$rst = $dbc->Query($sql);
									while($msg = $dbc->Fetch($rst)){
										$sender = $os->getAuthInfo($msg['source']);
										echo '<a href="javascript:void(0)" onclick="fn.notify.dialog_message('.$msg['id'].')" class="list-group-item list-group-item-action">';
										echo '<div class="media">';
										echo '<span class="bg-outline-dark btn-icon rounded-circle"><i data-feather="mail"></i></span>';
										echo '<div class="media-body ml-2">';
												echo '<p class="mb-0">'.$sender['display'].'</p>';
												echo '<small class="text-secondary">';
													echo '<i class="material-icons icon-inline mr-1">access_time</i>'.$os->TimeElapsed(strtotime($msg['created']))." ago";
												echo '</small>';
											echo '</div>';
											echo '</div>';
										echo '</a>';
									}
								?>
								
								</div>
							</div>
							<div class="card-footer justify-content-center">
								<a href="#apps/notify/index.php?view=message">Open Message &rsaquo;</a>
							</div>
						</div>
					</div>
				</li>