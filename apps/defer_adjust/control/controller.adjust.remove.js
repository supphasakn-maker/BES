fn.app.defer_adjust.adjust.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/defer_adjust/xhr/action-remove-adjust.php",{item:id},function(response){
                $("#tblAdjust").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};