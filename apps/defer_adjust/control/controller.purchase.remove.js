fn.app.defer_adjust.purchase.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/defer_adjust/xhr/action-remove-purchase.php",{item:id},function(response){
                $("#tblPurchase").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};