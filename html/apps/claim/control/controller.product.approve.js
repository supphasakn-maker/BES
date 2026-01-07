// fn.app.claim.product.dialog_approve = function(id) {
//     $.ajax({
//         url: "apps/claim/view/dialog.product.approve.php",
//         data: {id:id},
//         type: "POST",
//         dataType: "html",
//         success: function(html){
//             $("body").append(html);
//             fn.ui.modal.setup({dialog_id : "#dialog_approve_product"});
//         }
//     });
// };

// fn.app.claim.product.approve = function(){
//     $.post("apps/claim/xhr/action-approve-product.php",$("form[name=form_approveproduct]").serialize(),function(response){
//         if(response.success){
//             $("#tblProduct").DataTable().draw();
//             $("#dialog_approve_product").modal("hide");
//         }else{
//             fn.notify.warnbox(response.msg,"Oops...");
//         }
//     },"json");
//     return false;
// };

fn.app.claim.product.approve = function(id){
    bootbox.confirm("Are you sure to Approve?", function(result){ 
        if(result){
            $.post("apps/claim/xhr/action-approve-product.php",{id:id},function(response){
                $("#tblProduct").DataTable().draw();
            });
        }
    });
};