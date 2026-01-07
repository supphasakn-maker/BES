fn.app.production_pmr.pmr.dialog_add = function() {
    $.ajax({
        url: "apps/production_pmr/view/dialog.pmr.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_pmr"});
        }
    });
};

fn.app.production_pmr.pmr.add = function(id) {
    $.post("apps/production_pmr/xhr/action-add-pmr.php",$("form[name=form_addpmr]").serialize(),function(response){
        if(response.success){
            $("#tblPmr").DataTable().draw();
            $("#dialog_add_pmr").modal("hide");
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
	onclick : "fn.app.production_pmr.pmr.dialog_add()",
	caption : "เพิ่มการส่งผลิต"
}));
