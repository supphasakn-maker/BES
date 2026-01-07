fn.app.claim.product.confirm = function(id){
    bootbox.confirm("Are you sure to Closed?", function(result){ 
        if(result){
            $.post("apps/claim/xhr/action-confirm-product.php",{id:id},function(response){
                $("#tblProduct").DataTable().draw();
            });
        }
    });
};