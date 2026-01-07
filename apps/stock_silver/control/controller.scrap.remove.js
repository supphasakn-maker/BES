fn.app.stock_silver.scrap.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/stock_silver/xhr/action-remove-scrap.php",{item:id},function(response){
                $("#tblStock").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};