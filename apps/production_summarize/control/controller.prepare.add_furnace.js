fn.app.production_summarize.prepare.dialog_add_furnace = function(id) {
    $.ajax({
        url: "apps/production_summarize/view/prepare/dialog.pack.add_furnace.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_furnace"});
            }
        });
};
fn.app.production_summarize.prepare.add_furnace = function(){
    $.post("apps/production_summarize/xhr/action-add-add_furnace.php",$("form[name=form_add_furnace]").serialize(),function(response){
        if(response.success){
            $("#tblFurnace").DataTable().draw();
            $("#dialog_add_furnace").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};


$("#tblFurnace").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth" : true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "data" : function(d){
            d.production_id = $("#tblOven").attr("data-id");
        },
        "url":"apps/production_summarize/store/store-furnace.php"
    },
    "aoColumns": [
        {"bSort":true		,"data":"id",	class: "text-center"	},
        {"bSort":true		,"data":"date",	class: "text-center"	},
        {"bSort":true		,"data":"furnace",	class: "text-center"	},
        {"bSort":true		,"data":"time_start",	class: "text-center"	},
        {"bSort":true		,"data":"time_end",	class: "text-center"	},
        {"bSort":true		,"data":"crucible",	class: "text-center"	},
        {"bSort":true		,"data":"amount",	class: "text-center"	},
        {"bSort":true		,"data":"remark",	class: "text-center"	},
        {"bSort":true		,"data":"user",	class: "text-center"	},
        {"bSort":true		,"data":"status",	class: "text-center"	}
    ],"order": [[ 1, "asc" ]],
    "createdRow": function ( row, data, index ) {
        
        var s = '';
        
        if(data.status=="0"){
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_summarize.furnace.remove("+data[0]+")");
        }else{
            s += '<span class="badge badge-warning">-</span>';
        }
        $("td", row).eq(9).html(s);
        
    }
});
