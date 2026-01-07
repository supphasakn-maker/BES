fn.app.coa_coc.run_number.dialog_edit = function(id) {
    $.ajax({
        url: "apps/coa_coc/view/dialog.run-number.edit.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_edit_run-number"});

            $("form[name=form_editrun-number] select[name=order_id]").select2();
            $("form[name=form_editrun-number] select[name=order_id]").unbind().change(function(){
                $.post("apps/coa_coc/xhr/action-load-order.php",{order_id:$(this).val()},function(response){
                    $("form[name=form_editrun-number] input[name=customer_name]").val(response.order.customer_name);
                    $("form[name=form_editrun-number] input[name=customer_id]").val(response.order.customer_id);
                    $("form[name=form_editrun-number] input[name=order_code]").val(response.order.code);
                    $("form[name=form_editrun-number] input[name=delivery_date]").val(response.order.delivery_date);
                },"json");
            }).change();
        }
    });
};

fn.app.coa_coc.run_number.edit = function(){
    $.post("apps/coa_coc/xhr/action-edit-run-number.php",$("form[name=form_editrun-number]").serialize(),function(response){
        if(response.success){
            $("#tblRun").DataTable().draw();
            $("#dialog_edit_run-number").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
