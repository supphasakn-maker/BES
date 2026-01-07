fn.app.announce.difference.dialog_change_buy = function(id) {
    $.ajax({
        url: "apps/announce/view/dialog.difference.change_buy.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_change_buy"});
            }
        });
};

fn.app.announce.difference.change_buy = function(){
    $.post("apps/announce/xhr/action-change_buy.php",$("form[name=form_change_buy]").serialize(),function(response){
        if(response.success){
            $("#dialog_change_buy").modal("hide");
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
