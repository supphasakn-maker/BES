<?php
	session_start();
	include_once "../../../config/define.php";
	@ini_set('display_errors',DEBUG_MODE?1:0);
	date_default_timezone_set(DEFAULT_TIMEZONE);

	include_once "../../../include/db.php";
	include_once "../../../include/oceanos.php";
	include_once "../../../include/iface.php";

	$dbc = new dbc;
	$dbc->Connect();

	$os = new oceanos($dbc);
	$import = $dbc->GetRecord("bs_imports","*","id=".$_POST['id']);
	
	
	
	class imodal_view extends imodal{
		function body(){
			echo '<form name="uploader" class="d-none" enctype="multipart/form-data"><input type="file" name="file[]" multiple></form>';
		}
	}

	$modal = new imodal_view($dbc,$os->auth);

	$modal->setModel("dialog_edit_import","Edit Import");
	$modal->initiForm("form_editimport");
	$modal->setExtraClass("modal-lg");
	$modal->setButton(array(
		array("close","btn-secondary","Dismiss"),
		array("action","btn-outline-dark","Save Change","fn.app.import.import.edit()")
	));
	$modal->SetVariable(array(
		array("id",$import['id'])
	));
	
	
	$json = json_decode($import['info_coa_files'],true);
	$fize_zone = "";
	$file_zone .= '<div id="file_zone">';
		$file_zone .= '<div><button class="btn btn-primary btn-sm" onclick="$(\'form[name=uploader] input[type=file]\').click()">Add</button></div>';
		$file_zone .= '<ul class="list-group">';
			foreach($json as $item){
				$file_zone .= '<li class="list-group-item"><button class="btn btn-danger" onclick="$(this).parent().remove()">X</button> <input type="hidden" name="path[]" value="'.$item.'"><span>'.$item.'</span></li>';
			}		
		$file_zone .= '</ul>';
	$file_zone .= '</div>';

	$blueprint = array(
		array(
			array(
				"type" => "date",
				"name" => "delivery_date",
				"caption" => "Delviery Date",
				"placeholder" => "Delviery Date",
				"value" => $import['delivery_date']
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "delivery_by",
				"source" => array(
					"Brink",
					"G4S",
					"รับเอง"
				),
				"caption" => "Delviery By",
				"value" => $import['delivery_by']
			)
		),array(
			array(
				"type" => "combobox",
				"name" => "type",
				"caption" => "Type",
				"source" => array(
					"แท่ง",
					"เม็ด"
				),
				"value" => $import['type']
			)
		),array(
			array(
				"type" => "textarea",
				"name" => "comment",
				"caption" => "Remark",
				"placeholder" => "Remark",
				"value" => $import['comment']
			)
		),array(
			array(
				"type" => "textbox",
				"name" => "info_coa",
				"caption" => "COA",
				"placeholder" => "Certificate Number",
				"value" => $import['info_coa']
			)
		),array(
			array(
				"type" => "custom",
				"html" => $file_zone,
			)
		)
	);

	$modal->SetBlueprint($blueprint);
	$modal->EchoInterface();
	$dbc->Close();
?>
