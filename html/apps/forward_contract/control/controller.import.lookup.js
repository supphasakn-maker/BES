
	fn.app.forward_contract.import.dialog_lookup = function(id) {
		var multiple = true
		$.ajax({
			url: "apps/forward_contract/view/dialog.import.lookup.php",
			type: "POST",
			data: {id:id},
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_import_lookup").on("hidden.bs.modal",function(){$(this).remove();});
				$("#dialog_import_lookup").modal('show');
				
				$("#tblImportLookup").data( "selected",[]);
				$('#tblImportLookup').DataTable({
					"bStateSave": true,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": "apps/forward_contract/store/store-import.php",
					"aoColumns": [
						{"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
						{"bSortable":true		,"data":"parent",class:"text-center"	},
						{"bSortable":true		,"data":"delivery_date",class:"text-center"	},
						{"bSortable":true		,"data":"supplier",class:"text-center"	},
						{"bSortable":true		,"data":"amount",class:"text-center"	},
						{"bSortable":true		,"data":"delivery_by",class:"text-center"	},
						{"bSortable":true		,"data":"type",class:"text-center"	},
						{"bSortable":true		,"data":"comment",class:"text-center"	},
					],"order": [[ 2, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						
						if ( $.inArray(data.DT_RowId, $("#tblImportLookup").data("selected")) !== -1 ) {
							$(row).addClass('selected');
							selected = true;
						}
						$('td', row).eq(0).html(fn.ui.checkbox("chk_import",data[0],selected,multiple));
							
						
					}
				});
				fn.ui.datatable.selectable('#tblImportLookup','chk_import',multiple);
				
			}	
		});
		
	};
	
	fn.app.forward_contract.import.select = function(id) {
		if($("#tblImportLookup").data("selected").length==0){
			fn.notify.warnbox("Please select import!","No selected");
		}else{
			$.post("apps/forward_contract/xhr/action-map-import.php",{id:id,import:$("#tblImportLookup").data("selected")},function(json){
				$("#dialog_import_lookup").modal('hide');
			},'json');
		}
	}
	
	fn.app.forward_contract.import.unmap = function(id) {
		bootbox.confirm({
			message: "Are sure to unmatch this record?",
			buttons: {
				confirm: {label: 'Remove',className: 'btn-danger'},
				cancel: {label: 'No',className: 'btn-secondary'}
			},
			callback: function (result) {
				if(result){
					$.post("apps/forward_contract/xhr/action-unmap-import.php",{id:id},function(response){
						$("#dialog_import_lookup").modal('hide');
					},"json");
				}
			}
		});

	}

	
