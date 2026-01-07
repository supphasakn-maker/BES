$("#tblMessage").data( "selected", [] );
$('#tblMessage').DataTable({
	"bStateSave": true,
	"sDom": fn.config.datatable.sDom,
	"oLanguage": fn.config.datatable.oLanguage,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/profile/store/store-message.php",
	"aoColumns": [
		{"bSortable":false	,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":true	,"data":"created"	,"sClass":"text-center"},
		{"bSort":true		,"data":"fullname"	},
		{"bSortable":true	,"data":"msg"	,"sClass":"hidden-xs text-center"},
		{"bSortable":true	,"data":"opened"	,"sClass":"hidden-xs text-center"},
		{"bSortable":false	,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblMessage").data( "selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_message",data[0],selected));
		$('td', row).eq(1).html(moment(data.created).format("DD/MM/YYYY HH:mm:ss"));

		s = '';
		s += fn.ui.button("btn btn-xs btn-default","fa fa-eye","fn.app.profile.message.dialog_open("+data.id+")");
		$('td', row).eq(5).html(s);
	}
});
fn.ui.datatable.selectable('#tblMessage','chk_message');

fn.app.profile.message.dialog_open = function(id){
	$.ajax({
		url: "ajax/notify/dialog.message.view.php",
		data: {id:id},
		type: "POST",
		dataType: "html",
		success: function(html){
			$("body").append(html);
			$("#dialog_view_message").on("hidden.bs.modal",function(){
				$(this).remove();
			});
			$("#dialog_view_message").modal('show');
		}
	});
}


