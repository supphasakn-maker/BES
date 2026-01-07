
fn.app.production_summarize.oven.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_summarize/xhr/action-remove_oven.php",{id:id},function(response){
                $("#tblOven").DataTable().draw();
            });
        }
    });

};
