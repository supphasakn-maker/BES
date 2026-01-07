fn.app.claim.product.reject = function(id){
    bootbox.confirm("Are you sure to Rejected Claim?", function(result){ 
        if(result){
            $.post("apps/claim/xhr/action-reject-product.php",{id:id},function(response){
                $("#tblProduct").DataTable().draw();
            });
        }
    });
};