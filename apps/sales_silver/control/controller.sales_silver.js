$("form[name=order] input[name=amount],form[name=order] input[name=price]").change(function(){
    let amount = parseFloat($("form[name=order] input[name=amount]").val());
    let price = parseFloat($("form[name=order] input[name=price]").val());
    let tax = $("form[name=order] select[name=vat_percent]").val()=="2"?7:0;
    let total = amount*price;
    
    let net = total * (1 + (tax/100));
    $("form[name=order] input[name=net]").val(net);
});

$("form[name=customer] select[name=customer_select]").change(function(){
    let customer_id = $(this).val();
    if(customer_id != ""){
        $.post('apps/sales_silver/xhr/action-load-customer.php',{id:customer_id},function(customer){
            for(i in customer){
                $("form[name=customer] input[name="+i+"]").val(customer[i]);
                $("form[name=customer] textarea[name="+i+"]").val(customer[i]);
                $("form[name=customer] select[name="+i+"]").val(customer[i]);
            };
            $("form[name=order] input[name=contact]").val(customer['contact']);
            $("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);

            if(customer['new_cus']="0"){	
                $("form[name=order] input[name=new_cus]").val(customer['new_cus']);
            }else if(customer['new_cus']="1"){
                $("form[name=order] input[name=new_cus]").val(customer['new_cus']);
            }
            
            if(customer['default_vat_type']!=null){						
                $("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);
            }else{
                $("form[name=order] select[name=vat_type]").val(0);
            }
            
            if(customer['remark']!=""){
                $("#info_memo").html('<div class="alert alert-danger" role="alert"><strong>คำเตือน</strong> '+customer['remark']+'</div>');
            }else{
                $("#info_memo").html("");
            }
            
        },"json");
    }else{
        $("form[name=customer] input").val("-");
    }
    $("#tblDailyTable").DataTable().draw();
    $("#tblDailyRemain").DataTable().draw();
    
    
    
});


//ทำรายการตอนเปลี่ยน Customer ID
$("form[name=order] select[name=customer_id]").change(function(){
    
    $("#tblDailyTable").DataTable().draw();
    let customer_id = $(this).val();
    if(customer_id != ""){
        $.post('apps/sales_silver/xhr/action-load-customer.php',{id:customer_id},function(customer){
            $("form[name=order] input[name=contact]").val(customer['contact']);
            $("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);
            
            if(customer['default_vat_type']!=null){						
                $("form[name=order] select[name=vat_type]").val(customer['default_vat_type']);
            
            }else{
                $("form[name=order] select[name=vat_type]").val(0);
            }
            
            if(customer['remark']!=""){
                $("#info_memo").html('<div class="alert alert-danger" role="alert"><strong>ตำเตือน</strong> '+customer['remark']+'</div>');
            }else{
                $("#info_memo").html("");
            }
            
        },"json");
    }else{
        $("form[name=customer] input").val("-");
    }
});

$("input[name=delivery_lock]").change(function(){
    $("input[name=delivery_date]").prop('readOnly',$(this).prop('checked'));
});



fn.app.sales_silver.reset = function(){
    $('form[name=quick_order]')[0].reset();
    $('form[name=order]')[0].reset();
    $("#info_memo").html("");
    $('.select2').val("").trigger('change');
    return false;
};

$("form[name=form_addquick_order] select[name=customer_id]").change(function(){
    let customer_id = $(this).val();
    if(customer_id != ""){
        
        $.post('apps/sales_silver/xhr/action-load-customer.php',{id:customer_id},function(customer){
            $("form[name=form_addquick_order] select[name=product_id]").val(customer['product_id']);
            if(customer['default_vat_type']!=null){
                $("form[name=form_addquick_order] select[name=vat_type]").val(customer['default_vat_type']);
            }else{
                $("form[name=form_addquick_order] select[name=vat_type]").val(0);
            }
            
        },"json");
    }
});
            