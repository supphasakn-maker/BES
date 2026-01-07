fn.app.production_scrap.scrap.dialog_combine = function(id) {
    var item_selected = $("#tblScrapRefine").data("selected");
    $.ajax({
        url: "apps/production_scrap/view/dialog.scrap.combine.php",
        data: {items:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_combine_scrap"});
            
            $("input[name=weight_actual]").val($("input[name=total_weight_actual]").val());
            
            $("form[name=form_combinescrap] select[name=pack_name]").unbind().change(function(){
                var caption_pack = $(this).val();
                var value_pack = $(this).find(":selected").attr("data-value");
                var readonly_pack = $(this).find(":selected").attr("data-readonly");
                $("form[name=form_combinescrap] input[name=weight_expected]").val(value_pack);
                if(readonly_pack=="false"){
                    $("form[name=form_combinescrap] input[name=weight_expected]").attr("readonly",false);
                }else{
                    $("form[name=form_combinescrap] input[name=weight_expected]").attr("readonly",true);
                }
            }).change();
        }
    });
};

fn.app.production_scrap.scrap.combine = function(){
    $.post("apps/production_scrap/xhr/action-combine-scrap.php",$("form[name=form_combinescrap]").serialize(),function(response){
        if(response.success){
            $("#tblScrapRefine").DataTable().draw();
            $("#dialog_combine_scrap").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};
