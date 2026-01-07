<?php
	session_start();
	include "../../config/define.php";
	include "../../include/db.php";
	include "../../include/oceanos.php";
	include "../../include/nebulaos.php";
	include "../../include/widgeteer.php";
	
	$dbc = new dbc;
	$dbc->Connect();
	
	$param = $_GET['param'];
	
	function show_array($array){
		$msgs = array();
		foreach($array as $key => $item){
			if(!is_numeric($key)){
				$message = "";
				$message .= $key;
				$message .= " => ";
				if(is_null($item)){
					$message .= "NULL";
				}else{
					$message .= $item;
				}
				array_push($msgs,$message);
			}
		}
		return join("\n",$msgs);
	}
	
	
?>
<script src="apps/accctrl/include/interface.js"></script>
<div class="row">
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-4">
	<h1 class="page-title txt-color-blueDark">
		<i class="fa-fw fa fa-home"></i>
		Home
		<span> > Search</span>
		<span> > "<?php echo $param;?>"</span>
	</h1>
	</div>
</div>
<section class="">
	<div class="row">
		<article class="col-sm-12 col-md-12 col-lg-12">
		<table id="tblSearch" class="table table-striped table-bordered table-hover table-middle" width="100%">
			<thead>
				<tr>
					<th class="text-center hidden-xs">
						<span class="fa fa-sort-numeric-asc"></span>
					</th>
					<th class="text-center">Application</th>
					<th class="text-center">Ref ID</th>
					<th class="text-center hidden-xs">Overview</th>
					<th class="text-center">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$i = 0;
	
			$appname = "group";
			$sql = "SELECT * FROM groups WHERE name LIKE '%".$param."%'";
			$rst = $dbc->Query($sql);
			while($line = $dbc->Fetch($rst)){
			
				$i++;
				echo '<tr>';
					echo '<td class="text-center">'.$i.'</td>';
					echo '<td class="text-center">'.$appname.'</td>';
					echo '<td class="text-center">'.$line['id'].'</td>';
					echo '<td class="text-center">';
						echo '<pre>';
							echo show_array($line);
							
						echo '</pre>';
					echo '</td>';
					echo '<td class="text-center">';
						echo '<a href="#apps/group/index.php" class="btn btn-info">Go</a>';
					echo '</td>';
				echo '</tr>';
			
			}
			?>
			</tbody>
		</table>
		</article>
	</div>
</section>
<script>
App.stopLoading();
</script>
<?php
	$dbc->Close();
?>