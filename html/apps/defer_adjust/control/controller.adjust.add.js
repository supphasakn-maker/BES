
fn.app.defer_adjust.adjust.add = function(){
    $.post("apps/defer_adjust/xhr/action-add_adjust.php",$("form[name=form_addadjust]").serialize(),function(response){
        fn.dialog.confirmbox("Confirmation","Are you sure to Add",function(){
        if(response.success){
            $("#tblAdjust").DataTable().draw();
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    });
    },"json");


    return false;
};
