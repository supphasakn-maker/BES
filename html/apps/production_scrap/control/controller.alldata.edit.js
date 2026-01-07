fn.app.production_scrap.scrap.dialog_edit_refine = function(id) {
    $.ajax({
        url: "apps/production_scrap/view/dialog.scrap.editrefine.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_editrefine_scrap"});
        }
    });
};

fn.app.production_scrap.alldata.edit_refine = function(){
    $.post("apps/production_scrap/xhr/action-edit-scraprefine.php",$("form[name=form_editscrap]").serialize(),function(response){
        if(response.success){
            $("#tblScrapRefine").DataTable().draw();
            $("#dialog_editrefine_scrap").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
