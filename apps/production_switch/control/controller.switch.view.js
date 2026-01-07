$("#tblSwitch").DataTable({
    responsive: true,
    "bStateSave": true,
    "autoWidth": true,
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "apps/production_switch/store/store_switch.php",
        "data": function (d) {
            d.date_from = $("form[name=filter] input[name=from]").val();
            d.date_to = $("form[name=filter] input[name=to]").val();
        }
    },
    "aoColumns": [
        { "bSort": true, "data": "created", class: "text-center" },
        { "bSort": true, "data": "submited", class: "text-center" },
        { "bSort": true, "data": "date_back", class: "text-center" },
        { "bSort": true, "data": "round", class: "text-center" },
        { "bSort": true, "data": "round_turn", class: "text-center" },
        { "bSort": true, "data": "weight_out_packing", class: "text-center" },
        { "bSort": true, "data": "weight_out_packing", class: "text-center" },
        { "bSort": true, "data": "product_name", class: "text-right" },
        { "bSort": true, "data": "product_name_turn", class: "text-right" },
        { "bSort": true, "data": "remark", class: "text-center" },
        { "bSortable": false, "data": "id", "sClass": "text-center", "sWidth": "120px" }
    ], "order": [[1, "desc"]],
    "createdRow": function (row, data, index) {

        var s = '';
        if (data.status == "0") {
            s += fn.ui.button("btn btn-xs btn-outline-danger mr-1", "far fa-trash", "fn.app.production_switch.switch.remove(" + data[0] + ")");
            s += fn.ui.button("btn btn-xs btn-outline-warning mr-1", "far fa-thumbs-up", "fn.app.production_switch.switch.dialog_approve(" + data[0] + ")");
        } else if (data.status == "1") {
            s += fn.ui.button("btn btn-xs btn-outline-dark mr-1", "fas fa-random", "fn.app.production_switch.switch.dialog_edit(" + data[0] + ")");

            s += '<span class="badge badge-danger">ยังไม่ได้คืน</span>';
        } else {
            s += '<span class="badge badge-success">คืนเรียบร้อย</span>';
        }
        $("td", row).eq(10).html(s);
    }
});

fn.app.production_switch.switch.load_data = function () {
    $.post("apps/production_switch/xhr/action-load-data.php", { id: $("#tblPmrDetail").attr("data-id") }, function (json) {
        $("#amount_total").html(json.total);
        $("#amount_remain").html(json.remain);
        if (json.remain > 0) {
            $("#amount_remain").addClass("text-danger");
        } else {
            $("#amount_remain").removeClass("text-danger");
        }
    }, "json");

};
fn.app.production_switch.switch.load_data();