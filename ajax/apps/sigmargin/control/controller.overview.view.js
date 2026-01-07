
var key_date = $("#date").val();

$('#date').change(function() {
    var date = $(this).val();
    key_date = date;
    console.log("key_date -> "+key_date);
    window.location.href = "#apps/sigmargin/index.php?view=overview&date="+key_date;
});

function load_data(key_date){
    $.ajax({
        url: "apps/sigmargin/store/store-overview.php",
        method: "POST",
        data: {key_date},
        success: function(data){
            var res_data = JSON.parse(data);
            var html = "";
            console.log("res_data => ",res_data)
            for(var i=0; i < res_data.length; i++){
                var bsr_amount = res_data[i].bsr_amount == null ? 0  : parseFloat(res_data[i].bsr_amount);
                var bsp_amount_usd =  res_data[i].bsp_amount_usd == null ? 0 : parseInt(res_data[i].bsp_amount_usd);
                var bsr_rate_pmdc = res_data[i].bsr_rate_pmdc == null ? 0 : parseFloat(res_data[i].bsr_rate_pmdc);
                console.log("res_data => ",res_data[i]);
                html += "<tr>"
                html += "<td align='center' style='width: 150px;'>"+moment(res_data[i].bsp_date).format("DD-MM-YYYY")+"</td>"  
                html += "<td>"+ bsr_amount +"</td>" 
                html += "<td>"+bsp_amount_usd+"</td>" 
                html += "<td>"+ bsr_rate_pmdc*0.2+"</td>" 
                html += "<td>"+bsr_rate_pmdc+"</td>" 
                html += "<td>"+bsr_rate_pmdc+"</td>" 
                html += "<td>"+ (parseFloat(res_data[i].bsr_amount)-parseFloat(res_data[i].bsp_amount_usd))+"</td>" 
                html += "<td width=70> - </td>" 
                html += "<td width=70> - </td>" 
                html += "<td width=70> - </td>"  
                html += "<td width=70> - </td>"
                html += "</tr>"
                
            }
            $("#silver_table").html("");
            $('#silver_table').append(html);
        }
    });
}


$('#dynamic_input').on('keyup',function(){
    var input_value = $('#dynamic_input').val();
    $('#dynamic_input_1').text(input_value);
    $('#dynamic_input_2').text(input_value);
})