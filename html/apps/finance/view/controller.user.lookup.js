
	fn.app.accctrl.user.dialog_lookup = function(func,multiple,selected_member) {
		if(typeof multiple == "undefined")multiple = false;
		$.ajax({
			url: "apps/accctrl/view/dialog.user.lookup.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_user_lookup").on("hidden.bs.modal",function(){$(this).remove();});
				$("#dialog_user_lookup").modal('show');
				if(multiple){
					$("#tblUserLookup").data( "selected",[]);
				}else{
					$("#tblUserLookup").data( "selected",null);
				}
				
				$('#tblUserLookup').DataTable({
					"bStateSave": true,
					"sDom": fn.config.datatable.sDom,
					"oLanguage": fn.config.datatable.oLanguage,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": "apps/accctrl/store/store-user.php",
					"aoColumns": [
						{"bSortable": false	,"data":"id"		,"sWidth": "20px", "sClass" : "hidden-xs text-center"},
						{"bSortable": false	,"data":"avatar"	,"sWidth": "40px", "sClass" : "text-center"},
						{"bSortable": true	,"data":"fullname"	,"sClass" : ""},
						{"bSortable": false	,"data":"email"		,"sClass" : "hidden-xs text-center"},
						{"bSortable": false	,"data":"mobile"	,"sClass" : "hidden-xs text-center"},
						{"bSortable" : true	,"data":"groupname"	,"sClass" : "text-center"}
					],"order": [[ 2, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, selected_member) !== -1 ) {
							$(row).addClass('hidden');
						}else{
							
							if ( $.inArray(data.DT_RowId, $("#tblUserLookup").data("selected")) !== -1 ) {
								$(row).addClass('selected');
								selected = true;
							}
							$('td', row).eq(0).html(fn.ui.checkbox("chk_user",data[0],selected,multiple));
							
							s = '';
							var avatar = data.avatar==null?"img/default/user.png":data.avatar;
							s += '<img src="'+avatar+'" alt="" class="img-round" height="36">';
							$('td', row).eq(1).html(s);
						}
					}
				});
				fn.ui.datatable.selectable('#tblUserLookup','chk_user',multiple);
				$("#dialog_user_lookup .btnSelect").unbind().click(function(){
					if(multiple){
						if($("#tblUserLookup").data("selected").length==0){
							fn.engine.alert("No selected","Please select user!");
						}else{
							$.post("apps/accctrl/xhr/action-load-user.php",{id:$("#tblUserLookup").data("selected")},function(json){
								func(json);
								$("#dialog_user_lookup").modal('hide');
							},'json');
						}
					}else{
						if($("#tblUserLookup").data("selected")==null){
							fn.engine.alert("No selected","Please select user!");
						}else{
							$.post("apps/accctrl/xhr/action-load-user.php",{id:$("#tblUserLookup").data("selected")},function(json){
								func(json);
								$("#dialog_user_lookup").modal('hide');
							},'json');
						}
					}
					
				});
			}	
		});
		
	};

	
