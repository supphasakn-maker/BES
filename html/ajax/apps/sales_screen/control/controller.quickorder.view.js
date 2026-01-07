$('#tblQuickOrder').DataTable({
		"paging": false,
		responsive: true,
		"bStateSave": true,
		"autoWidth" : true,
		"processing": true,
		"serverSide": true,
		"ajax": "apps/sales_screen/store/store-quick_order.php",
		"aoColumns": [
			{"bSortable":true	,"data":"created"	,class:"text-center"	},
			{"bSortable":true	,"data":"customer_name"		,class:"text-center"	},
			{"bSortable":true	,"data":"amount"	,class:"text-right"	},
			{"bSortable":true	,"data":"price"		,class:"text-right"	},
			{"bSortable":true	,"data":"status"	,class:"text-center"	},
			{"bSortable":true	,"data":"rate_spot"		,class:"text-center"	},
			{"bSortable":true	,"data":"rate_exchange"	,class:"text-center"	},
			{"bSortable":true	,"data":"remark"	,class:"text-center"	},
			{"bSortable":true	,"data":"sales"	,class:"text-center"	},
			{"bSortable":true	,"data":"product"	,class:"text-center"	},
		],"order": [[ 0, "desc" ]],
		"createdRow": function ( row, data, index ) {
			$('td', row).eq(0).html(moment(data.created).format("DD/MM/YYYY HH:mm:ss"));
			var s= '';
			
			if(data.status == "1"){
				s += fn.ui.button("btn btn-xs btn-outline-dark mr-1","far fa-pen","fn.app.sales.quick_order.dialog_edit("+data[0]+")");
				s += fn.ui.button("btn btn-xs btn-danger mr-1","far fa-times","fn.app.sales.quick_order.remove("+data[0]+")");
				s += fn.ui.button("btn btn-xs btn-primary mr-1","far fa-shopping-cart","fn.app.sales.quick_order.dialog_transform("+data[0]+")");
				$('td', row).eq(4).html(s);
			}else if(data.status == "0"){
				$('td', row).eq(4).html('<span class="badge badge-danger">ลบแล้ว</span>');
			}else{
				s += '<span class="badge badge-dark">created</span> ';
				s += '<a class="btn btn-xs btn-outline-dark mr-1" href="#apps/schedule/index.php?view=printable&order_id='+data.order_id+'" target="_blank"><i class="far fa-print"></i></a> ';
				
				$('td', row).eq(4).html(s);
			}
		},
		"footerCallback": function (row,data,start,end,display) {
			var api = this.api(),data;
			
			var tAmount = 0,tValue = 0;
			for(i in data){
				tAmount += parseFloat(data[i].amount);
				tValue += (parseFloat(data[i].amount)*parseFloat(data[i].price));
			}

			$("#tblQuickOrder [xname=tAmount]").html(fn.ui.numberic.format(tAmount,4));
			$("#tblQuickOrder [xname=tValue]").html(fn.ui.numberic.format(tValue,4));
			
        }
	});