<?php
/*
 * 2020 : Form Custom Type in Default : Todsaporn S.
 * 
 */
 
	class iface{
		protected $dbc = null;
		protected $auth = null;
		protected $param = null;
		
		function setDBC($dbc){
			$this->dbc = $dbc;
		}
		
		function setAuth($auth){
			$this->auth = $auth;
		}
		
		function setParam($param){
			$this->param = $param;
		}
		
		function Initial($dbc,$auth){
			$this->dbc = $dbc;
			$this->auth = $auth;
		}

		function __construct($dbc,$auth){
			$this->dbc = $dbc;
			$this->auth = $auth;
		}
	}
	
	class ipanel extends iface{
		private $app = "noname";
		private $appname = "Noname";
		private $view = "";
		private $section = "";
		private $header_meta=array();
		
		function setMeta($header_meta){
			$this->header_meta = $header_meta;
		}
		
		function setApp($name,$caption){
			$this->app = $name;
			$this->appname = $caption;
		}
		
		function setView($view){
			$this->view = $view;
		}
		
		function getView(){
			return $this->view;
		}
		
		function setSection($section){
			$this->section = $section;
		}
		
		function getSection(){
			return $this->section;
		}
		
		function body(){
			foreach($this->header_meta as $header){
				if($header[0] == $this->view){
					include_once "view/page.".$header[0].".php";
				}
			}			
		}
		
		function EchoInterface(){
			echo '<div class="card">';
				echo '<div class="card-body">';
					echo '<ul class="nav nav-tabs" role="tablist">';
					foreach($this->header_meta as $header){
						$active = $this->view == $header[0] ? " active" : "";
						$href = '#apps/'.$this->app.'/index.php?view='.$header[0];
						echo '<li class="nav-item">';
							echo '<a class="nav-link'.$active.'" href="'.$href.'" ><i class="'.$header[2].'"></i> '.$header[1].'</a>';
						echo '</li>';							
					}
					echo '</ul>';
					echo '<div class="tab-content p-3">';
						echo '<div class="tab-pane fade show active" id="'.$this->app.'_'.$this->view.'" role="tabpanel">';
						$this->body();
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
		
		function EchoInterface_verticle(){
			echo '<div class="row gutters-sm">';
				echo '<div class="col-md-2 d-none d-md-block">';
					echo '<div class="card">';
						echo '<div class="card-body">';
							echo '<nav class="nav flex-column nav-pills nav-gap-y-1">';
							foreach($this->header_meta as $header){
								$active = $this->view == $header[0] ? " active" : "";
								$href = '#apps/'.$this->app.'/index.php?view='.$header[0];
								echo '<a href="'.$href.'" class="nav-item nav-link has-icon nav-link-faded'.$active.'">';
									echo '<i class="'.$header[2].'"></i> '.$header[1];
								echo '</a>';
							}
							echo '</nav>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '<div class="col-md-10">';
				echo '<div class="card">';
					echo '<div class="card-header border-bottom mb-3 d-flex d-md-none">';
						echo '<ul class="nav nav-tabs card-header-tabs nav-gap-x-1" role="tablist">';
							foreach($this->header_meta as $header){
								$active = $this->view == $header[0] ? " active" : "";
								$href = '#apps/'.$this->app.'/index.php?view='.$header[0];
								echo '<li class="nav-item">';
									echo '<a href="'.$href.'" class="nav-link has-icon'.$active.'">';
										echo '<i class="'.$header[2].'"></i> '.$header[1];
									echo '</a>';
								echo '</li>';
							}
						echo '</ul>';
					echo '</div>';
					echo '<div class="card-body tab-content">';
						echo '<div class="tab-pane active" id="'.$this->app.'_'.$this->view.'">';
							$this->body();
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		}
		
		function PageBreadcrumb(){
			echo '<ol class="breadcrumb page-breadcrumb">';
				echo '<li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>';
				echo '<li class="breadcrumb-item">'.$this->appname.'</li>';
				foreach($this->header_meta as $header){
					if($header[0]==$this->view){
						echo '<li class="breadcrumb-item active">'.$header[1].'</li>';
					}
				}
			echo '</ol>';
		}
	}
	
	class iform extends iface{
		private $options = array(
			"method" => "post",
			"enctype" => "",
			"class" => ""
		);
		
		protected $form_name = "";
		protected $form_action = "";
		protected $form_extra_class = "";
		protected $variable = array();
		private $blueprint = array();
		
		function setOption($option,$value){
			$this->options[$option] = $value;
		}
		
		function setFrom($form_name,$action=""){
			$this->form_name = $form_name;
			$this->form_action = $action;
		}
		
		function SetBlueprint($blueprint){
			$this->blueprint = $blueprint;
		}
		
		function SetVariable($variable){
			$this->variable = $variable;
		}
		
		function setExtraClass($class){
			$this->form_extra_class = $class;
		}
		
		function EchoInterface(){
			$frm = "";
			$frm .= '<form name="'.$this->form_name.'"';
			$frm .= ' class="form-horizontal'.((isset($this->options['class']) && $this->options['class'] != "")?(" ". $this->options['class']):"").'"';
			$frm .= 'role="form" ';
			$frm .= 'onsubmit="'.$this->form_action.';return false;"';
			if(isset($this->options['enctype']) && $this->options['enctype'] != "")$frm .= 'enctype="'.$this->options['enctype'].'"';
			$frm .= '>';
			echo $frm;
			foreach($this->variable as $variable){
				echo '<input type="hidden" name="'.$variable[0].'" value="'.$variable[1].'">';
			}
			
			$this->LoopBluePrint($this->blueprint);
		
			$this->CustomHTML();
			echo '</form>';
		}
		
		function LoopBluePrint($blueprint){
			foreach($blueprint as $form_group){
				if(is_array($form_group)){
					if(isset($form_group['group'])){
						if(isset($form_group['type'])){
							switch($form_group['type']){
								case "list":
									$tag = isset($form_group['tag'])?$form_group['tag']:"ul";
									echo '<'.$tag.' id="'.$form_group['group'].'">';
									$this->LoopBluePrint($form_group['items']);
									echo '</'.$tag.'>';
									break;
								case "tablist":
									echo '<ul id="'.$form_group['group'].'" class="nav nav-tabs" role="tablist">';
									$i = 0;
									foreach($form_group['items'] as $tab){
										echo '<li class="nav-item'.($i==0?" active":"").'">';
											echo '<a class="nav-link'.($i==0?" active":"").'" id="'.$tab['group'].'-tab" data-toggle="tab" href="#'.$tab['group'].'" role="tab" aria-controls="home" aria-selected="true">'.$tab['name'].'</a>';
										echo '</li>';
										$i++;
									}
										
									echo '</ul>';
									echo '<div class="tab-content" id="'.$form_group['group'].'-content">';
									$i = 0;
									foreach($form_group['items'] as $tab){
										echo '<div class="tab-pane fade'.($i==0?" show active":"").'" id="'.$tab['group'].'" role="tabpanel" aria-labelledby="home-tab">';
										echo '<br>';
										$this->LoopBluePrint($tab['items']);
										echo '</div>';
										$i++;
									}
									echo '</div>';
									break;
								case "tab":
									echo '<div id="'.$form_group['group'].'">';
									$this->LoopBluePrint($form_group['items']);
									echo '</div>';
									break;
								case "inline":
									echo '<div class="form-group'.(isset($form_group['class'])?" ".$form_group['class']:"").'">';
									foreach($form_group['items'] as $control){
										$this->EchoItem($control);
									}
									echo '</div>';
									break;
							}
						}else{
							echo '<div id="'.$form_group['group'].'">';
							$this->LoopBluePrint($form_group['items']);
							echo '</div>';
						}
						
					}else{
						echo '<div class="form-group row">';
						foreach($form_group as $control){
							if(isset($control['caption'])){
								$flex = isset($control['flex-label'])?$control['flex-label']:2;
								echo '<label class="col-sm-'.$flex.' col-form-label text-right">'.$control['caption'].'</label>';
								$col_class = "col-sm-10";
							}else{
								$col_class = "col-sm-12";
							}
							
							if(isset($control['flex'])){
								$col_class = "col-sm-".$control['flex'];
							}
							
							echo '<div class="'.$col_class.'">';
							$this->EchoItem($control);
							echo '</div>';
						}
						echo '</div>';
					}
				}else{
					switch($form_group){
						case "hr";
							echo "<hr>";
							break;
					}
				}
			}
		}
		
		
		function EchoItem($control){
			$type = isset($control['type'])?$control['type']:"textbox";
			$placeholder = isset($control['placeholder'])?$control['placeholder']:"";
			$class = isset($control['class'])?" ".$control['class']:"";
			switch($type){
				case "custom":
					echo $control['html'];
					break;
				case "combobox":
					echo '<select name="'.$control['name'].'" class="form-control'.$class.'">';
					foreach($control['source'] as $item){
						if(is_array($item)){
							
							if(isset($control['config'])){
								$value = $item[$control['config']['value']];
								$caption = $item[$control['config']['caption']];
							}else{
								$value = $item[0];
								$caption = $item[1];
							}
						}else{
							$value = $item;
							$caption = $item;
						}
						
						$selected = "";
						if(isset($control['value'])){
							$selected = $value==$control['value']?" selected":"";
						}
						echo '<option value="'.$value.'"'.$selected.'>'.$caption.'</option>';
					}
					echo '</select>';
					break;
				case "comboboxdb":
					$readonly = "";if(isset($control['readonly'])){$readonly = ' readonly="'.$control['readonly'].'"';}
					$multiple = "";if(isset($control['multiple'])){$multiple = ' multiple="'.$control['multiple'].'"';}
					$size = "";if(isset($control['size'])){$size = ' size="'.$control['size'].'"';}
					echo '<select name="'.$control['name'].'" class="form-control'.$class.'"'.$readonly.$multiple.$size.'>';
					if(isset($control['default'])){
						if(is_array($control['default'])){
							echo '<option value="'.$control['default']['value'].'">'.$control['default']['name'].'</option>';
						}else{
							echo '<option>'.$control['default'].'</option>';
						}
					}
					$sql = "SELECT ".$control['source']['value']." AS id,".$control['source']['name']." AS name FROM ".$control['source']['table'];
					if(isset($control['source']['where']))$sql .= " WHERE ".$control['source']['where'];
					$rst = $this->dbc->Query($sql);
					while($line = $this->dbc->Fetch($rst)){
						$selected = "";
						if(isset($control['value'])){
							if(isset($control['multiple'])){
								$stack = explode(",",$control['value']);
								$selected = in_array($line['id'],$stack)?" selected":"";
								
							}else{
								$selected = $control['value']==$line['id']?" selected":"";
							}
							
							
						}
						echo '<option value="'.$line['id'].'"'.$selected.'>'.$line['name'].'</option>';
					}
					echo '</select>';
					break;
				case "comboboxdatabank":
					$readonly = "";if(isset($control['readonly'])){$readonly = ' readonly="'.$control['readonly'].'"';}
					echo '<select name="'.$control['name'].'" class="form-control'.$class.'"'.$readonly.'>';
					if(isset($control['default'])){
						if(is_array($control['default'])){
							echo '<option value="'.$control['default']['value'].'">'.$control['default']['name'].'</option>';
						}else{
							echo '<option>'.$control['default'].'</option>';
						}
					}
					$record = $this->dbc->GetRecord("os_variable","value","name='".$control['source']."'");
					$manyline = json_decode($record['value'],true);
					//var_dump($manyline);
					foreach($manyline as $line){
						$selected = "";
						if(isset($control['value'])){
							$selected = $control['value']==$line?" selected":"";
						}
						echo '<option '.$selected.'>'.$line.'</option>';
					}
					echo '</select>';
					break;
				case "password":
					$value = "";
					if(isset($control['value'])){
						$value = ' value="'.$control['value'].'"';
					}
					echo '<input type="password" class="form-control'.$class.'" name="'.$control['name'].'" placeholder="'.$placeholder.'"'.$value.'>';
					break;
				case "file":
					$value = "";
					if(isset($control['value'])){
						$value = ' value="'.$control['value'].'"';
					}
					echo '<input type="file" class="form-control'.$class.'" name="'.$control['name'].'" placeholder="'.$placeholder.'"'.$value.'>';
					break;
				case "textarea":
					$value = "";$rows = "";
					if(isset($control['value'])){$value = $control['value'];}
					if(isset($control['rows'])){
						$rows = ' rows="'.$control['rows'].'"';
					}
					echo '<textarea class="form-control'.$class.'" name="'.$control['name'].'" placeholder="'.$placeholder.'"'.$rows.'>'.$value.'</textarea>';
					break;
				case "button":
					$value = "";
					$onclick = isset($control['onclick'])?$control['onclick']:"";
					$class = isset($control['class'])?$control['class']:"";
					echo '<button type="button" onclick="'.$onclick.'" class="'.$class.'">'.$control['name'].'</button>';
					break;
				case "checkbox-multiple":
					foreach($control['source'] as $chval => $chcaption){
						$checkid = $control['name'].$chval;
						$checked = "";
						if(isset($control['value'])){
							$checked = in_array($chval,explode(",",$control['value']))?" checked" : "";
						}
						echo '<div class="custom-control custom-checkbox custom-control-inline" style="padding-top: 8px;">';
							echo '<input name="'.$control['name'].'" type="checkbox" value="'.$chval.'" class="custom-control-input" id="'.$checkid.'"'.$checked.'>';
							echo '<label class="custom-control-label" for="'.$checkid.'">'.$chcaption.'</label>';
						echo '</div>';
					}
					break;
				case "checkbox":
					$checked = "";
					if(isset($control['value'])){
						if($control['value']=="yes")$checked = " checked";
						if($control['value']=="checked")$checked = " checked";
						if($control['value']==true)$checked = " checked";
						if($control['value']=="true")$checked = " checked";
					}
					$class = isset($control['class'])?" ".$control['class']:"";
					echo '<div class="frame-wrap">';
						echo '<div class="custom-control custom-checkbox pt-2">';
							echo '<input id="'.$control['name'].'" class="custom-control-input'.$class.'" type="checkbox" name="'.$control['name'].'" value="yes"'.$checked.'>';
							if(isset($control['text']))echo '<label class="custom-control-label" for="'.$control['name'].'">'.$control['text'].'</label>';
						echo '</div>';
					echo '</div>';
					/*
					<div class="custom-control custom-checkbox">
                  <input type="checkbox" class="custom-control-input" id="customCheck4">
                  <label class="custom-control-label" for="customCheck4"></label>
                </div>
					*/
					
					break;
				case "switchbox":
					$checked = "";
					if(isset($control['value'])){
						
						if($control['value']==="yes")$checked = " checked";
						if($control['value']==="checked")$checked = " checked";
						if($control['value']===true)$checked = " checked";
						if($control['value']==="true")$checked = " checked";
					}
					
					echo '<div class="frame-wrap">';
					echo '<div class="custom-control custom-switch">';
						echo '<input id="'.$control['name'].'" class="custom-control-input'.$class.'" type="checkbox" name="'.$control['name'].'" value="yes"'.$checked.'>';
						if(isset($control['text']))echo '<label for="'.$control['name'].'" class="custom-control-label">'.$control['text'].'</label>';
					echo '</div>';
					echo '</div>';
					break;
				case "input-group":
					echo '<div class="input-group">';
					if(isset($control['items'])){
						foreach($control['items'] as $item){
							if(isset($item['type'])){
								switch($item['type']){
									case "icon":
										echo '<div class="input-group-addon">';
											echo '<span class="'.$item['icon'].'"></span>';
										echo '</div>';
										break;
									case "label":
										echo '<div class="input-group-addon">';
											echo $item['caption'];
										echo '</div>';
										break;
									case "btn":
										$onclick = isset($item['onclick'])?$item['onclick']:"";
										echo '<div class="input-group-append">';
											echo '<button type="button" onclick="'.$onclick.'" class="'.(isset($item['class'])?$item['class']:"").'">';
												echo '<span class="'.$item['icon'].'"></span>';
											echo '</button>';
										echo '</div>';
										break;
									default:
										$this->EchoItem($item);break;
								}
							}else{
								$this->EchoItem($item);
							}
						}
					}
					echo '</div>';
					break;
				default:
					$type = isset($control['type'])?$control['type']:"";
					$value = "";if(isset($control['value'])){$value = ' value="'.$control['value'].'"';}
					$readonly = "";
					if(isset($control['readonly'])){
						if($control['readonly']!="false")
						$readonly = ' readonly="'.$control['readonly'].'"';
						
					}
					
					if(isset($control['name'])){
						echo '<input type="'.$type.'" class="form-control'.$class.'" name="'.$control['name'].'" placeholder="'.$placeholder.'"'.$value.''.$readonly.'>';
					}
					break;
			}
			
			if(isset($control['help'])){
				echo '<span class="help-block">'.$control['help'].'</span>';
			}
			
			
		}
		
		function CustomHTML(){
			
		}
	}
	
	class imodal extends iface{
		protected $dialog_id;
		protected $dialog_title;
		protected $dialog_extra_class = "";
		protected $iform=null;
		
		function initiForm($form_name,$form_action=null){
			$this->iform = new iform($this->dbc,$this->auth);
			$this->iform->setFrom($form_name,$form_action);
		}

		function initilForm($form_name,$form_action=null){
			$this->iform = new iform($this->dbc,$this->auth);
			$this->iform->setFrom($form_name,$form_action);
		}
		
		function SetBlueprint($blueprint){
			$this->iform->SetBlueprint($blueprint);
		}
		
		protected $button = array(
			array("close","btn-secondary","Dismiss")
		);

		function setModel($id,$title){
			$this->dialog_id = $id;
			$this->dialog_title = $title;
		}
		
		function setExtraClass($class){
			$this->dialog_extra_class = $class;
		}
		
		function setButton($button){
			$this->button = $button;
		}
		
		function SetVariable($variable){
			$this->iform->SetVariable($variable);
		}
		
		function body(){
			
		}
		
		function form(){
			
		}
		
		function EchoInterface(){
			echo '<div class="modal fade" id="'.$this->dialog_id.'" tabindex="-1" role="dialog" aria-hidden="true">';
				echo '<div class="modal-dialog'.($this->dialog_extra_class!=""?(" ".$this->dialog_extra_class):"").'" role="document">';
					echo '<div class="modal-content">';
						echo '<div class="modal-header">';
							echo '<h4 class="modal-title">';
								echo $this->dialog_title;
							echo '</h4>';
							echo '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
								echo '<span aria-hidden="true"><i class="fal fa-times"></i></span>';
							echo '</button>';
						echo '</div>';
						echo '<div class="modal-body">';
							$this->body();
							if(!is_null($this->iform)){
								$this->iform->EchoInterface();
							}
						echo '</div>';
						echo '<div class="modal-footer">';
						foreach($this->button as $btn){
							switch($btn[0]){
								case "close":
									echo '<button type="button" class="btn '.$btn[1].'" data-dismiss="modal">'.$btn[2].'</button>';
									break;
								case "action":
									echo '<button type="button" class="btn '.$btn[1].'" onclick="'.$btn[3].'">'.$btn[2].'</button>';
									break;
							}
						}
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		}
	}
?>