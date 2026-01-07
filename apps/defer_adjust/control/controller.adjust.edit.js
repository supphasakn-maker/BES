fn.app.defer_adjust.adjust.dialog_edit = function(id) {
    $.ajax({
        url: "apps/defer_adjust/view/dialog.adjust.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_adjust"});
            }
        });
};

fn.app.defer_adjust.adjust.edit = function(){
    $.post("apps/defer_adjust/xhr/action-edit-adjust.php",$("form[name=form_editadjust]").serialize(),function(response){
        if(response.success){
            $("#tblAdjust").DataTable().draw();
            $("#dialog_edit_adjust").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
