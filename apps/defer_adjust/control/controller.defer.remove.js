fn.app.defer_adjust.defer.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/defer_adjust/xhr/action-remove-defer.php",{item:id},function(response){
                $("#tblDefer").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};