fn.app.claim.product.dialog_remove = function() {
    var item_selected = $("#tblPrepare").data("selected");
    $.ajax({
        url: "apps/production_prepare/view/dialog.product.remove.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_remove_product").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_remove_product").modal("show");
            $("#dialog_remove_product .btnConfirm").click(function(){
                fn.app.claim.product.remove();
            });
        }
    });
};

fn.app.claim.product.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/claim/xhr/action-remove-product.php",{id:id},function(response){
                $("#tblProduct").data("selected",[]);
                $("#tblProduct").DataTable().draw();
                $("#dialog_remove_product").modal("hide");
            });
        }
    });

};

