<?php
	$section = isset($_GET['section'])?$_GET['section']:"";
	$message = $this->dbc->GetRecord("os_messages","*","id=".$_GET['id']);
	
	if(is_null($message['opened'])){
		$this->dbc->Update("os_messages",array("#opened"=>"NOW()"),"id=".$_GET['id']);
	}
?>

<div class="jumbotron">
	<h1 class="display-4">Message ID : <?php echo $message['id']?></h1>
	<p class="lead"><?php echo $message['msg']?></p>
	<hr class="my-4">
	<table>
		<tbody class="table">
			<tr>
				<th>Source</th><td><?php echo $message['source']?></td>
				<th>Destination</th><td><?php echo $message['destination']?></td>
			</tr>
			<tr>
				<th>Created</th><td><?php echo $message['created']?></td>
				<th>Updated</th><td><?php echo $message['updated']?></td>
			</tr>
			<tr>
				<th>Open</th><td><?php
					if(is_null($message['opened'])){
						echo "Just Now!";
					}else{
						echo $message['opened'];
					}
				?></td>
				<th>Acknowledge</th><td><?php echo $message['acknowledge']?></td>
			</tr>
		</tbody>
	</table>
	<p class="lead">
		<a class="btn btn-primary" href="javascript:;" onclick="history.back();" role="button">Back</a>
	</p>
</div>