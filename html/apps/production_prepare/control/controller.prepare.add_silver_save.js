fn.app.production_prepare.prepare.dialog_add_silver_save = function (id) {
    $.ajax({
        url: "apps/production_prepare/view/prepare/dialog.pack.add_silver_save.php",
        data: { id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_add_silver_save" });
        }
    });
};
fn.app.production_prepare.prepare.add_silver_save = function () {
    $.post("apps/production_prepare/xhr/action-add-add_silver_save.php", $("form[name=form_add_silver_save]").serialize(), function (response) {
        if (response.success) {
            $("#tblSilver").DataTable().draw();
            $("#dialog_add_silver_save").modal("hide");
        } else {
            fn.notify.warnbox(response.msg, "Oops...");
        }
    }, "json");
    return false;
};


$("#tblSilver").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "data": function (d) {
            d.production_id = $("#tblSilver").attr("data-id");
        },
        "url": "apps/production_prepare/store/store-silver_save.php"
    },
    "aoColumns": [
        { "bSort": true, "data": "id", class: "text-center" },
        { "bSort": true, "data": "date", class: "text-center" },
        { "bSort": true, "data": "bar", class: "text-center" },
        { "bSort": true, "data": "amount", class: "text-center" },
        { "bSort": true, "data": "time", class: "text-center" },
        { "bSort": true, "data": "user", class: "text-center" },
        { "bSort": true, "data": "status", class: "text-center" }
    ], "order": [[1, "asc"]],
    "createdRow": function (row, data, index) {

        var s = '';

        if (data.status == "0") {
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.production_prepare.silver_save.remove(" + data[0] + ")");
        } else {
            s += '<span class="badge badge-warning">-</span>';
        }
        $("td", row).eq(6).html(s);

    }
});
