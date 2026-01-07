fn.app.setting.system = {
	load_menu : function(){
	},
	
	append_category : function(){
		var s = '';
		s += '<li class="list-group-item dd-category"';
		s += ' data-type="category"';
		s += ' data-app="category_'+Math.floor((Math.random() * 10000) + 1)+'"';
		s += ' data-icon="fa-list"';
		s += ' data-name_th="หมวดหมู่ใหม่"';
		s += ' data-name_en="New Category"';
		s += '>';
			s += '<div class="dd-handle">';
				s += '<div class="input-group">';
					s += '<input type="text" class="form-control" value="New Category" readonly>';
					s += '<div class="input-group-append">';
						s += '<span class="input-group-text"><i class="fa fa-list"></i></span>';
						s += '<button class="btn btn-outline-dark" type="button" onclick="fn.app.setting.system.setting(this)">Edit</button>';
					s += '</div>';
				s += '</div>';
			s += '</div>';
			s += '<ul class="dd-list">';
			s += '</ul>';
		s += '</li>';
		$("#nestable > ol").append(s);
		
		$( ".dd-list" ).sortable({
		  connectWith: ".dd-list"
		});
		
	},
	setting : function(btn){
		var li = $(btn).parent().parent().parent().parent();
		var lang = $("#form_general").attr("lang");
		$("#form_setting input[name=appname]").val(li.attr("data-app"));
		$("#form_setting input[name=icon]").val(li.attr("data-icon"));
		$("#form_setting input[name=name_en]").val(li.attr("data-name_en"));
		$("#form_setting input[name=name_th]").val(li.attr("data-name_th"));
		$("#dialog_setting").modal('show');
		$("#form_setting .btnSave").unbind("click").click(function(){
			
			li.attr("data-icon",$("#form_setting input[name=icon]").val());
			li.attr("data-name_en",$("#form_setting input[name=name_en]").val());
			li.attr("data-name_th",$("#form_setting input[name=name_th]").val());
			li.find(".input-group-addon").html('<i class="fa '+$("#form_setting input[name=icon]").val()+'"></i>');
			if(lang=="th"){
				li.find("input[type=text]").val($("#form_setting input[name=name_th]").val())
			}else{
				li.find("input[type=text]").val($("#form_setting input[name=name_en]").val())
			}
			
			$("#dialog_setting").modal('hide');
		});
		$("#form_setting .btnDelete").unbind("click").click(function(){
			fn.confirmbox("Are you sure to remove this items?","Are you sure to confirm this action?",function(){
				$("#dialog_setting").modal('hide');
				li.remove();
			});
			return false;
			
		});
	},
	save_general : function(){
		fn.confirmbox("Please confirm to save?","This action may affect the your structure! Are you sure to confirm this action?",function(){
			$.post('apps/setting/setting/xhr/action-save-system-menu.php',$('#form_general').serialize(),function(response){
				if(response.success){	
					fn.successbox('Setting','Save complete',function(){
						fn.navigate("setting","view=system");
					});
				}else{
					fn.alertbox("Alert",response.msg);
				}
			},'json');
		});
		return false;
	},
	dialog_add_app : function(btn){
		$("#form_add_menu")[0].reset();
		$("#dialog_add_menu").modal('show');
		var lang = $("#form_general").attr("lang");
		$("#form_add_menu [name=cbbApplication]").unbind("change").change(function(){
			var app = $(this).val();
			console.log(app);
				var option = $(this).find("[value="+app+"]");
				$("#form_add_menu [name=name_en]").val(option.attr("data-name"));
				$("#form_add_menu [name=icon]").val(option.attr("data-icon"));
				$("#form_add_menu [name=path]").val(option.attr("data-path"));
			}).change();
			
			$("#form_add_menu .btnSave").unbind("click").click(function(){
				var appname = $("#form_add_menu [name=cbbApplication]").val();
			var name_en = $("#form_add_menu [name=name_en]").val();
			var name_th = $("#form_add_menu [name=name_th]").val();
			var icon = $("#form_add_menu [name=icon]").val();
			var path = $("#form_add_menu [name=path]").val();
			
			var s = '';
			s += '<li class="dd-item"';
			s += ' data-type="app"';
			s += ' data-app="'+appname+'"';
			s += ' data-icon="'+icon+'"';
			s += ' data-name_th="'+name_th+'"';
			s += ' data-name_en="'+name_en+'"';
			s += ' data-path="'+path+'"';
			s += '>';
				s += '<div class="dd-handle">';
					s += '<div class="input-group">';
						s += '<span class="input-group-addon"><i class="fa '+icon+'"></i></span>';
						if(lang=="th"){
							s += '<input type="text" class="form-control" value="'+name_th+'" readonly>';
						}else{
							s += '<input type="text" class="form-control" value="'+name_en+'" readonly>';
						}
						s += '<span class="input-group-btn">';
							s += '<button class="btn btn-default" type="button" onclick="fn.app.setting.system.setting(this)">Edit</button>';
						s += '</span>';
					s += '</div>';
				s += '</div>';
			s += '</li>';
			$("#nestable > ol.dd-list").append(s);
			$("#dialog_add_menu").modal('hide');
				
		});
	},save : function(){
		var data = [];
		
		$("ol.dd-list > li").each(function(){
			var li = $(this);
			if(li.attr("data-type")=="category"){
				var submenu = [];
				li.find("ul.dd-list > li").each(function(){
					submenu.push({
						"appname" : $(this).attr("data-app"),
						"name" : {
							"en" : $(this).attr("data-name_en"),
							"th" : $(this).attr("data-name_th")
						},
						"icon" : $(this).attr("data-icon"),
						"path" : $(this).attr("data-path")
					});
				});
				var item = {
					"appname" : li.attr("data-app"),
					"name" : {
						"en" : $(this).attr("data-name_en"),
						"th" : $(this).attr("data-name_th")
					},
					"icon" : li.attr("data-icon"),
					"submenu" : submenu
				};
			}else{
				var item = {
					"appname" : li.attr("data-app"),
					"name" : {
						"en" : $(this).attr("data-name_en"),
						"th" : $(this).attr("data-name_th")
					},
					"icon" : li.attr("data-icon"),
					"path" : li.attr("data-path")
				};
			}
			data.push(item);
			
		});
		
		fn.confirmbox("Please confirm to save?","This action may affect the your structure! Are you sure to confirm this action?",function(){
			$.post('apps/setting/setting/xhr/action-save-system-menu.php',{data:JSON.stringify(data),available:$("select[name=available]").val()},function(response){
				if(response.success){	
					fn.successbox('Setting','Save complete',function(){
						window.location.reload();
						//fn.navigate("setting","view=system&section=menu");
					});
				}else{
					fn.alertbox("Alert",response.msg);
				}
			},'json');
		});
		return false;
	},
	clearAll : function(){
		fn.confirmbox("Are you sure to clear all?","This action may affect the your structure! Are you sure to confirm this action?",function(){
			$("ol.dd-list").html("");
			$("select[name=available]").val("no");
		});
		return false;
	},
	remove : function(){
		fn.confirmbox("Are you sure to remove this items?","Are you sure to confirm this action?",function(){
			
		});
		return false;
	}
}

$(function(){
	$( ".dd-category" ).sortable({
      connectWith: ".dd-category"
    });
});


