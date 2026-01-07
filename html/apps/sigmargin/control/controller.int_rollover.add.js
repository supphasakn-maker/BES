fn.app.sigmargin.int_rollover.dialog_add = function() {
    $.ajax({
        url: "apps/sigmargin/view/dialog.int_rollover.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_int_rollover"});
        }
    });
};

fn.app.sigmargin.int_rollover.add = function(){
    $.post("apps/sigmargin/xhr/action-add-int_rollover.php",$("form[name=form_addint_rollover]").serialize(),function(response){
        if(response.success){
            $("#tblInt_rollover").DataTable().draw();
            $("#dialog_add_int_rollover").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
$(".btn-area").append(fn.ui.button({
    class_name : "btn btn-light has-icon",
    icon_type : "material",
    icon : "add_circle_outline",
    onclick : "fn.app.sigmargin.int_rollover.dialog_add()",
    caption : "Add"
}));
