var date_base = new Date();
var weekday = ['อา','จ','อ','พ','พฤ','ศ','ส'];


$("#tblOrder").data( "selected", [] );
$("#tblOrder").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,	
	"ajax": {
		"url": "apps/schedule/store/store-order.php",
		"data": function ( d ) {
			d.date_from = $("form[name=filter] input[name=from]").val();
			d.date_to = $("form[name=filter] input[name=to]").val();
		}
	},
	"aoColumns": [
		{"bSortable":false		,"data":"id"	,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
		{"bSortable":false		,"data":"id",	"sWidth": "40px","class":"text-center"	},
		{"bSortable":true		,"data":"id",	"sWidth": "40px","class":"text-center"	},
		{"bSortable":true		,"data":"code", "class":"text-center"	},
		{"bSort":true			,"data":"delivery_code",	"class":"text-center"	},
		{"bSort":true			,"data":"customer_name",	"class":"text-center"},
		{"bSort":true			,"data":"amount",			"class":"text-right pr-2"	},
		{"bSort":true			,"data":"price",			"class":"text-right pr-2"	},
		{"bSort":true			,"data":"vat",		"class":"text-right pr-2"	},
		{"bSort":true			,"data":"net",				"class":"text-right pr-2"	},
		{"bSort":true			,"data":"date",				"class":"text-center"	},
		{"bSort":true			,"data":"delivery_date",	"class":"text-center"	},
		{"bSortable":false		,"data":"id",				"class":"text-center"	},
		{"bSort":true			,"data":"sales"										},
		{"bSortable":false		,"data":"id","class":"text-center"  }
	],"order": [[ 4, "desc" ]],
	"createdRow": function ( row, data, index ) {
		var selected = false,checked = "",s = '';
		if ( $.inArray(data.DT_RowId, $("#tblOrder").data( "selected")) !== -1 ) {
			$(row).addClass("selected");
			selected = true;
		}
		$("td", row).eq(0).html(fn.ui.checkbox("chk_order",data[0],selected));
		$("td", row).eq(1).html(fn.ui.button("btn btn-xs btn-outline-dark","far fa-cut","fn.app.sales.order.dialog_split("+data[0]+")"));
		$("td", row).eq(2).html(fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-pen ","fn.app.sales.order.dialog_edit("+data[0]+")"));
		$("td", row).eq(3).html(fn.ui.button("btn btn-xs btn-danger mr-1","far fa-trash ","fn.app.sales.order.dialog_remove_each("+data[0]+")"));
		
		
		if(data.delivery_id == null){
			$("td", row).eq(11).html(fn.ui.button("btn btn-xs btn-outline-danger","far fa-truck ","fn.app.sales.order.dialog_add_delivery("+data[0]+")"));
		}else{
			
		}
		
		if(data.delivery_id != null){
			s = '';
			s += fn.ui.button("btn btn-xs btn-outline-warning mr-1","far fa-truck ","fn.app.sales.order.dialog_postpone("+data[0]+")");
			s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-lock ","fn.app.sales.order.dialog_lock("+data[0]+")");
			$("td", row).eq(12).html(s);
		}else{
			$("td", row).eq(12).html('<span class="badge badge-danger">Lock</span>');
		}
		
		$("td", row).eq(3).html('<a href="#apps/schedule/index.php?view=printable&order_id='+data.id+'">'+data.code+'</a>');
		
		s = '';
		
		$("td", row).eq(14).html(s).attr("date",data.delivery_date).addClass("show_date");
	},
	"drawCallback": function( settings ) {
       fn.app.schedule.order.date_update();
    }
});
fn.ui.datatable.selectable("#tblOrder","chk_order");

fn.app.schedule.order.date_update = function(){
	var date_iterator = new Date();
	var s ='';
	s += '<table class="table table-xs mb-0">';
		s += '<tbody>';
			s += '<tr>';
				s += '<th width="20" class="p-0 m-0"><button onclick="fn.app.schedule.order.date_previous()" class="btn btn-xs btn-dark  m-0"><i data-feather="chevron-left"></i></button></th>';
				for(var i=0;i<7;i++){
					s += '<th width="35" class="text-center">';
						date_iterator.setDate(date_base.getDate() + i);
						var show = weekday[date_iterator.getDay()] + "." + date_iterator.getDate();
						s += show;
					s += '</th>';
				}
				s += '<th width="20" class="p-0"><button onclick="fn.app.schedule.order.date_next()" class="btn btn-xs btn-dark m-0"><i data-feather="chevron-right"></i></button></th>';
			s += '</tr>';
		s += '<tbody>';
	s += '</table>';
	$('#schedule_header').html(s);
	
	
	$(".show_date").each(function(){
		s = '';
		s += '<table class="table table-xs mb-0">';
			s += '<tbody>';
				s += '<tr>';
					s += '<td width="20"></td>';
					for(var i=0;i<7;i++){
						date_iterator.setDate(date_base.getDate() + i);
						const compare = moment(date_iterator);
						if(compare.format('YYYY-MM-DD')==$(this).attr('date')){
							s += '<td width="35" class="text-center"><i class="fa fa-sm fa-truck"></i></td>';
						}else{
							s += '<td width="35" class="text-center"><i class="fa fa-sm fa-minus"></i></td>';
						}
					}
					s += '<td width="20"></td>';
				s += '</tr>';
			s += '<tbody>';
		s += '</table>';
		$(this).html(s);
	});
}


fn.app.schedule.order.date_next = function(){
	date_base.setDate(date_base.getDate() + 1);
	fn.app.schedule.order.date_update();
}

fn.app.schedule.order.date_previous = function(){
	date_base.setDate(date_base.getDate() - 1);
	fn.app.schedule.order.date_update();
}

