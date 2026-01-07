fn.app.defer_adjust.purchase.add = function(){
    $.post("apps/defer_adjust/xhr/action-add_purchase.php",$("form[name=form_addpurchase]").serialize(),function(response){
        fn.dialog.confirmbox("Confirmation","Are you sure to Add",function(){
        if(response.success){
            $("#tblPurchase").DataTable().draw();
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    });
    },"json");


    return false;
};

