fn.app.stock_silver.silver.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/stock_silver/xhr/action-remove-silver.php",{item:id},function(response){
                $("#tblStockSilver").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};