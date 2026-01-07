fn.app.production_scrap.scrap.dialog_edit = function(id) {
    $.ajax({
        url: "apps/production_scrap/view/dialog.scrap.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_scrap"});
        }
    });
};

fn.app.production_scrap.scrap.edit = function(){
    $.post("apps/production_scrap/xhr/action-edit-scrap.php",$("form[name=form_editscrap]").serialize(),function(response){
        if(response.success){
            $("#tblScrapData").DataTable().draw();
            $("#dialog_edit_scrap").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
