<div id="img_frame" class="row">
	<p class="text-center pl-2 m-2"><a href="javascript:;" onclick="$('form[name=form_uploader] input[type=file]').click();"><i class="fas fa-plus fa-2x"></a></i></p>
	<?php
	
	if(!is_null($claim['imgs'])){
		$imgs = json_decode($claim['imgs'],true);
		//var_dump($imgs);
		foreach($imgs as $imgs){
			$id = rand(0,999999);
			echo '<a data-toggle="tooltip" data-placement="top" id="'.$id.'" title="'.$imgs['desc'].'" onclick="fn.app.claim.product.dialog_file(\'#'.$id.'\')" href="javascript:;" class="m-2">';
				echo '<input type="hidden" xname="img_path" name="img_path[]" value="'.$imgs['path'].'">';
				echo '<input type="hidden" xname="img_desc" name="img_desc[]" value="'.$imgs['desc'].'">';
				echo '<img style="height:800px;" class="img-thumbnail" src="'.$imgs['path'].'">';
			echo '</a>';
		}
	}
	?>

</div>