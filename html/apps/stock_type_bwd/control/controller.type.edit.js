fn.app.stock_type_bwd.type.dialog_edit = function(id) {
    $.ajax({
        url: "apps/stock_type_bwd/view/dialog.type.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_type"});
        }
    });
};

fn.app.stock_type_bwd.type.edit = function(){
    $.post("apps/stock_type_bwd/xhr/action-edit-type.php",$("form[name=form_edittype]").serialize(),function(response){
        if(response.success){
            $("#tblType").DataTable().draw();
            $("#dialog_edit_type").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
