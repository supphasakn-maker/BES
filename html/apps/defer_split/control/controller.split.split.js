fn.app.defer_split.spot.dialog_split = function(id) {
    $.ajax({
        url: "apps/defer_split/view/dialog.split.php",
        data: {id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_split_defer"});
            $("[name=form_splitdefer] [name=split]").change(function(){
                var amount = parseFloat($("[name=form_splitspot] [name=amount]").val());
                var splited = parseFloat($(this).val());
                var remain = amount-splited;
                $("[name=form_splitspot] [name=remain]").val(remain.toFixed(4));
            });
        }
    });
};

fn.app.defer_split.spot.append_split = function() {
    var s ='';
    s += '<div class="form-group row">';
        s += '<label class="col-sm-2 col-form-label text-right">Split</label>';
            s += '<div class="col-sm-10">';
            s += '<input xname="split" class="form-control" name="split[]" placeholder="Splited Amount">';
        s += '</div>';
    s += '</div>';
    $("#splited_zone").append(s);
    
} 

fn.app.defer_split.spot.split = function(){
    var amount = parseFloat($("[name=form_splitdefer] [name=amount]").val());
    var total = 0;
    $("[xname=split]").each(function(){
        total += parseFloat($(this).val());	
    });
    
    amount = parseFloat(amount.toFixed(2));
    total = parseFloat(total.toFixed(2));
    
    
    if(amount != total){
        fn.notify.warnbox("จำนวนไม่ตรง","Oops..."+amount+":"+total);
    }else{
        
        $.post("apps/defer_split/xhr/action-split-spot.php",$("form[name=form_splitdefer]").serialize(),function(response){
            if(response.success){
                $("#tblDefer").DataTable().draw();
                $("#tblSplitted").DataTable().draw();
                $("#dialog_split_defer").modal("hide");
            }else{
                fn.notify.warnbox(response.msg,"Oops...");
            }
        },"json");
    }
    return false;
};
