<?php
	$noti = $dbc->GetRecord("notifications","COUNT(id)",'user = '.$os->auth['id'].' AND acknowledge IS NULL');
	$msg = $dbc->GetRecord("messages","COUNT(id)",'destination = '.$os->auth['id'].' AND acknowledge IS NULL');

?>
<div>
	<a href="#" class="header-icon" data-toggle="dropdown" title="You got 11 notifications">
		<i class="fal fa-bell"></i>
		<span class="badge badge-icon"><?php echo ($noti[0]+$msg[0])?></span>
	</a>
	<div class="dropdown-menu dropdown-menu-animated dropdown-xl">
		<div class="dropdown-header bg-trans-gradient d-flex justify-content-center align-items-center rounded-top mb-2">
			<h4 class="m-0 text-center color-white">
				<?php echo ($noti[0]+$msg[0])?> New
				<small class="mb-0 opacity-80">User Notifications</small>
			</h4>
		</div>
		<ul class="nav nav-tabs nav-tabs-clean" role="tablist">
			<li class="nav-item">
				<a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-messages" data-i18n="drpdwn.messages">Messages</a>
			</li>
			<li class="nav-item">
				<a class="nav-link px-4 fs-md js-waves-on fw-500" data-toggle="tab" href="#tab-feeds" data-i18n="drpdwn.feeds">Notification</a>
			</li>
		</ul>
		<div class="tab-content tab-notification">
			<div class="tab-pane active p-3 text-center">
				<h5 class="mt-4 pt-4 fw-500">
					<span class="d-block fa-3x pb-4 text-muted">
						<i class="ni ni-arrow-up text-gradient opacity-70"></i>
					</span> Select a tab above to activate
					<small class="mt-3 fs-b fw-400 text-muted">
						This blank page message helps protect your privacy, or you can show the first message here automatically through
						<a href="#">settings page</a>
					</small>
				</h5>
			</div>
			<div class="tab-pane" id="tab-messages" role="tabpanel">
				<div class="custom-scroll h-100">
					<ul class="notification">
					<?php
					$sql = "SELECT * FROM messages ORDER BY created DESC LIMIT 0,5";
					$rst = $dbc->Query($sql);
					while($msg = $dbc->Fetch($rst)){
						$source = $os->getAuthInfo($msg['source']);
						echo '<li class="'.(is_null($msg['opened'])?"unread":"").'">';
							echo '<a href="#apps/notify/index.php?view=message&section=view&id='.$msg['id'].'" class="d-flex align-items-center">';
								echo '<span class="status mr-2">';
									echo '<span class="profile-image rounded-circle d-inline-block" style="background-size: cover;background-image:url(\''.$source['avatar'].'\')"></span>';
								echo '</span>';
								echo '<span class="d-flex flex-column flex-1 ml-1">';
									echo '<span class="name">'.$source['display'].'</span>';
									echo '<span class="msg-a fs-sm">'.$msg['msg'].'</span>';
									echo '<span class="fs-nano text-muted mt-1">'.$msg['created'].'</span>';
								echo '</span>';
							echo '</a>';
						echo '</li>';
					}
					?>
					</ul>
				</div>
			</div>
			<div class="tab-pane" id="tab-feeds" role="tabpanel">
				<div class="custom-scroll h-100">
					<ul class="notification">
					<?php
					$sql = "SELECT * FROM notifications ORDER BY created DESC LIMIT 0,5";
					$rst = $dbc->Query($sql);
					while($noti = $dbc->Fetch($rst)){
						echo '<li class="'.(is_null($noti['acknowledge'])?"unread":"").'">';
							echo '<div class="d-flex align-items-center show-child-on-hover">';
								echo '<span class="d-flex flex-column flex-1">';
									echo '<span class="name d-flex align-items-center">';
										switch($noti['type']){
											case "notify":
												$class="badge-warning";
												break;
											case "alert":
												$class="badge-danger";
												break;
											case "schedule":
												$class="badge-primary";
												break;
										}
										echo '<span class="badge '.$class.' fw-n ml-1">'.$noti['type'].'</span>';
									echo '</span>';
									
									echo '<span class="msg-a fs-sm">'.$noti['topic'].'</span>';
									echo '<span class="fs-nano text-muted mt-1">'.$noti['created'].'</span>';
								echo '</span>';
								echo '<div class="show-on-hover-parent position-absolute pos-right pos-bottom p-3">';
									echo '<a href="#" class="text-muted" title="delete"><i class="fal fa-trash-alt"></i></a>';
								echo '</div>';
							echo '</div>';
						echo '</li>';
					}
					?>
					</ul>
				</div>
			</div>
			<div class="tab-pane" id="tab-events" role="tabpanel">
				<div class="d-flex flex-column h-100">
					<div class="h-auto">
						<table class="table table-bordered table-calendar m-0 w-100 h-100 border-0">
							<tr>
								<th colspan="7" class="pt-3 pb-2 pl-3 pr-3 text-center">
									<div class="js-get-date h5 mb-2">[your date here]</div>
								</th>
							</tr>
							<tr class="text-center">
								<th>Sun</th>
								<th>Mon</th>
								<th>Tue</th>
								<th>Wed</th>
								<th>Thu</th>
								<th>Fri</th>
								<th>Sat</th>
							</tr>
							<tr>
								<td class="text-muted bg-faded">30</td>
								<td>1</td>
								<td>2</td>
								<td>3</td>
								<td>4</td>
								<td>5</td>
								<td><i class="fal fa-birthday-cake mt-1 ml-1 position-absolute pos-left pos-top text-primary"></i> 6</td>
							</tr>
							<tr>
								<td>7</td>
								<td>8</td>
								<td>9</td>
								<td class="bg-primary-300 pattern-0">10</td>
								<td>11</td>
								<td>12</td>
								<td>13</td>
							</tr>
							<tr>
								<td>14</td>
								<td>15</td>
								<td>16</td>
								<td>17</td>
								<td>18</td>
								<td>19</td>
								<td>20</td>
							</tr>
							<tr>
								<td>21</td>
								<td>22</td>
								<td>23</td>
								<td>24</td>
								<td>25</td>
								<td>26</td>
								<td>27</td>
							</tr>
							<tr>
								<td>28</td>
								<td>29</td>
								<td>30</td>
								<td>31</td>
								<td class="text-muted bg-faded">1</td>
								<td class="text-muted bg-faded">2</td>
								<td class="text-muted bg-faded">3</td>
							</tr>
						</table>
					</div>
					<div class="flex-1 custom-scroll">
						<div class="p-2">
							<div class="d-flex align-items-center text-left mb-3">
								<div class="width-5 fw-300 text-primary l-h-n mr-1 align-self-start fs-xxl">
									15
								</div>
								<div class="flex-1">
									<div class="d-flex flex-column">
										<span class="l-h-n fs-md fw-500 opacity-70">
											October 2020
										</span>
										<span class="l-h-n fs-nano fw-400 text-secondary">
											Friday
										</span>
									</div>
									<div class="mt-3">
										<p>
											<strong>2:30PM</strong> - Doctor's appointment
										</p>
										<p>
											<strong>3:30PM</strong> - Report overview
										</p>
										<p>
											<strong>4:30PM</strong> - Meeting with Donnah V.
										</p>
										<p>
											<strong>5:30PM</strong> - Late Lunch
										</p>
										<p>
											<strong>6:30PM</strong> - Report Compression
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="py-2 px-3 bg-faded d-block rounded-bottom text-right border-faded border-bottom-0 border-right-0 border-left-0">
			<a href="#apps/notify/index.php" class="fs-xs fw-500 ml-auto">view all notifications</a>
		</div>
	</div>
</div>