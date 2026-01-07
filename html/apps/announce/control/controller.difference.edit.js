fn.app.announce.difference.dialog_edit = function(id) {
    $.ajax({
        url: "apps/announce/view/dialog.difference.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_difference"});
            }
        });
};

fn.app.announce.difference.edit = function(){
    $.post("apps/announce/xhr/action-edit-difference.php",$("form[name=form_editdifference]").serialize(),function(response){
        if(response.success){
            $("#dialog_edit_difference").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
