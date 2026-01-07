fn.app.stock_silver.future.dialog_move = function() {
    var item_selected = $("#tblStockFuture").data("selected");
    $.ajax({
        url: "apps/stock_silver/view/dialog.future.move.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_move_future").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_move_future").modal("show");
            $("#dialog_move_future .btnConfirm").click(function(){
                fn.app.stock_silver.future.move();
            });
        }
    });
};

fn.app.stock_silver.future.move = function(){
    var item_selected = $("#tblStockFuture").data("selected");
    $.post("apps/stock_silver/xhr/action-move-future.php",{items:item_selected},function(response){
        $("#tblStockFuture").data("selected",[]);
        $("#tblStockFuture").DataTable().draw();
        $("#tblStockSilver").DataTable().draw();
        $("#dialog_move_future").modal("hide");
    });
};