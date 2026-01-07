
fn.app.contact.address.dialog_lookup = function (type, id, func) {
    $.ajax({
        url: "apps/contact/view/dialog.address.lookup.php",
        type: "POST",
        dataType: "html",
        success: function (html) {
            $("body").append(html);
            $("#dialog_address_lookup").on("hidden.bs.modal", function () {
                $(this).remove();
            });
            $("#dialog_address_lookup").modal('show');
            $("#tblAddressLookup").data("selected", null);
            $('#tblAddressLookup').DataTable({
                "bStateSave": true,
                "sDom": fn.config.datatable.sDom,
                "oLanguage": fn.config.datatable.oLanguage,
                "autoWidth": true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "apps/contact/store/store-address.php",
                    "data": function (d) {
                        d.id = id;
                        d.type = type;
                    }
                },
                "aoColumns": [
                    {"bSortable": false, "data": "id", "sClass": "hidden-xs text-center", "sWidth": "20px"},
                    {"bSortable": true, "data": "address"},
                    {"bSortable": true, "data": "country", "sClass": "hidden-xs text-center"},
                    {"bSortable": true, "data": "city", "sClass": "hidden-xs text-center"},
                    {"bSortable": true, "data": "district", "sClass": "hidden-xs text-center"},
                    {"bSortable": true, "data": "subdistrict", "sClass": "hidden-xs text-center"},
                    {"bSortable": true, "data": "postal", "sClass": "hidden-xs text-center"},
                    {"bSortable": true, "data": "type", "sClass": "hidden-xs text-center"},
                ],
                "createdRow": function (row, data, index) {
                    var selected = false, checked = "", s = '';
                    if ($.inArray(data.DT_RowId, $("#tblAddressLookup").data("selected")) !== -1) {
                        $(row).addClass('selected');
                        selected = true;
                    }
                    $('td', row).eq(0).html(fn.ui.checkbox("chk_address", data[0], selected, false));

                    if (data.type == null || data.type == '' || data.type == 'null') {
                        $('td', row).eq(7).html('<label class="label label-default">Contact</label>');
                    } else {
                        $('td', row).eq(7).html('<label class="label label-primary">Organization</label>');
                    }
                },
                "drawCallback": function (settings) {
                    $("[rel=tooltip]").tooltip();
                }
            });
            fn.ui.datatable.selectable('#tblAddressLookup', 'chk_address', false);
            $("#dialog_address_lookup .btnSelect").unbind().click(function () {
                if ($("#tblAddressLookup").data("selected") == null) {
                    fn.engine.alert("No selected", "Please select address!");
                } else {
                    $.post("apps/contact/xhr/action-load-address.php", {id: $("#tblAddressLookup").data("selected")}, function (json) {
                        func(json);
                        $("#dialog_address_lookup").modal('hide');
                    }, 'json');
                }
            });
        }
    });

};