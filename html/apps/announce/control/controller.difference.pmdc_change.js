fn.app.announce.difference.dialog_pmdc_change = function(id) {
    $.ajax({
        url: "apps/announce/view/dialog.difference.pmdc_change.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_pmdc_change"});
            }
        });
};

fn.app.announce.difference.pmdc_change = function(){
    $.post("apps/announce/xhr/action-pmdc_change.php",$("form[name=form_pmdc_change]").serialize(),function(response){
        if(response.success){
            $("#dialog_pmdc_change").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
