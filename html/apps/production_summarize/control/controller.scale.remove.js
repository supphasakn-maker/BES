
fn.app.production_summarize.scale.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_summarize/xhr/action-remove_scale.php",{id:id},function(response){
                $("#tblScale").DataTable().draw();
            });
        }
    });

};
