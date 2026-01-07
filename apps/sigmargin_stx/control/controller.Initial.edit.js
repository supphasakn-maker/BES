fn.app.sigmargin_stx.Initial.dialog_edit = function(id) {
    $.ajax({
        url: "apps/sigmargin_stx/view/dialog.Initial.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_Initial"});
        }
    });
};

fn.app.sigmargin_stx.Initial.edit = function(){
    $.post("apps/sigmargin_stx/xhr/action-edit-Initial.php",$("form[name=form_editInitial]").serialize(),function(response){
        if(response.success){
            $("#tblInitial").DataTable().draw();
            $("#dialog_edit_Initial").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
