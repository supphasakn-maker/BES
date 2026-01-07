fn.app.defer_adjust.purchase.dialog_edit = function(id) {
    $.ajax({
        url: "apps/defer_adjust/view/dialog.purchase.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_purchase"});
            }
        });
};

fn.app.defer_adjust.purchase.edit = function(){
    $.post("apps/defer_adjust/xhr/action-edit-purchase.php",$("form[name=form_editpurchase]").serialize(),function(response){
        if(response.success){
            $("#tblPurchase").DataTable().draw();
            $("#dialog_edit_purchase").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
