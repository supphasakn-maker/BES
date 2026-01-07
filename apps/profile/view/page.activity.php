<div class="card-body py-0 px-4 border-faded border-right-0 border-bottom-0 border-left-0">
	<div class="d-flex flex-column align-items-center">
	<?php
		$sql = "SELECT * FROM os_logs WHERE user_type=0 AND user=".$this->auth['id']." ORDER BY id DESC LIMIT 0,20";
		$rst = $this->dbc->Query($sql);
		while($log = $this->dbc->Fetch($rst)){
			echo '<div class="d-flex flex-row w-100 py-4">';
			echo '<div class="d-inline-block align-middle mr-3">';
				echo '<span class="profile-image profile-image-md rounded-circle d-block mt-1" style="background-image:url('.$this->auth['avatar'].'); background-size: cover;"></span>';
			echo '</div>';
			echo '<div class="mb-0 flex-1 text-dark">';
				echo '<div class="d-flex">';
					echo '<a href="javascript:void(0);" class="text-dark fw-500">'.$log['action'].' [ '.$log['value'].' ] </a>';
					echo '<span class="text-muted fs-xs opacity-70 ml-auto">'.date("F d,Y \a\\t H:i:s",strtotime($log['datetime'])).'</span>';
				echo '</div>';
				echo ' <p class="mb-0">'.$log['location'].'</p>';
			echo '</div>';
				
			echo '</div>';
			echo '<hr class="m-0 w-100">';
			
		}
	?>
	</div>
</div>