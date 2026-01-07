fn.app.production_blackdust.scrap.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_blackdust/xhr/action-remove_scrap.php",{id:id},function(response){
                $("#tblScrap").DataTable().draw();
            });
        }
    });

};
