fn.app.production_blackdust.prepare.dialog_add_scale = function(id) {
    $.ajax({
        url: "apps/production_blackdust/view/prepare/dialog.pack.add_scale.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_scale"});
            }
        });
};
fn.app.production_blackdust.prepare.add_scale= function(){
    $.post("apps/production_blackdust/xhr/action-add-add_scale.php",$("form[name=form_add_scale]").serialize(),function(response){
        if(response.success){
            $("#tblScale").DataTable().draw();
            $("#dialog_add_scale").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};

$("#tblScale").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth" : true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "data" : function(d){
            d.production_id = $("#tblScale").attr("data-id");
        },
        "url":"apps/production_blackdust/store/store-scale.php"
    },
    "aoColumns": [
        {"bSort":true		,"data":"id",	class: "text-center"	},
        {"bSort":true		,"data":"date",	class: "text-center"	},
        {"bSort":true		,"data":"scale",	class: "text-center"	},
        {"bSort":true		,"data":"approve_scale",	class: "text-center"	},
        {"bSort":true		,"data":"approve_packing",	class: "text-center"	},
        {"bSort":true		,"data":"approve_check",	class: "text-center"	},
        {"bSort":true		,"data":"remark",	class: "text-center"	},
        {"bSort":true		,"data":"status",	class: "text-center"	}
    ],"order": [[ 1, "desc" ]],
    "createdRow": function ( row, data, index ) {
        
        var s = '';
        
        if(data.status=="0"){
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1","far fa-trash","fn.app.production_blackdust.scale.remove("+data[0]+")");
        }else{
            s += '<span class="badge badge-warning">-</span>';
        }
        $("td", row).eq(7).html(s);
        
    }
});