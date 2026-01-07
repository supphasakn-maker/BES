fn.app.production_prepare.furnace.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_prepare/xhr/action-remove_furnace.php",{id:id},function(response){
                $("#tblFurnace").DataTable().draw();
            });
        }
    });

};
