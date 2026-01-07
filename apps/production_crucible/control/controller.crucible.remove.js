fn.app.production_crucible.crucible.remove = function(id){
    if(typeof id != "undefined"){
        fn.dialog.confirmbox("Confirmation","Are you sure to remove this item?",function(){
            $.post("apps/production_crucible/xhr/action-remove-crucible.php",{item:id},function(response){
                $("#tblCrucible").DataTable().draw();
                fn.notify.successbox("","Remove Success");
            });
        });
    }
};