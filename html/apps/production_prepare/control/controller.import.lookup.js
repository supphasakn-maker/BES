
fn.app.production_prepare.import.dialog_lookup = function (id) {
    var multiple = true
    $.ajax({
        url: "apps/production_prepare/view/dialog.import.scrap.php",
        type: "POST",
        data: { id: id },
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_import_lookup").on("hidden.bs.modal", function () { $(this).remove(); });
            $("#dialog_import_lookup").modal('show');

            $("#tblImportLookup").data("selected", []);
            $('#tblImportLookup').DataTable({
                "bStateSave": true,
                "autoWidth": true,
                "processing": true,
                "serverSide": true,
                "ajax": "apps/production_prepare/store/store-import_scrap.php",
                "aoColumns": [
                    { "bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px" },
                    { "bSort": true, "data": "round", "class": "text-center" },
                    { "bSort": true, "data": "code", "class": "text-center" },
                    { "bSort": true, "data": "parent", "class": "text-center unselectable" },
                    { "bSort": true, "data": "created", "class": "text-center" },
                    { "bSort": true, "data": "pack_name", "class": "text-center" },
                    { "bSort": true, "data": "weight_expected", "class": "text-right" },
                    { "bSort": true, "data": "name", "class": "text-right" },
                    { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "80px" },
                ], "order": [[2, "desc"]],
                "createdRow": function (row, data, index) {
                    var selected = false, checked = "", s = '';

                    if ($.inArray(data.DT_RowId, $("#tblImportLookup").data("selected")) !== -1) {
                        $(row).addClass('selected');
                        selected = true;
                    }
                    $('td', row).eq(0).html(fn.ui.checkbox("chk_import", data[0], selected, multiple));


                }
            });
            fn.ui.datatable.selectable('#tblImportLookup', 'chk_import', multiple);

        }
    });

};

fn.app.production_prepare.import.dialog_select = function (id) {
    var item_selected = $("#tblImportLookup").data("selected");

    if (!item_selected || (Array.isArray(item_selected) && item_selected.length === 0)) {
        fn.notify.warnbox("กรุณาเลือกรายการที่ต้องการ", "ไม่มีรายการที่เลือก");
        return false;
    }

    $.ajax({
        url: "apps/production_prepare/view/dialog.scrap.select.php",
        data: { items: item_selected, id: id },
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            fn.ui.modal.setup({ dialog_id: "#dialog_combine_scrap" });

            $("input[name=weight_expected]").val($("input[name=total_weight_actual]").val());

            $("form[name=form_combinescrap] select[name=pack_name]").off('change').change(function () {
                var caption_pack = $(this).val();
                var value_pack = $(this).find(":selected").attr("data-value");
                var readonly_pack = $(this).find(":selected").attr("data-readonly");
                $("form[name=form_combinescrap] input[name=weight_expected]").val(value_pack);

                if (readonly_pack == "false") {
                    $("form[name=form_combinescrap] input[name=weight_expected]").prop("readonly", false);
                } else {
                    $("form[name=form_combinescrap] input[name=weight_expected]").prop("readonly", true);
                }
            }).change();
        },
        error: function (xhr, status, error) {
            fn.notify.errorbox("เกิดข้อผิดพลาดในการโหลด dialog");
            console.error("Dialog load error:", xhr, status, error);
        }
    });
};

fn.app.production_prepare.import.select = function () {
    var $form = $("form[name=form_combinescrap]");

    if (!$form.length) {
        fn.notify.errorbox("ไม่พบฟอร์มที่ต้องการ");
        return false;
    }

    if ($form.data('submitting')) {
        return false;
    }
    $form.data('submitting', true);

    $.post("apps/production_prepare/xhr/action-combine-scrap.php", $form.serialize(), function (response) {
        $form.data('submitting', false); 

        if (response.success) {
            $("#tblScrap").DataTable().draw();
            $("#dialog_combine_scrap").modal("hide");
            fn.notify.successbox("รวมข้อมูลสำเร็จ"); 
        } else {
            fn.notify.warnbox(response.msg || "เกิดข้อผิดพลาดในการรวมข้อมูล", "Oops...");
        }
    }, "json")
        .fail(function (xhr, status, error) {
            $form.data('submitting', false); 
            fn.notify.errorbox("เกิดข้อผิดพลาดในการเชื่อมต่อ");
            console.error("Combine scrap error:", xhr, status, error);
        });

    return false;
};