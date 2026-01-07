$("#tblUser").data( "selected", [] );
$('#tblUser').DataTable({
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": "apps/accctrl/store/store-user.php",
	"ajax": {
		"url": "apps/accctrl/store/store-user.php",
		"data": function ( d ) {
			d.account = $('#tblUser').attr('account');
		}
	},
	"aoColumns": [
		{"bSortable": false	,"data":"id", "sWidth": "20px",	 "sClass" : "hidden-xs text-center"},
		{"bSort" : true		,"data":"username",				 "sClass" : "text-center"},
		{"bSortable": false	,"data":"avatar",	"sWidth": "40px", "sClass" : "text-center"},
		{"bSortable": true	,"data":"fullname",				"sClass" : "hidden-xs"},
		{"bSort" : true		,"data":"groupname",				 "sClass" : "text-center"},
		{"bSortable": false	,"data":"phone", 				"sClass" : "hidden-xs text-center"},
		{"bSortable": false	,"data":"email", 				"sClass" : "hidden-xs text-center"},
		{"bSortable": true	,"data":"last_login", 			"sClass":"text-center hidden-xs"},
		{"bSortable": false	,"data":"id", 					"sClass" : "text-center", "sWidth": "30px" }
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblUser").data("selected")) !== -1 ) {
			$(row).addClass('selected');
			selected = true;
		}
		$('td', row).eq(0).html(fn.ui.checkbox("chk_user",data[0],selected));
		
		
		s = '';
		//var avatar = data.avatar==null?"img/default/user.png":data.avatar;
		//s += '<img src="'+avatar+'" alt="" class="img-round" height="25">';
		//$('td', row).eq(2).html(s);
		
		$('td', row).eq(2).html('<a href="javascript:void(0)" onclick="fn.app.engine.file.dialog_file(\'contact\','+data.contact_id+')"><img class="img-circle" style="height:25px;" src="'+data.avatar+'" onerror=this.src=\'img/default/noimage.png\';""></a>');
		
		s = '';
		if(data.phone)
		s += '<a href="tel:'+data.phone+'" class="badge bg-color-darken">'+fn.ui.numberic.phone(data.phone)+'</a>';
		
		if(data.mobile)
		s += '<a href="tel:'+data.mobile+'" class="badge bg-color-orange">'+fn.ui.numberic.phone(data.mobile)+'</a>';
		$('td', row).eq(5).html(s);
		
		//<td class="sorting_1">AngularJS UI design and development<br><small class="text-muted"><i>Budget: 5,000<i></i></i></small></td>
		/*
		s = '';
		var avatar = data[9]==null?"img/default/user.png":data[9];
		s += '<div class="media">';
				s += '<div class="media-left avatar">';
					s += '<img src="'+avatar+'" alt="" class="media-object img-circle" height="40">';
					s += '<span class="status '+(data[8]=="1"?'bg-success':'bg-danger')+'"></span>';
				s += '</div>';
				s += '<div class="media-body">';
					s += '<h5 class="media-heading">'+(data[2]!=" "?data[2]:"No Name")+'</h5>';
					s += '<p class="text-muted mb-0">'+(data[10]!=""?data[10]:"No-Email")+'</p>';
				s += '</div>';
		s += '</div>';
		$('td', row).eq(2).html(s);
		*/
			
		s = '';
		if(data.last_login=="-"){
				s += 'Never Login';
		}else{
			var d = new Date(data.last_login);
			s +=  moment(data.last_login).format("YYYY-MM-DD HH:mm:ss");
			//s += ''+d.format("d/m/Y")+'<br>';
			//s += ''+d.format("H:i:s")+'';
		}
		$('td', row).eq(7).html(s);
		
		s = '';
		
		s += fn.ui.button("btn btn-xs btn-outline-dark","far fa-pen","fn.app.accctrl.user.dialog_edit("+data[0]+")");
		$('td', row).eq(8).html(s);
	}
});
fn.ui.datatable.selectable('#tblUser','chk_user');