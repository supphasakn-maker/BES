$("#tblSilverDetail").DataTable({
	responsive: true,
	"bStateSave": true,
	"autoWidth" : true,
	"processing": true,
	"serverSide": true,
	"ajax": {
		"url" : "apps/sales_bwd/store/store-item.php",
		"data" : function(d){
			d.where = "bs_bwd_pack_items.delivery_id = "+$("#tblSilverDetail").attr("data-id");
		}
	},	
	"aoColumns": [
		{"bSort":true			,"data":"code", "class": "text-center"	},
		{"bSort":true			,"data":"pack_type" , "class": "text-center"	},
		{"bSort":true			,"data":"pack_name", "class": "text-center"	},
		{"bSort":true			,"data":"weight_expected", "class": "text-center"	},
		{"bSort":true			,"data":"amount", "class": "text-center"	},
		{"bSortable":false		,"data":"item_id"		,"sClass":"text-center" , "sWidth": "80px"  }
		
	],"order": [[ 1, "desc" ]],
	"createdRow": function ( row, data, index ) {

		var s = '';
		s += fn.ui.button("btn btn-xs btn-danger","far fa-trash","fn.app.sales_bwd.delivery.remove_mapping("+data[3]+")");
		$("td", row).eq(5).html(s);
	}
});


fn.app.sales_bwd.delivery.dialog_packing = function(id) {
    $.ajax({
        url: "apps/sales_bwd/view/dialog.delivery.packing.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_packing_delivery"});
        }
    });
};

fn.app.sales_bwd.delivery.packing = function(){
    $.post("apps/sales_bwd/xhr/action-packing-delivery.php",$("form[name=form_packing]").serialize(),function(response){
        if(response.success){
            $("#tblDelivery").DataTable().draw();
            $("#dialog_packing_delivery").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};


  

