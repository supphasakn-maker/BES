<h3>
	Physical Testing Tool
</h3>
<p>
	Welcome to physical diagnotics tool for OceanOS Nebula. This software was developed by Todsaporn S.
</p>
<div class="row">
<?php

	echo '<div class="col-6">';
		echo '<h4>CPU Information</h4>';
		$rs = shell_exec("lscpu");
		$rst = explode("\n",$rs);
		echo '<table class="table table-bordered">';
			echo '<thead>';
				echo '<tr>';
					echo '<th width="300" class="text-center">Name</th>';
					echo '<th class="text-center">Value</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($rst as $line){
				
				echo '<tr>';
					$content = explode(":",$line);
					echo '<td>';
						echo $content[0];
					echo '</td>';
					echo '<td>';
					echo $content[1];
					echo '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
		echo '</table>';
	echo '</div>';
	echo '<div class="col-6">';
		echo '<h4>Memory Info</h4>';
		$rs = shell_exec("vmstat -s");
		$rst = explode("\n",$rs);
		echo '<table class="table table-bordered">';
			echo '<thead>';
				echo '<tr>';
					echo '<th class="text-center">Value</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($rst as $line){
				echo '<tr>';
					echo '<td>';
					echo $line;
					echo '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
		echo '</table>';
	echo '</div>';

	$aCmd = array(
		'lscpu',
		'df -H',
		'free -m',
		'vmstat -s',
		'dmidecode -t 1',
		'dmidecode -t 2',
		'dmidecode -t 3',
		'dmidecode -t 4'
	);
echo '<div class="col-12">';
		echo '<h4>Operations System</h4>';
		echo '<table class="table table-bordered">';
			echo '<thead>';
				echo '<tr>';
					echo '<th width="300" class="text-center">Command</th>';
					echo '<th class="text-center">Value</th>';
				echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($aCmd as $cmd){
				$rs = shell_exec("$cmd");
				echo '<tr>';
					echo '<td>';
						echo $cmd;
					echo '</td>';
					echo '<td>';
					echo $rs;
					echo '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
		echo '</table>';
	echo '</div>';
	
	
	
?>


</div>