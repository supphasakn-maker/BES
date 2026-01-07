fn.app.production_crucible.crucible.dialog_lookup = function(id) {
    $.ajax({
        url: "apps/production_crucible/view/dialog.crucible.viewcrucible.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_viewcrucible"});
        }
    });
};

