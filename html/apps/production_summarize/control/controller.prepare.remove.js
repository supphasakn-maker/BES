fn.app.production_summarize.prepare.dialog_remove = function() {
    var item_selected = $("#tblPrepare").data("selected");
    $.ajax({
        url: "apps/production_summarize/view/dialog.prepare.remove.php",
        data: {item:item_selected},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_remove_prepare").on("hidden.bs.modal",function(){
                $(this).remove();
            });
            $("#dialog_remove_prepare").modal("show");
            $("#dialog_remove_prepare .btnConfirm").click(function(){
                fn.app.production_prepare.prepare.remove();
            });
        }
    });
};

fn.app.production_summarize.prepare.remove = function(id){
    bootbox.confirm("Are you sure to remove?", function(result){ 
        if(result){
            $.post("apps/production_summarize/xhr/action-remove-prepare.php",{id:id},function(response){
                $("#tblPrepare").data("selected",[]);
                $("#tblPrepare").DataTable().draw();
                $("#dialog_remove_prepare").modal("hide");
            });
        }
    });

};

