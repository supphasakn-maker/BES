<?php
class engine{
	private $param = null;
	private $path = null;
	
	function __construct($p){
		$this->param = $p;
		exec("rm ../../../../binary/tmp/* -rf");
	}
	
	function create_zip(){
		exec("tar -zcvf ../../../../binary/tmp/".$this->param['appname'].".tar.gz ../../../../binary/tmp/".$this->param['appname']);
		return "binary/tmp/".$this->param['appname'].'.tar.gz';
	}
	
	
	function create_directory(){
		$this->path = "../../../../binary/tmp/".$this->param['appname'];
		mkdir($this->path);
		mkdir($this->path."/control");
		mkdir($this->path."/include");
		mkdir($this->path."/store");
		mkdir($this->path."/view");
		mkdir($this->path."/xhr");
		
	}
	
	function write_index(){
		$file = fopen($this->path."/index.php", "w") or die("Unable to open file!");
		$s = '<?php'."\n";
		$s .= '	session_start();'."\n";
		$s .= '	@ini_set(\'display_errors\',1);'."\n";
		$s .= '	include "../../config/define.php";'."\n";
		$s .= '	include "../../include/db.php";'."\n";
		$s .= '	include "../../include/oceanos.php";'."\n";
		$s .= '	include "../../include/iface.php";'."\n";
		$s .= "\n";
		$s .= '	$dbc = new dbc;'."\n";
		$s .= '	$dbc->Connect();'."\n";
		$s .= '	$os = new oceanos($dbc);'."\n";
		$s .= '	$panel = new ipanel($dbc,$os->auth);'."\n";
		$s .= "\n";
		$s .= '	$panel->setApp("'.$this->param['appname'].'","'.$this->param['name'].'");'."\n";
		$s .= '	$panel->setView(isset($_GET[\'view\'])?$_GET[\'view\']:\'user\');'."\n";
		$s .= "\n";
		$s .= '	$panel->setMeta(array('."\n";
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$s .= '		array("'.$subapp.'","'.$subcaption.'","far fa-user"),'."\n";
		}
		$s .= '	));'."\n";
		$s .= '?>'."\n";
		$s .= '<?php'."\n";
		$s .= '	$panel->PageBreadcrumb();'."\n";
		$s .= '?>'."\n";
		$s .= '<div class="row">'."\n";
		$s .= '	<div class="col-xl-12">'."\n";
		$s .= '	<?php'."\n";
		$s .= '		$panel->EchoInterface();'."\n";
		$s .= '	?>'."\n";
		$s .= '	</div>'."\n";
		$s .= '</div>'."\n";
		$s .= '<script>'."\n";
		$s .= '	var plugins = ['."\n";
		$s .= '		\'apps/'.$this->param['appname'].'/include/interface.js\','."\n";
		$s .= '		\'plugins/datatables/dataTables.bootstrap4.min.css\','."\n";
		$s .= '		\'plugins/datatables/responsive.bootstrap4.min.css\','."\n";
		$s .= '		\'plugins/datatables/jquery.dataTables.bootstrap4.responsive.min.js\','."\n";
		$s .= '		\'plugins/select2/css/select2.min.css\','."\n";
		$s .= '		\'plugins/select2/js/select2.min.js\','."\n";
		$s .= '		\'plugins/moment/moment.min.js\''."\n";
		$s .= '	];'."\n";
		
		$s .= '	App.loadPlugins(plugins, null).then(() => {'."\n";
		$s .= '		App.checkAll()'."\n";
		$s .= '	<?php'."\n";
		$s .= '		switch($panel->getView()){'."\n";
		
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$s .= '			case "'.$subapp.'":'."\n";
			$s .= '				include "control/controller.'.$subapp.'.view.js";'."\n";
			$s .= '				if($os->allow("'.$this->param['appname'].'","remove"))include "control/controller.'.$subapp.'.remove.js";'."\n";
			$s .= '				if($os->allow("'.$this->param['appname'].'","add"))include "control/controller.'.$subapp.'.add.js";'."\n";
			$s .= '				if($os->allow("'.$this->param['appname'].'","edit"))include "control/controller.'.$subapp.'.edit.js";'."\n";
			$s .= '				break;'."\n";
			
		}
		$s .= '}'."\n";
		$s .= '	?>'."\n";
		$s .= '	}).then(() => App.stopLoading())'."\n";
		$s .= '</script>'."\n";
		
		fwrite($file, $s);
		fclose($file);
	}

	function write_include(){
		$file = fopen($this->path."/include/interface.js", "w") or die("Unable to open file!");
		$s = '';
		$s .= '';
		$s .= 'var '.$this->param['appname'].' = {'."\n";
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$s .= '	'.$subapp.' : {'."\n";
			$s .= '		dialog_lookup : fn.noaccess,'."\n";
			$s .= '		dialog_add : fn.noaccess,'."\n";
			$s .= '		dialog_edit : fn.noaccess,'."\n";
			$s .= '		dialog_remove : fn.noaccess,'."\n";
			$s .= '		add : fn.noaccess,'."\n";
			$s .= '		edit : fn.noaccess,'."\n";
			$s .= '		remove : fn.noaccess'."\n";
			$s .= '	},'."\n";
		}
		$s .= '};'."\n";
		$s .= '$.extend(fn.app,{'.$this->param['appname'].':'.$this->param['appname'].'});'."\n";
		fwrite($file, $s);
		fclose($file);
	}
	
	function write_control(){
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$tablename = "tbl".ucwords($this->param['subapp'][$i]);
			$bigsubapp = ucwords($this->param['subapp'][$i]);
			
			$file = fopen($this->path."/control/controller.".$subapp.".view.js", "w") or die("Unable to open file!");
			$s = '';
			$s .= '$("#'.$tablename.'").data( "selected", [] );'."\n";
			$s .= '$("#'.$tablename.'").DataTable({'."\n";
			$s .= '	responsive: true,'."\n";
			$s .= '	"bStateSave": true,'."\n";
			$s .= '	"autoWidth" : true,'."\n";
			$s .= '	"processing": true,'."\n";
			$s .= '	"serverSide": true,'."\n";
			$s .= '	"ajax": "apps/'.$this->param['appname'].'/store/store-'.$subapp.'.php",	'."\n";
			$s .= '	"aoColumns": ['."\n";
			$s .= '		{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },'."\n";
			$s .= '		{"bSort":true			,"data":"name"	},'."\n";
			$s .= '		{"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }'."\n";
			$s .= '	],"order": [[ 1, "desc" ]],'."\n";
			
			$s .= '	"createdRow": function ( row, data, index ) {'."\n";
			$s .= '		var selected = false,checked = "",s = \'\';'."\n";
			$s .= '		if ( $.inArray(data.DT_RowId, $("#'.$tablename.'").data( "selected")) !== -1 ) {'."\n";
			$s .= '			$(row).addClass("selected");'."\n";
			$s .= '			selected = true;'."\n";
			$s .= '		}'."\n";
			$s .= '		$("td", row).eq(0).html(fn.ui.checkbox("chk_'.$subapp.'",data[0],selected));'."\n";
			$s .= '		s = \'\';'."\n";

			$s .= '		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.'.$this->param['appname'].'.'.$subapp.'.dialog_edit("+data[0]+")");'."\n";
			$s .= '		$("td", row).eq(2).html(s);'."\n";
			$s .= '	}'."\n";
			$s .= '});'."\n";
			$s .= 'fn.ui.datatable.selectable("#'.$tablename.'","chk_'.$subapp.'");'."\n";
			
			
			fwrite($file, $s);
			fclose($file);
			
			
			$file = fopen($this->path."/control/controller.".$subapp.".add.js", "w") or die("Unable to open file!");
			$s = '';
			$s .= '	fn.app.'.$this->param['appname'].'.'.$subapp.'.dialog_add = function() {'."\n";
			$s .= '		$.ajax({'."\n";
			$s .= '			url: "apps/'.$this->param['appname'].'/view/dialog.'.$subapp.'.add.php",'."\n";
			$s .= '			type: "POST",'."\n";
			$s .= '			dataType: "html",'."\n";
			$s .= '			success: function(html){'."\n";
			$s .= '				$("body").append(html);'."\n";
			$s .= '				fn.ui.modal.setup({dialog_id : "#dialog_add_'.$subapp.'"});'."\n";
			$s .= '			}'."\n";
			$s .= '		});'."\n";
			$s .= '	};'."\n";
			$s .= "\n";
			$s .= '	fn.app.'.$this->param['appname'].'.'.$subapp.'.add = function(){'."\n";
			$s .= '		$.post("apps/'.$this->param['appname'].'/xhr/action-add-'.$subapp.'.php",$("form[name=form_add'.$subapp.']").serialize(),function(response){'."\n";
			$s .= '			if(response.success){'."\n";
			$s .= '				$("#'.$tablename.'").DataTable().draw();'."\n";
			$s .= '				$("#dialog_add_'.$subapp.'").modal("hide");'."\n";
			$s .= '			}else{'."\n";
			$s .= '				fn.notify.warnbox(response.msg,"Oops...");'."\n";
			$s .= '			}'."\n";
			$s .= '		},"json");'."\n";
			$s .= '		return false;'."\n";
			$s .= '	};'."\n";
			
			$s .= '	$(".btn-area").append(fn.ui.button({'."\n";
			$s .= '		class_name : "btn btn-light has-icon",'."\n";
			$s .= '		icon_type : "material",'."\n";
			$s .= '		icon : "add_circle_outline",'."\n";
			$s .= '		onclick : "fn.app.'.$this->param['appname'].'.'.$subapp.'.dialog_add()",'."\n";
			$s .= '		caption : "Add"'."\n";
			$s .= '	}));'."\n";
			fwrite($file, $s);
			fclose($file);
			
			$file = fopen($this->path."/control/controller.".$subapp.".edit.js", "w") or die("Unable to open file!");
			$s = '';
			$s .= '	fn.app.'.$this->param['appname'].'.'.$subapp.'.dialog_edit = function(id) {'."\n";
			$s .= '		$.ajax({'."\n";
			$s .= '			url: "apps/'.$this->param['appname'].'/view/dialog.'.$subapp.'.edit.php",'."\n";
			$s .= '			data: {id:id},'."\n";
			$s .= '			type: "POST",'."\n";
			$s .= '			dataType: "html",'."\n";
			$s .= '			success: function(html){'."\n";
			$s .= '				$("body").append(html);'."\n";
			$s .= '				fn.ui.modal.setup({dialog_id : "#dialog_edit_'.$subapp.'"});'."\n";
			$s .= '			}'."\n";
			$s .= '		});'."\n";
			$s .= '	};'."\n";
			$s .= "\n";
			$s .= '	fn.app.'.$this->param['appname'].'.'.$subapp.'.edit = function(){'."\n";
			$s .= '		$.post("apps/'.$this->param['appname'].'/xhr/action-edit-'.$subapp.'.php",$("form[name=form_edit'.$subapp.']").serialize(),function(response){'."\n";
			$s .= '			if(response.success){'."\n";
			$s .= '				$("#'.$tablename.'").DataTable().draw();'."\n";
			$s .= '				$("#dialog_edit_'.$subapp.'").modal("hide");'."\n";
			$s .= '			}else{'."\n";
			$s .= '				fn.notify.warnbox(response.msg,"Oops...");'."\n";
			$s .= '			}'."\n";
			$s .= '		},"json");'."\n";
			$s .= '		return false;'."\n";
			$s .= '	};'."\n";
			fwrite($file, $s);
			fclose($file);
			
			$file = fopen($this->path."/control/controller.".$subapp.".remove.js", "w") or die("Unable to open file!");
			$s = '';
			$s .= '	fn.app.'.$this->param['appname'].'.'.$subapp.'.dialog_remove = function() {'."\n";
			$s .= '		var item_selected = $("#'.$tablename.'").data("selected");'."\n";
			$s .= '		$.ajax({'."\n";
			$s .= '			url: "apps/'.$this->param['appname'].'/view/dialog.'.$subapp.'.remove.php",'."\n";
			$s .= '			data: {item:item_selected},'."\n";
			$s .= '			type: "POST",'."\n";
			$s .= '			dataType: "html",'."\n";
			$s .= '			success: function(html){'."\n";
			$s .= '				$("body").append(html);'."\n";
			$s .= '				$("#dialog_remove_'.$subapp.'").on("hidden.bs.modal",function(){'."\n";
			$s .= '					$(this).remove();'."\n";
			$s .= '				});'."\n";
			$s .= '				$("#dialog_remove_'.$subapp.'").modal("show");'."\n";
			$s .= '				$("#dialog_remove_'.$subapp.' .btnConfirm").click(function(){'."\n";
			$s .= '					fn.app.'.$this->param['appname'].'.'.$subapp.'.remove();'."\n";
			$s .= '				});'."\n";
			$s .= '			}'."\n";
			$s .= '		});'."\n";
			$s .= '	};'."\n";
			$s .= "\n";
			$s .= '	fn.app.'.$this->param['appname'].'.'.$subapp.'.remove = function(){'."\n";
			$s .= '		var item_selected = $("#tbl'.$bigsubapp.'").data("selected");'."\n";
			$s .= '		$.post("apps/'.$this->param['appname'].'/xhr/action-remove-'.$subapp.'.php",{items:item_selected},function(response){'."\n";
			$s .= '			$("#'.$tablename.'").data("selected",[]);'."\n";
			$s .= '			$("#'.$tablename.'").DataTable().draw();'."\n";
			$s .= '			$("#dialog_remove_'.$subapp.'").modal("hide");'."\n";
			$s .= '		});'."\n";
			$s .= '	};'."\n";
			$s .= '	$(".btn-area").append(fn.ui.button({'."\n";
			$s .= '		class_name : "btn btn-light has-icon",'."\n";
			$s .= '		icon_type : "material",'."\n";
			$s .= '		icon : "delete",'."\n";
			$s .= '		onclick : "fn.app.'.$this->param['appname'].'.'.$subapp.'.dialog_remove()",'."\n";
			$s .= '		caption : "Remove"'."\n";
			$s .= '	}));'."\n";
			fwrite($file, $s);
			fclose($file);
		
		}
	}
	
	
	
	
	function write_store(){
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$dbname = $subapp."s";
			$file = fopen($this->path."/store/store-".$subapp.".php", "w") or die("Unable to open file!");	
			$s = '';
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/datastore.php";'."\n";
			$s .= "\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";
			$s .= '	$dbc = new datastore;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= "\n";
			$s .= '	$columns = array('."\n";
			$s .= '		"id" => "'.$dbname.'.id",'."\n";
			$s .= '		"name" => "'.$dbname.'.name",'."\n";
			$s .= '	);'."\n";
			$s .= "\n";
			$s .= '	$table = array('."\n";
			$s .= '		"index" => "id",'."\n";
			$s .= '		"name" => "'.$dbname.'",'."\n";
			$s .= '	);'."\n";
			$s .= "\n";
			$s .= '	$dbc->SetParam($table,$columns,$_GET[\'order\'],$_GET[\'columns\'],$_GET[\'search\']);'."\n";
			$s .= '	$dbc->SetLimit($_GET[\'length\'],$_GET[\'start\']);'."\n";
			$s .= '	$dbc->Processing();'."\n";
			$s .= '	echo json_encode($dbc->GetResult());'."\n";
			$s .= "\n";
			$s .= '	$dbc->Close();'."\n";
			$s .= "\n";
			$s .= '?>'."\n";
			fwrite($file, $s);
			fclose($file);
		}
	}
	
	
	function write_view(){
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$dbname = $subapp."s";
			$tablename = "tbl".ucwords($this->param['subapp'][$i]);
			$bigsubapp = ucwords($this->param['subapp'][$i]);
			
			$file = fopen($this->path."/view/page.".$subapp.".php", "w") or die("Unable to open file!");	
			$s = '';
			$s .= '<div class="btn-area btn-group mb-2"></div>';
			$s .= '<table id="'.$tablename.'" class="table table-striped table-bordered table-hover table-middle" width="100%">'."\n";
			$s .= '	<thead>'."\n";
			$s .= '		<tr>'."\n";
			$s .= '			<th class="text-center hidden-xs">'."\n";
			$s .= '				<span type="checkall" control="chk_'.$subapp.'" class="far fa-lg fa-square"></span>'."\n";
			$s .= '			</th>'."\n";
			$s .= '			<th class="text-center">Name</th>'."\n";
			$s .= '			<th class="text-center">Action</th>'."\n";
			$s .= '		</tr>'."\n";
			$s .= '	</thead>'."\n";
			$s .= '	<tbody>'."\n";
			$s .= '	</tbody>'."\n";
			$s .= '</table>'."\n";
			fwrite($file, $s);
			fclose($file);
			
			$file = fopen($this->path."/view/dialog.".$subapp.".add.php", "w") or die("Unable to open file!");	
			$s = '';
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	@ini_set(\'display_errors\',DEBUG_MODE?1:0);'."\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/oceanos.php";'."\n";
			$s .= '	include_once "../../../include/iface.php";'."\n";
			$s .= "\n";
			$s .= '	$dbc = new dbc;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= "\n";
			$s .= '	$os = new oceanos($dbc);'."\n";
			$s .= "\n";
			$s .= '	$modal = new imodal($dbc,$os->auth);'."\n";
			$s .= '	$modal->setModel("dialog_add_'.$subapp.'","Add '.$bigsubapp.'");'."\n";
			$s .= '	$modal->initiForm("form_add'.$subapp.'");'."\n";
			$s .= '	$modal->setExtraClass("modal-lg");'."\n";
			$s .= '	$modal->setButton(array('."\n";
			$s .= '		array("close","btn-secondary","Dismiss"),'."\n";
			$s .= '		array("action","btn-primary","Save Change","fn.app.'.$this->param['appname'].'.'.$subapp.'.add()")'."\n";
			$s .= '	));'."\n";
			$s .= "\n";
			$s .= '	$blueprint = array('."\n";
			$s .= '		array('."\n";
			$s .= '			array('."\n";
			$s .= '				"name" => "name",'."\n";
			$s .= '				"caption" => "Name",'."\n";
			$s .= '				"placeholder" => "'.$bigsubapp.' Name"'."\n";
			$s .= '			)'."\n";
			$s .= '		)'."\n";
			$s .= '	);'."\n";
			$s .= "\n";
			$s .= '	$modal->SetBlueprint($blueprint);'."\n";
			$s .= '	$modal->EchoInterface();'."\n";
			$s .= '	$dbc->Close();'."\n";
			$s .= "\n";
			$s .= '?>'."\n";
			fwrite($file, $s);
			fclose($file);
			
			
			
			$file = fopen($this->path."/view/dialog.".$subapp.".edit.php", "w") or die("Unable to open file!");	
			$s = '';
			
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	@ini_set(\'display_errors\',DEBUG_MODE?1:0);'."\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/oceanos.php";'."\n";
			$s .= '	include_once "../../../include/iface.php";'."\n";
			$s .= "\n";	
			$s .= '	$dbc = new dbc;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= "\n";	
			$s .= '	$os = new oceanos($dbc);'."\n";
			$s .= '	$'.$subapp.' = $dbc->GetRecord("'.$dbname.'","*","id=".$_POST[\'id\']);'."\n";
			$s .= "\n";
			$s .= '	$modal = new imodal($dbc,$os->auth);'."\n";
			$s .= "\n";
			$s .= '	$modal->setModel("dialog_edit_'.$subapp.'","Edit '.$bigsubapp.'");'."\n";
			$s .= '	$modal->initiForm("form_edit'.$subapp.'");'."\n";
			$s .= '	$modal->setExtraClass("modal-lg");'."\n";
			$s .= '	$modal->setButton(array('."\n";
			$s .= '		array("close","btn-secondary","Dismiss"),'."\n";
			$s .= '		array("action","btn-outline-dark","Save Change","fn.app.'.$this->param['appname'].'.'.$subapp.'.edit()")'."\n";
			$s .= '	));'."\n";
			$s .= '	$modal->SetVariable(array('."\n";
			$s .= '		array("id",$'.$subapp.'[\'id\'])'."\n";
			$s .= '	));'."\n";
			$s .= "\n";	
			$s .= '	$blueprint = array('."\n";
			$s .= '		array('."\n";
			$s .= '			array('."\n";
			$s .= '				"name" => "name",'."\n";
			$s .= '				"caption" => "Name",'."\n";
			$s .= '				"placeholder" => "'.$bigsubapp.' Name",'."\n";
			$s .= '				"value" => $'.$subapp.'[\'name\']'."\n";
			$s .= '			)'."\n";
			$s .= '		)'."\n";
			$s .= '	);'."\n";
			$s .= "\n";	
			$s .= '	$modal->SetBlueprint($blueprint);'."\n";
			$s .= '	$modal->EchoInterface();'."\n";
			$s .= '	$dbc->Close();'."\n";
			$s .= '?>'."\n";
			
			fwrite($file, $s);
			fclose($file);
			
			
			
			$file = fopen($this->path."/view/dialog.".$subapp.".remove.php", "w") or die("Unable to open file!");	
			$s = '';
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/oceanos.php";'."\n";
			$s .= '	include_once "../../../include/iface.php";'."\n";
			$s .= "\n";		
			$s .= '	@ini_set(\'display_errors\',DEBUG_MODE?1:0);'."\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";		
			$s .= '	$dbc = new dbc;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= "\n";		
			$s .= '	$os = new oceanos($dbc);'."\n";
			$s .= "\n";	
			$s .= '	class myModel extends imodal{'."\n";
			$s .= '		function body(){'."\n";
			$s .= '			$dbc = $this->dbc;'."\n";
			$s .= '			$items = isset($this->param[\'item\'])?$this->param[\'item\']:array();'."\n";
			$s .= '			$removable = true;'."\n";
			$s .= "\n";	
			$s .= '			if(count($items)==0){'."\n";
			$s .= '				$removable = false;'."\n";
			$s .= '			}'."\n";
			$s .= "\n";
			$s .= '			if($removable){'."\n";
			$s .= '				echo \'<ul>\';'."\n";
			$s .= '				foreach($items as $item){'."\n";
			$s .= '					$'.$subapp.' = $dbc->GetRecord("'.$dbname.'","*","id=".$item);'."\n";
			$s .= '					echo "<li>".$'.$subapp.'[\'id\'].\' : \'.$'.$subapp.'[\'name\']."</li>";'."\n";
			$s .= '				}'."\n";
			$s .= '				echo \'</ul>\';'."\n";
			$s .= '			}else{'."\n";
			$s .= '				echo \'Please selecte item to remove!\';'."\n";
			$s .= '			}'."\n";
			$s .= '		}'."\n";
			$s .= '	}'."\n";
			$s .= "\n";	
			$s .= '	$modal = new myModel($dbc,$os->auth);'."\n";
			$s .= '	$modal->setParam($_POST);'."\n";
			$s .= '	$modal->setModel("dialog_remove_'.$subapp.'","Remove '.$bigsubapp.'");'."\n";
			$s .= '	$modal->setButton(array('."\n";
			$s .= '		array("close","btn-secondary","Dismiss"),'."\n";
			$s .= '		array("action","btn-danger","Remove","fn.app.'.$this->param['appname'].'.'.$subapp.'.remove()")'."\n";
			$s .= '	));'."\n";
			$s .= '	$modal->EchoInterface();'."\n";
			$s .= "\n";	
			$s .= '	$dbc->Close();'."\n";
			$s .= '?>'."\n";
			fwrite($file, $s);
			fclose($file);
			
			
		}
	}
	
	
	function write_xhr(){
		for($i=0;$i< count($this->param['subapp']);$i++){
			$subapp = $this->param['subapp'][$i];
			$subcaption = $this->param['subcaption'][$i];
			$dbname = $subapp."s";
			$tablename = "tbl".ucwords($this->param['subapp'][$i]);
			$bigsubapp = ucwords($this->param['subapp'][$i]);
			
			$file = fopen($this->path."/xhr/action-add-".$subapp.".php", "w") or die("Unable to open file!");	
			$s = '';
			
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/oceanos.php";'."\n";
			$s .= "\n";	
			$s .= '	@ini_set(\'display_errors\',DEBUG_MODE?1:0);'."\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";	
			$s .= '	$dbc = new dbc;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= '	$os = new oceanos($dbc);'."\n";
			$s .= "\n";	
			$s .= "\n";	
			$s .= '	if($dbc->HasRecord("'.$dbname.'","name = \'".$_POST[\'name\']."\'")){'."\n";
			$s .= '		echo json_encode(array('."\n";
			$s .= '			\'success\'=>false,'."\n";
			$s .= '			\'msg\'=>\''.$bigsubapp.' Name is already exist.\''."\n";
			$s .= '		));'."\n";
			$s .= '	}else{'."\n";
			$s .= '		$data = array('."\n";
			$s .= '			\'#id\' => "DEFAULT",'."\n";
			$s .= '			\'name\' => $_POST[\'name\'],'."\n";
			$s .= '			\'#created\' => \'NOW()\','."\n";
			$s .= '			\'#updated\' => \'NOW()\''."\n";
			$s .= '		);'."\n";
			$s .= "\n";	
			$s .= '		if($dbc->Insert("'.$dbname.'",$data)){'."\n";
			$s .= '			$'.$subapp.'_id = $dbc->GetID();'."\n";
			$s .= '			echo json_encode(array('."\n";
			$s .= '				\'success\'=>true,'."\n";
			$s .= '				\'msg\'=> $'.$subapp.'_id'."\n";
			$s .= '			));'."\n";
			$s .= "\n";			
			$s .= '			$'.$subapp.' = $dbc->GetRecord("'.$dbname.'","*","id=".$'.$subapp.'_id);'."\n";
			$s .= '			$os->save_log(0,$_SESSION[\'auth\'][\'user_id\'],"'.$subapp.'-add",$'.$subapp.'_id,array("'.$dbname.'" => $'.$subapp.'));'."\n";
			$s .= '		}else{'."\n";
			$s .= '			echo json_encode(array('."\n";
			$s .= '				\'success\'=>false,'."\n";
			$s .= '				\'msg\' => "Insert Error"'."\n";
			$s .= '			));'."\n";
			$s .= '		}'."\n";
			$s .= '	}'."\n";
			$s .= "\n";	
			$s .= '	$dbc->Close();'."\n";
			$s .= '?>'."\n";
			
			
			fwrite($file, $s);
			fclose($file);
			
			
			
			$file = fopen($this->path."/xhr/action-edit-".$subapp.".php", "w") or die("Unable to open file!");	
			$s = '';
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/oceanos.php";'."\n";
			$s .= "\n";	
			$s .= '	@ini_set(\'display_errors\',DEBUG_MODE?1:0);'."\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";	
			$s .= '	$dbc = new dbc;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= '	$os = new oceanos($dbc);'."\n";
			$s .= "\n";	
			
			$s .= '	if($dbc->HasRecord("'.$dbname.'","name = \'".$_POST[\'name\']."\'")){'."\n";
			$s .= '		echo json_encode(array('."\n";
			$s .= '			\'success\'=>false,'."\n";
			$s .= '			\'msg\'=>\''.$bigsubapp.' Name is already exist.\''."\n";
			$s .= '		));'."\n";
			$s .= '	}else{'."\n";
			$s .= '		$data = array('."\n";
			$s .= '			\'name\' => $_POST[\'name\'],'."\n";
			$s .= '			\'#updated\' => \'NOW()\','."\n";
			$s .= '		);'."\n";
			$s .= "\n";		
			$s .= '		if($dbc->Update("'.$dbname.'",$data,"id=".$_POST[\'id\'])){'."\n";
			$s .= '			echo json_encode(array('."\n";
			$s .= '				\'success\'=>true'."\n";
			$s .= '			));'."\n";
			$s .= '			$'.$subapp.' = $dbc->GetRecord("'.$dbname.'","*","id=".$_POST[\'id\']);'."\n";
			$s .= '			$os->save_log(0,$_SESSION[\'auth\'][\'user_id\'],"'.$subapp.'-edit",$_POST[\'id\'],array("'.$dbname.'" => $'.$subapp.'));'."\n";
			$s .= '		}else{'."\n";
			$s .= '			echo json_encode(array('."\n";
			$s .= '				\'success\'=>false,'."\n";
			$s .= '				\'msg\' => "No Change"'."\n";
			$s .= '			));'."\n";
			$s .= '		}'."\n";
			$s .= '	}'."\n";
			$s .= "\n";	
			$s .= '	$dbc->Close();'."\n";
			$s .= '?>'."\n";
			fwrite($file, $s);
			fclose($file);
			
			$file = fopen($this->path."/xhr/action-remove-".$subapp.".php", "w") or die("Unable to open file!");	
			$s = '';
			$s .= '<?php'."\n";
			$s .= '	session_start();'."\n";
			$s .= '	include_once "../../../config/define.php";'."\n";
			$s .= '	include_once "../../../include/db.php";'."\n";
			$s .= '	include_once "../../../include/oceanos.php";'."\n";
			$s .= "\n";	
			$s .= '	@ini_set(\'display_errors\',DEBUG_MODE?1:0);'."\n";
			$s .= '	date_default_timezone_set(DEFAULT_TIMEZONE);'."\n";
			$s .= "\n";	
			$s .= '	$dbc = new dbc;'."\n";
			$s .= '	$dbc->Connect();'."\n";
			$s .= '	$os = new oceanos($dbc);'."\n";
			$s .= "\n";	
			$s .= '	foreach($_POST[\'items\'] as $item){'."\n";
			$s .= '		$'.$subapp.' = $dbc->GetRecord("'.$dbname.'","*","id=".$item);'."\n";
			$s .= '		$dbc->Delete("'.$dbname.'","id=".$item);'."\n";
			$s .= '		$os->save_log(0,$_SESSION[\'auth\'][\'user_id\'],"'.$subapp.'-delete",$id,array("'.$dbname.'" => $'.$subapp.'));'."\n";
			$s .= '	}'."\n";
			$s .= "\n";	
			$s .= '	$dbc->Close();'."\n";
			$s .= '?>'."\n";
			fwrite($file, $s);
			fclose($file);
			
			
			
			
			
		}
	}
	
	
}


?>