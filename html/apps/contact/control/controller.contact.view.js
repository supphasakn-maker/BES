$("#tblContact").data( "selected", [] );
$('#tblContact').DataTable({
	"bStateSave": true,
	responsive: true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/contact/store/store-contact.php",
	"aoColumns": [
		{"bSortable": false 	,"data" : "id" 			,"sClass" : "hidden-xs text-center" ,"sWidth": "20px"},
		{"bSortable" : false		,"data" : "avatar"		,"sClass" : "text-center unselectable"	},
		{"bSortable" : true		,"data" : "fullname"	},
		{"bSortable" : true		,"data" : "dob"			,"searchable": false,"sClass" : "text-center"		},
		{"bSortable" : true		,"data" : "gender"		,"sClass" : "text-center" },
		{"bSortable" : false	,"data" : "email"		,"sClass" : "text-left unselectable"},
		{"bSortable" : true		,"data" : "citizen_id"	,"sClass" : "text-center"},
		{"bSortable": false		,"data" : "id" 			,"sClass" : "text-center" , "sWidth": "80px"}
	],"order": [[ 2, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblContact").data( "selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_contact",data[0],selected));
		
		$('td', row).eq(1).html('<a href="javascript:void(0)" onclick="fn.app.engine.file.dialog_file(\'contact\','+data.id+')"><img class="img-circle" style="height:25px;" src="'+data.avatar+'" onerror=this.src=\'img/default/noimage.png\';""></a>');
		s += data.fullname + '<br>' + ((data.nickname != '')?('(' + data.nickname + ')'):"");
		
		$('td', row).eq(2).html(s);
		$('td', row).eq(3).html(data.dob==null?"-":moment(data.dob).format("DD/MM/YYYY"));
		
		var contact = JSON.parse(data.data);

		
		s = '';
		if(data.email != ""){s += '<div>e-mail : <a href="mailto:'+data.email+'">'+data.email+'</a></div>'}
		if(data.phone != ""){s += '<div>phone : <a href="tel:'+data.phone+'">'+data.phone+'</a></div>'}
		if(data.mobile != ""){s += '<div>mobile : <a href="tel:'+data.mobile+'">'+data.mobile+'</a></div>'}
		if(contact != null){
		if(contact.skype == null || contact.skype != ""){s += '<div>skype : <a href="skype:'+data.skype+'?call">'+data.skype+'</a></div>'}
		if(contact.facebook == null || contact.facebook != ""){s += '<div>facebook id : '+data.facebook+'</div>'}
		if(contact.google == null || contact.google != ""){s += '<div>google id : '+data.google+'</div>'}
		}
		$('td', row).eq(5).html(s);
		
		s = '';
		s += fn.ui.button("btn btn-xs btn-outline-primary mr-1","fa fa-map-marker","fn.app.contact.address.dialog('contact',"+data[0]+")");
		s += fn.ui.button("btn btn-xs btn-outline-dark","fa fa-pen","fn.app.contact.contact.dialog_edit("+data[0]+")");
		$('td', row).eq(7).html(s);
		
	},
	"drawCallback": function( settings ) {
		$("[rel=tooltip]").tooltip();
	}
});

fn.ui.datatable.selectable('#tblContact','chk_contact');


