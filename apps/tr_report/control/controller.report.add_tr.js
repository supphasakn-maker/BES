fn.app.tr_report.report.add_tr = function(){
    $.post("apps/tr_report/xhr/action-add-tr.php",$("form[name=form_addtr]").serialize(),function(response){
        fn.dialog.confirmbox("Confirmation","Are you sure to Add",function(){
        if(response.success){
            fn.reload();
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    });
    },"json");


    return false;
};
