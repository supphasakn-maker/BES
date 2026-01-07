fn.app.announce.announce_silver.dialog_edit = function(id) {
    $.ajax({
        url: "apps/announce/view/dialog.announce_silver.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_announce_silver"});
            }
        });
};

fn.app.announce.announce_silver.edit = function(){
    $.post("apps/announce/xhr/action-edit-announce_silver.php",$("form[name=form_editannounce]").serialize(),function(response){
        if(response.success){
            $("#tblSilver").DataTable().draw();
            $("#dialog_edit_announce_silver").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
