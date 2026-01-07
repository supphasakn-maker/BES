fn.app.stock_silver.silver.dialog_add = function() {
    $.ajax({
        url: "apps/stock_silver/view/dialog.stock_silver.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_silver"});
        }
    });
};

fn.app.stock_silver.silver.add = function(id) {
    $.post("apps/stock_silver/xhr/action-add-silver.php",$("form[name=form_addsilver]").serialize(),function(response){
        if(response.success){
            $("#tblStockSilver").DataTable().draw();
            $("#tblStockFuture").DataTable().draw();
            $("#dialog_add_silver").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};

