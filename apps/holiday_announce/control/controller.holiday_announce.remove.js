fn.app.holiday_announce.holiday.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/holiday_announce/xhr/action-remove-holiday_announce.php",{item:id},function(response){
                $("#tblSilver").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};