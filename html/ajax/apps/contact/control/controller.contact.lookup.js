	fn.app.contact.contact.dialog_lookup = function(me) {
		$("body").data("input-org",me);
		$.ajax({
			url: "apps/contact/view/dialog.contact.lookup.php",
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				$("#dialog_contact_lookup").on("hidden.bs.modal",function(){
					$(this).remove();
				});
				$("#dialog_contact_lookup").modal('show');
				$("#tblOrganization").data("selected",null);
				$('#tblOrganization').DataTable({
					"bStateSave": true,
					"sDom": fn.config.datatable.sDom,
					"oLanguage": fn.config.datatable.oLanguage,
					"autoWidth" : true,
					"processing": true,
					"serverSide": true,
					"ajax": "apps/contact/store/store-contact.php",
					"aoColumns": [
						{"bSortable": false ,"data" : "id"			,"sClass" : "hidden-xs text-center" ,"sWidth": "20px"},
						{"bSortable": true	,"data" : "code"		,"sClass" : "hidden-xs text-center"},
						{"bSort" : true		,"data" : "name"},
						{"bSortable": true	,"data" : "email"		,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "industry"	,"sClass" : "hidden-xs text-center"},
						{"bSortable": true	,"data" : "type"		,"sClass" : "hidden-xs text-center"}
					],"order": [[ 1, "desc" ]],
					"createdRow": function ( row, data, index ) {
						var selected = false,checked = "",s = '';
						if ( $.inArray(data.DT_RowId, $("#tblOrganization").data( "selected")) !== -1 ) {
							$(row).addClass('selected');
							selected = true;
						}
						$('td', row).eq(0).html(fn.ui.checkbox("chk_contact",data[0],selected,false));
						
						if(data.phone != '')s += '<a href="javascript:;" class="btn btn-info btn-xs" rel="tooltip" data-toggle="tooltip" data-placement="top" title="'+data.phone+'"><span class="fa fa-phone"></span></a>';
						if(data.fax != '')s += '<a href="javascript:;" class="btn btn-info btn-xs" rel="tooltip" data-toggle="tooltip" data-placement="top" title="'+data.fax+'"><span class="fa fa-fax"></span></a>';
						if(data.email != '')s += '<a href="javascript:;" class="btn btn-info btn-xs" rel="tooltip" data-toggle="tooltip" data-placement="top" title="'+data.email+'"><span class="fa fa-envelope-o"></span></a>';
						$('td', row).eq(3).html(s);
						
						var s ='';
						if(data.industry_code)s += '<a href="javascript:;" class="label label-warning" rel="tooltip" data-toggle="tooltip" data-placement="top" title="'+data.industry+'">'+data.industry_code+'</a>';
						$('td', row).eq(4).html(s);
						
					},
					"drawCallback": function( settings ) {
						$("[rel=tooltip]").tooltip();
					}
				});
				fn.ui.datatable.selectable('#tblOrganization','chk_contact',false);
			}	
		});
	};
	
	fn.app.contact.contact.select = function(){
		var me = $("body").data("input-org");
		var val = $("#tblOrganization").data( "selected" );
		if(val == null){
			fn.engine.alert("Alert","Please any contact");
		}else{
			var input = $(me).parent().find("input[name=txtOrganization]");
			var caption = $(me).parent().find("#txtOrganization");
			var close_button = $(me).parent().find(".input-close");
			input.val(val);
			$.ajax({
				url: "apps/contact/xhr/action-load-contact.php",
				type: "POST",
				data:{id:val},
				dataType: "json",
				success: function(json){
					caption.val(json.name);
					close_button.removeClass("hidden");
					close_button.click(function(){
						$("#tblOrganization").data( "selected" ,null);
						caption.val("");
						input.val("");
						close_button.addClass("hidden");
					});
				}	
			});
			$("#dialog_contact_lookup").modal('hide');
		}
		return false;
	};