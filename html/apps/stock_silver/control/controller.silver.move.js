fn.app.stock_silver.silver.dialog_move = function() {
    var item_selected = $("#tblStockSilver").data("selected");
    $.ajax({
        url: "apps/stock_silver/view/dialog.silver.move.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_move_silver").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_move_silver").modal("show");
            $("#dialog_move_silver .btnConfirm").click(function(){
                fn.app.stock_silver.silver.move();
            });
        }
    });
};

fn.app.stock_silver.silver.move = function(){
    var item_selected = $("#tblStockSilver").data("selected");
    $.post("apps/stock_silver/xhr/action-move-silver.php",{items:item_selected},function(response){
        $("#tblStockSilver").data("selected",[]);
        $("#tblStockSilver").DataTable().draw();
        $("#tblStockFuture").DataTable().draw();
        $("#dialog_move_silver").modal("hide");
    });
};