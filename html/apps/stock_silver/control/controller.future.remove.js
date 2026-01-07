fn.app.stock_silver.future.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/stock_silver/xhr/action-remove-future.php",{item:id},function(response){
                $("#tblStockFuture").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};