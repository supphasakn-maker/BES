fn.app.production_summarize.prepare.dialog_add_scrap = function(id) {
    $.ajax({
        url: "apps/production_summarize/view/prepare/dialog.pack.scrap.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_scrap"});
            }
        });
};
fn.app.production_summarize.prepare.add_scrap= function(){
    $.post("apps/production_summarize/xhr/action-add-add_scrap.php",$("form[name=form_add_scrap]").serialize(),function(response){
        if(response.success){
            $("#tblScrap").DataTable().draw();
            $("#dialog_add_scrap").modal("hide");
            $("input[name=weight_out_safe]").change();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};

$("#tblScrap").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth" : true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "data" : function(d){
            d.production_id = $("#tblScrap").attr("data-id");
        },
        "url":"apps/production_summarize/store/store-scrap.php"
    },
    "aoColumns": [
		{"bSort":true			,"data":"round" ,"class":"text-center"	},
		{"bSort":true			,"data":"code"	,"class":"text-center"},
		{"bSort":true			,"data":"status"	,"class":"text-center unselectable"},
		{"bSort":true			,"data":"created","class":"text-center"	},
		{"bSort":true			,"data":"pack_name"	,"class":"text-center"},
		{"bSort":true			,"data":"weight_expected"	,"class":"text-right"},
        {"bSort":true			,"data":"name"	,"class":"text-right"},
		{"bSortable":true		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  }
    ],"order": [[ 3, "desc" ]],
    "createdRow": function ( row, data, index ) {
        
        var s = '';
        
        if(data.status=="0"){
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_summarize.scrap.remove("+data[0]+")");
        }else{
            s += '<span class="badge badge-warning">-</span>';
        }

        if(data.status != 0){
            $("td", row).eq(2).html('<a class="badge badge-warning">Combined</a>');       		
        }else{
            $("td", row).eq(2).html('<a>ยังไม่ได้ใช้</a>');       		
        }
        $("td", row).eq(7).html(s);
        
    }
});
