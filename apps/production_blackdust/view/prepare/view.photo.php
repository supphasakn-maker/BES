<div id="img_frame" class="row">
	<p class="text-center pl-2 m-2"><a href="javascript:;" onclick="$('form[name=form_uploader] input[type=file]').click();"><i class="fas fa-plus fa-2x"></a></i></p>
	<?php
	
	if(!is_null($production['imgs'])){
		$imgs = json_decode($production['imgs'],true);
		//var_dump($imgs);
		foreach($imgs as $img){
			$id = rand(0,999999);
			echo '<a data-toggle="tooltip" data-placement="top" id="'.$id.'" title="'.$img['desc'].'" onclick="fn.app.production.produce.dialog_file(\'#'.$id.'\')" href="javascript:;" class="m-2">';
				echo '<input type="hidden" xname="img_path" name="img_path[]" value="'.$img['path'].'">';
				echo '<input type="hidden" xname="img_desc" name="img_desc[]" value="'.$img['desc'].'">';
				echo '<img style="height:100px;" class="img-thumbnail" src="'.$img['path'].'">';
			echo '</a>';
		}
	}
	?>

</div>