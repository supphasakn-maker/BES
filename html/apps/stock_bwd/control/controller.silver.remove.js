fn.app.stock_bwd.silver.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/stock_bwd/xhr/action-remove-silver.php",{item:id},function(response){
                $("#tblStockSilver").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};