fn.app.sales_silver.quick_buyorder.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/sales_silver/xhr/action-remove-quick_buyorder.php",{item:id},function(response){
                $("#tblQuickBuyOrder").DataTable().draw();
                fn.notify.successbox("","Remove Success");
                fn.reload()
            });
        });
    }
};