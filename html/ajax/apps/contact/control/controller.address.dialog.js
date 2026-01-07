	fn.app.contact.address.dialog = function(type,id) {
		$.ajax({
			url: "apps/contact/view/dialog.address.php",
			type: "POST",
			data : {type:type,id:id},
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_address"});
				$("#tblAddress").data( "selected", [] );
				$('#tblAddress').DataTable({
					"bStateSave": true,responsive: true,
					dom: fn.ui.datatable.dom.default,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": {
						"url": "apps/contact/store/store-address.php",
						"data": function ( d ) {
							d.id = id;
							d.type = type;
						}
					},
					"aoColumns": [
						{"bSortable": false ,"data" : "id"		, "sWidth": "20px","sClass" : "hidden-xs , text-center"},
						{"bSort" : true		,"data" : "address"	},
						{"bSortable": true	,"data" : "country"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "city"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "district"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "subdistrict"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "postal"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "remark"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": false	,"data" : "id" 		, "sWidth": "80px","sClass" : "text-center"}
					],"order": [[ 1, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						
						if ( $.inArray(data.DT_RowId, $("#tblAddress").data("selected")) !== -1 ) {
							$(row).addClass('selected');
							selected = true;
						}
						$('td', row).eq(0).html(fn.ui.checkbox("chk_address",data.id,selected));
						
						s += fn.ui.button("btn btn-xs btn-outline-dark","fa fa-pen","fn.app.contact.address.dialog_edit("+data.id+")");
						if(data.priority != 1){
							s += fn.ui.button("btn btn-xs btn-outline-success ml-1","fa fa-check","fn.app.contact.address.dialog_primary("+data.id+")");
							s += fn.ui.button("btn btn-xs btn-outline-danger ml-1","fa fa fa-window-close","fn.app.contact.address.dialog_remove("+data.id+")");
						}else{
						}
						$('td', row).eq(8).html(s);
						
					}
				});
				fn.ui.datatable.selectable('#tblAddress','chk_address');
			}	
		});
	};
	
	fn.app.contact.address.dialog_add = function(type,id) {
		$.ajax({
			url: "apps/contact/view/dialog.address.add.php",
			type: "POST",
			data : {type:type,id:id},
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_add_address"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_addaddress] select[name=country]",
					"form[name=form_addaddress] select[name=city]",
					"form[name=form_addaddress] select[name=district]",
					"form[name=form_addaddress] select[name=subdistrict]");
				fn.app.contact.address.load_country("form[name=form_addaddress] select[name=country]");
				
			}	
		});
	};
	
	fn.app.contact.address.add = function(){
		$.post('apps/contact/xhr/action-add-address.php',$('form[name=form_addaddress]').serialize(),function(response){
			if(response.success){
				$("#tblAddress").DataTable().ajax.reload(null,false);
				$("#dialog_add_address").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},'json');
		return false;
	};
	
	fn.app.contact.address.dialog_edit = function(id) {
		$.ajax({
			url: "apps/contact/view/dialog.address.edit.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_edit_address"});
				$('select[name=country]').select2();
				fn.app.contact.address.initial(
					"form[name=form_editaddress] select[name=country]",
					"form[name=form_editaddress] select[name=city]",
					"form[name=form_editaddress] select[name=district]",
					"form[name=form_editaddress] select[name=subdistrict]");
			}
		});
	};
	
	fn.app.contact.address.edit = function(){
		$.post('apps/contact/xhr/action-edit-address.php',$('form[name=form_editaddress]').serialize(),function(response){
			if(response.success){
				$("#tblAddress").DataTable().ajax.reload(null,false);
				$("#dialog_edit_address").modal('hide');
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
			
		},'json');
		return false;
	};
	
	fn.app.contact.address.dialog_remove = function(id) {
		var item_selected = $("#tblAddress").data("selected");
		$.ajax({
			url: "apps/contact/view/dialog.address.remove.php",
			data: {item:(id != null?id:item_selected)},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_remove_address"});
			}
		});
	};
	
	fn.app.contact.address.remove = function(id){
		var item_selected = $("#tblAddress").data("selected");
		$.post('apps/contact/xhr/action-remove-address.php',{item:(id != null?id:item_selected)},function(response){
			$("#tblAddress").data("selected",[]);
			$("#tblAddress").DataTable().ajax.reload(null,false);
			$('#dialog_remove_address').modal('hide');
		});
		
	};
	
	fn.app.contact.address.dialog_primary = function(id) {
		$.ajax({
			url: "apps/contact/view/dialog.address.primary.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_primary_address"});
			}	
		});
	};
	
	fn.app.contact.address.primary = function(id){
		$.post('apps/contact/xhr/action-set-primary-address.php',{id:id},function(response){
			$("#tblAddress").DataTable().ajax.reload(null,false);
			$('#dialog_primary_address').modal('hide');
		});
		
	};
	
	