fn.app.production_prepare.prepare.dialog_add_oven = function(id) {
    $.ajax({
        url: "apps/production_prepare/view/prepare/dialog.pack.add_oven.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_oven"});
            }
        });
};
fn.app.production_prepare.prepare.add_oven= function(){
    $.post("apps/production_prepare/xhr/action-add-add_oven.php",$("form[name=form_add_oven]").serialize(),function(response){
        if(response.success){
            $("#tblOven").DataTable().draw();
            $("#dialog_add_oven").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};


$("#tblOven").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth" : true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "data" : function(d){
            d.production_id = $("#tblOven").attr("data-id");
        },
        "url":"apps/production_prepare/store/store-oven.php"
    },
    "aoColumns": [
        {"bSort":true		,"data":"id",	class: "text-center"	},
        {"bSort":true		,"data":"date",	class: "text-center"	},
        {"bSort":true		,"data":"oven",	class: "text-center"	},
        {"bSort":true		,"data":"time_start",	class: "text-center"	},
        {"bSort":true		,"data":"time_end",	class: "text-center"	},
        {"bSort":true		,"data":"temp",	class: "text-center"	},
        {"bSort":true		,"data":"remark",	class: "text-center"	},
        {"bSort":true		,"data":"user",	class: "text-center"	},
        {"bSort":true		,"data":"status",	class: "text-center"	}
    ],"order": [[ 1, "desc" ]],
    "createdRow": function ( row, data, index ) {
        
        var s = '';
        
        if(data.status=="0"){
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_prepare.oven.remove("+data[0]+")");
        }else{
            s += '<span class="badge badge-warning">-</span>';
        }
        $("td", row).eq(8).html(s);
        
    }
});
