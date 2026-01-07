
fn.app.claim.product.submit = function(id){
    bootbox.confirm("Are you sure to Submit?", function(result){ 
        if(result){
            $.post("apps/claim/xhr/action-submit-product.php",{id:id},function(response){
                $("#tblProduct").DataTable().draw();
            });
        }
    });
};