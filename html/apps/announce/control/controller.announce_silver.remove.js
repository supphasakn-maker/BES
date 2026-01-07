fn.app.announce.announce_silver.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/announce/xhr/action-remove-announce_silver.php",{item:id},function(response){
                $("#tblSilver").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};