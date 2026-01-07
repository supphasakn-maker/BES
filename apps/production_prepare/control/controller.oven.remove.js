
fn.app.production_prepare.oven.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_prepare/xhr/action-remove_oven.php",{id:id},function(response){
                $("#tblOven").DataTable().draw();
            });
        }
    });

};
