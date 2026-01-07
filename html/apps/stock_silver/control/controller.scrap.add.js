fn.app.stock_silver.scrap.dialog_add = function() {
    $.ajax({
        url: "apps/stock_silver/view/dialog.scrap.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_scrap"});
            
            $("form[name=form_addscrap] select[name=pack_name]").unbind().change(function(){
                var caption_pack = $("form[name=form_addscrap] select[name=pack_name]").val();
                var value_pack = $("form[name=form_addscrap] select[name=pack_name]").find(":selected").attr("data-value");
                var readonly_pack = $("form[name=form_addscrap] select[name=pack_name]").find(":selected").attr("data-readonly");
                
                $("form[name=form_addscrap] input[name=weight_expected]").val(value_pack);
                if(readonly_pack=="false"){
                    $("form[name=form_addscrap] input[name=weight_expected]").attr("readonly",false);
                }else{
                    $("form[name=form_addscrap] input[name=weight_expected]").attr("readonly",true);
                    
                }
                
            }).change();
        }
    });
};

fn.app.stock_silver.scrap.add = function(id) {
    $.post("apps/stock_silver/xhr/action-add-scrap.php",$("form[name=form_addscrap]").serialize(),function(response){
        if(response.success){
            $("#tblStock").DataTable().draw();
            $("#dialog_add_scrap").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};

fn.app.stock_silver.scrap.pack_update = function(input){

		
    $.ajax({
        url: "apps/stock_silver/xhr/action-pack-change.php",
        data: {
            id: $(input).attr("data-id"),
            weight_actual : $(input).val()
        },
        type: "POST",
        dataType: "html",
        success: function(html){
 
        }
    });
}
