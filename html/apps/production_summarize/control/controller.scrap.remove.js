fn.app.production_summarize.scrap.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_summarize/xhr/action-remove_scrap.php",{id:id},function(response){
                $("#tblScrap").DataTable().draw();
            });
        }
    });

};
