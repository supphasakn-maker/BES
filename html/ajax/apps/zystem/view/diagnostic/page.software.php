<h3>
	Software Testing Tool
</h3>
<p>
	Welcome to software diagnotics tool for OceanOS Nebula. This software was developed by Todsaporn S.
</p>
<div class="rows">
<?php
	$aCmd = array(
		'uname -r',
		'php -v',
		'cat /proc/version',
		'cat /etc/os-release',
		'cat /etc/issue'
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