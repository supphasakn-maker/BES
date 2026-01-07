<?php
	$sys = json_decode(file_get_contents("../../config/system.json"),true);
	$aApp = json_decode(file_get_contents("../index.json"),true);
	global $dbc,$os;
	
	
	$lang = $_SESSION['lang'];
	
	function loadMenu($app){
		echo '<li class="list-group-item'.(isset($app['submenu'])?" dd-category":"").'"';
			echo ' data-app="'.$app['appname'].'"';
			echo ' data-icon="'.$app['icon'].'"';
			
			if(is_array($app['name'])){
				echo ' data-name_th="'.$app['name']['th'].'"';
				echo ' data-name_en="'.$app['name']['en'].'"';
			}else{
				echo ' data-name_th="'.$app['name'].'"';
				echo ' data-name_en="'.$app['name'].'"';
			}
			
			if(isset($app['submenu'])){
				echo ' data-type="category"';
			}else{
				echo ' data-type="app"';
				echo ' data-path="'.$app['path'].'"';
			}
		echo '>'; 
			echo '<div class="dd-handle">';
				echo '<div class="input-group">';
					echo '<div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-arrows-alt"></i></span></div>';
					echo '<input type="text" class="form-control" value="'.(is_array($app['name'])?($_SESSION['lang']=="th"?$app['name']['th']:$app['name']['en']):$app['name']).'" readonly>';
					echo '<div class="input-group-append">';
						echo '<span class="input-group-text"><i class="fa '.$app['icon'].'"></i></span>';
						echo '<button class="btn btn-outline-dark" type="button" onclick="fn.app.setting.system.setting(this)">Edit</button>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			if(isset($app['submenu'])){
				echo '<ul class="list-group mt-2 dd-list">';
				foreach($app['submenu'] as $submenu){
					loadMenu($submenu);
				}
				echo '</ul>';
			}else{}
		echo '</li>';
	};
	
	$sql = "SELECT * FROM apps";
	$rst_app = $dbc->Query($sql);
	while($line = $dbc->Fetch($rst_app)){
		array_push($aApp,json_decode($line['menu'],true));
	}

?>
<form lang="<?php echo $lang;?>" id="form_general" class="form-horizontal form-label-left" role="form" onsubmit="fn.app.setting.system.save_general();return false;">
<div class="row">
	<div class="col-lg-3 col-xl-3 order-lg-1 order-xl-1">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>Menu Managment <span class="fw-300"><i>GUI</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
		
					<div>
					<?php
						$available = $os->load_variable("bMenu");
							
					?>
						Available : 
						<select class="form-control" name="available">
							<option value="yes">Yes</option>
							<option value="no"<?php if($available!="yes")echo" selected";?>>No</option>
						</select>
					</div>	
		
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-9 col-xl-9 order-lg-1 order-xl-1">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>Extra Setting <span class="fw-300"><i>default</i></span></h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<div>
						<button type="button" class="btn btn-primary" onclick="fn.app.setting.system.dialog_add_app()" >Add Menu</button>
						<button type="button" class="btn btn-success" onclick="fn.app.setting.system.append_category()">Add Category</button>
						<button type="button" class="btn btn-danger" onclick="fn.app.setting.system.clearAll()">Reset</button>
						<button type="button" class="btn btn-warning" onclick="fn.app.setting.system.save()">Save</button>
					</div>
					<div class="dd" id="nestable">
						<ol class="list-group list-group-sm">
						<?php
							$menu = json_decode($os->load_variable("aMenu",$type="json"),true);
							foreach($menu as $app){
								loadMenu($app);
							}
						?>
						</ol>
					</div>
					
					
				</div>
			</div>
		</div>
	</div>
</div>
</form>

<div class="modal fade" id="dialog_setting" data-backdrop="static">
  	<div class="modal-dialog">
		<form id="form_setting" class="form-horizontal" role="form" onsubmit="return false;">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Setting</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Application</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="appname" value="" readonly>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Icon</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="icon" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Menu Name</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="name_en" value="" placeholder="English">
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="name_th" value="" placeholder="ภาษาไทย">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary btnSave">Save</button>
				<button type="button" class="btn btn-danger pull-left btnDelete" data-dismiss="modal">Delete</button>
			</div>
	  	</div>
		</form>
	</div>
</div>

<div class="modal fade" id="dialog_add_menu" data-backdrop="static">
  	<div class="modal-dialog">
		<form id="form_add_menu" class="form-horizontal" role="form" onsubmit="return false;">
		<input type="hidden" name="path">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Add New Menu</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label class="col-sm-2 control-label">Application</label>
					<div class="col-sm-10">
						<select name="cbbApplication" class="form-control">
						<?php
							foreach($aApp as $app){
								echo '<option
									data-appname="'.$app['appname'].'"
									data-name="'.$app['name'].'"
									data-icon="'.$app['icon'].'"
									data-path="'.$app['path'].'"
								value="'.$app['appname'].'">'.$app['name'].'</option>';
							}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Icon</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="icon" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Menu Name</label>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="name_en" value="" placeholder="English">
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control" name="name_th" value="" placeholder="ภาษาไทย">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary btnSave">Append</button>
				<button type="button" class="btn btn-danger pull-left btnDelete" data-dismiss="modal">Delete</button>
			</div>
	  	</div>
		
	</div>
</div>
<style>
.dd-list{
	background: #eee; padding: 5px;
	border: 1px solid #222;
}
</style>