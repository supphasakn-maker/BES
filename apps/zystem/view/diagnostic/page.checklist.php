<h3>
	Diagnotics Checklist for Preparing System
</h3>
<p>
	Welcome to physical diagnotics tool for OceanOS Nebula. This software was developed by Todsaporn S.
</p>
<div class="row">
	<div class="col-12">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="text-center">Topic</th>
					<th class="text-center">Requirement</th>
					<th class="text-center">Result</th>
					<th class="text-center">Pass</th>
				</tr>
			</thead>
			<tbody>
			<?php
				echo '<tr>';
					echo '<td>PHP Version</td>';
					echo '<td class="text-center"> >= 5.6 </td>';
					echo '<td class="text-center">';
						echo PHP_MAJOR_VERSION;
					echo '</td>';
					echo '<td class="text-center">';
						if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5){
							echo 'Pass';
						}else{
							echo 'Fail';
						}
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>Mysql Version</td>';
					echo '<td class="text-center"> Morethan 10 </td>';
					echo '<td class="text-center">';
						echo exec("mysql -V");
					echo '</td>';
					echo '<td class="text-center">';
						if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 5){
							echo 'Pass';
						}else{
							echo 'Fail';
						}
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					$file = '../../binary';
					$permiss = substr(sprintf('%o', fileperms('/tmp')), -4);
					echo '<td>File Permission : '.$file.'</td>';
					echo '<td class="text-center"> 1777 </td>';
					echo '<td class="text-center">';
						echo $permiss;
					echo '</td>';
					echo '<td class="text-center">';
						if($permiss == "1777"){echo 'Pass';}else{echo 'Fail';}
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					$file = '../../binary/contact';
					$permiss = substr(sprintf('%o', fileperms('/tmp')), -4);
					echo '<td>File Permission : '.$file.'</td>';
					echo '<td class="text-center"> 1777 </td>';
					echo '<td class="text-center">';
						echo $permiss;
					echo '</td>';
					echo '<td class="text-center">';
						if($permiss == "1777"){echo 'Pass';}else{echo 'Fail';}
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					$file = '../../binary/organization';
					$permiss = substr(sprintf('%o', fileperms('/tmp')), -4);
					echo '<td>File Permission : '.$file.'</td>';
					echo '<td class="text-center"> 1777 </td>';
					echo '<td class="text-center">';
						echo $permiss;
					echo '</td>';
					echo '<td class="text-center">';
						if($permiss == "1777"){echo 'Pass';}else{echo 'Fail';}
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					$file = '../../binary/tmp';
					$permiss = substr(sprintf('%o', fileperms('/tmp')), -4);
					echo '<td>File Permission : '.$file.'</td>';
					echo '<td class="text-center"> 1777 </td>';
					echo '<td class="text-center">';
						echo $permiss;
					echo '</td>';
					echo '<td class="text-center">';
						if($permiss == "1777"){echo 'Pass';}else{echo 'Fail';}
					echo '</td>';
				echo '</tr>';
				echo '<tr>';
					$output = shell_exec("ls -Z ../../binary/tmp");
					echo '<td>SELinux :: tmp</td>';
					echo '<td class="text-center"> - </td>';
					echo '<td class="text-center">';
						echo "<pre>$output</pre>";
					echo '</td>';
					echo '<td class="text-center">';
						
					echo '</td>';
				echo '</tr>';
				
				
			?>
			</tbody>
		</table>
	</div>
</div>