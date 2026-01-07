fn.app.claim.product.solve = function(id){
    bootbox.confirm("Are you sure to Solved?", function(result){ 
        if(result){
            $.post("apps/claim/xhr/action-solve-product.php",{id:id},function(response){
                $("#tblProduct").DataTable().draw();
            });
        }
    });
};