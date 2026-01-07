fn.app.production_crucible.crucible.dialog_add = function() {
    $.ajax({
        url: "apps/production_crucible/view/dialog.crucible.add.php",
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_add_crucible"});
        }
    });
};
fn.app.production_crucible.crucible.add = function(id) {
    $.post("apps/production_crucible/xhr/action-add-crucible.php",$("form[name=form_addcrucible]").serialize(),function(response){
        if(response.success){
            $("#tblCrucible").DataTable().draw();
            $("#dialog_add_crucible").modal("hide");
            var s = '';
            
            s += '<div>จำนวนทีเพิ่ม ' + response.created.length + ' รายการ</div>';
            s += '<div>จำนวนที่ซ้ำ ' + response.redundant.length + ' รายการ</div>';
            
            
            fn.notify.warnbox(s,"Result");
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
	onclick : "fn.app.production_crucible.crucible.dialog_add()",
	caption : "เพิ่มเบ้า"
}));
