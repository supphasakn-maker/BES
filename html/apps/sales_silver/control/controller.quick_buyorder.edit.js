fn.app.sales_silver.quick_buyorder.dialog_edit = function(id) {
    $.ajax({
        url: "apps/sales_silver/view/dialog.quick_buyorder.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_quick_buyorder"});
        }
    });
};

fn.app.sales_silver.quick_buyorder.edit = function(){
    $.post("apps/sales_silver/xhr/action-edit-quick_buyorder.php",$("form[name=form_editquick_buyorder]").serialize(),function(response){
        if(response.success){
            $("#tblQuickBuyOrder").DataTable().draw();
            $("#dialog_edit_quick_buyorder").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
