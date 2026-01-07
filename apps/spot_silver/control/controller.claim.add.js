fn.app.spot_silver.claim.add = function(){
    $.post("apps/spot_silver/xhr/action-add-claim.php",$("form[name=form_addspot]").serialize(),function(response){
        fn.dialog.confirmbox("Confirmation","Are you sure to Add",function(){
        if(response.success){
            $("#tblClaim").DataTable().draw();
            $("form[name=form_addspot]")[0].reset();
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    });
    },"json");
    return false;
};
