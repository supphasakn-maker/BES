fn.app.claim.product.dialog_edit = function(id) {
    $.ajax({
        url: "apps/claim/view/dialog.product.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_product"});
        }
    });
};

fn.app.claim.product.edit = function(){
    $.post("apps/claim/xhr/action-edit-product.php",$("form[name=form_editproduct]").serialize(),function(response){
        if(response.success){
            $("#tblProduct").DataTable().draw();
            $("#dialog_edit_product").modal("hide");
            window.location.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
