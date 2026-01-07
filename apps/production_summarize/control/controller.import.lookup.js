
fn.app.production_summarize.import.dialog_lookup = function(id) {
    var multiple = true
    $.ajax({
        url: "apps/production_summarize/view/dialog.import.scrap.php",
        type: "POST",
        data: {id:id},
        dataType: "html",
        success: function(html){
            $("body").append(html);
            $("#dialog_import_lookup").on("hidden.bs.modal",function(){$(this).remove();});
            $("#dialog_import_lookup").modal('show');
            
            $("#tblImportLookup").data( "selected",[]);
            $('#tblImportLookup').DataTable({
                "bStateSave": true,
                "autoWidth" : true,
                "processing": true,
                "serverSide": true,
                "ajax": "apps/production_summarize/store/store-import_scrap.php",
                "aoColumns": [
                    {"bSortable":false		,"data":"id"		,"sClass":"hidden-xs text-center",	"sWidth": "20px"  },
                    {"bSort":true			,"data":"round" ,"class":"text-center"	},
                    {"bSort":true			,"data":"code"	,"class":"text-center"},
                    {"bSort":true			,"data":"parent"	,"class":"text-center unselectable"},
                    {"bSort":true			,"data":"created","class":"text-center"	},
                    {"bSort":true			,"data":"pack_name"	,"class":"text-center"},
                    {"bSort":true			,"data":"weight_expected"	,"class":"text-right"},
                    {"bSort":true			,"data":"name"	,"class":"text-right"},
                    {"bSortable":false		,"data":"id"		,"sClass":"text-center" , "sWidth": "80px"  },
                ],"order": [[ 2, "desc" ]],
                "createdRow": function ( row, data, index ) {
                    var selected = false,checked = "",s = '';
                    
                    if ( $.inArray(data.DT_RowId, $("#tblImportLookup").data("selected")) !== -1 ) {
                        $(row).addClass('selected');
                        selected = true;
                    }
                    $('td', row).eq(0).html(fn.ui.checkbox("chk_import",data[0],selected,multiple));
                        
                    
                }
            });
            fn.ui.datatable.selectable('#tblImportLookup','chk_import',multiple);
            
        }	
    });
    
};

fn.app.production_summarize.import.dialog_select = function(id) {
    var item_selected = $("#tblImportLookup").data("selected");
    $.ajax({
        url: "apps/production_summarize/view/dialog.scrap.select.php",
        data: {items:item_selected,id:id},
        type: "POST",
        dataType: "html",
        success: function(html){
            $("body").append(html);
            fn.ui.modal.setup({dialog_id : "#dialog_combine_scrap"});
            
            $("input[name=weight_expected]").val($("input[name=total_weight_actual]").val());
            
            $("form[name=form_combinescrap] select[name=pack_name]").unbind().change(function(){
                var caption_pack = $(this).val();
                var value_pack = $(this).find(":selected").attr("data-value");
                var readonly_pack = $(this).find(":selected").attr("data-readonly");
                $("form[name=form_combinescrap] input[name=weight_expected]").val(value_pack);
                if(readonly_pack=="false"){
                    $("form[name=form_combinescrap] input[name=weight_expected]").attr("readonly",false);
                }else{
                    $("form[name=form_combinescrap] input[name=weight_expected]").attr("readonly",true);
                }
            }).change();
        }
    });
};

fn.app.production_summarize.import.select = function(){
    $.post("apps/production_summarize/xhr/action-combine-scrap.php",$("form[name=form_combinescrap]").serialize(),function(response){
        if(response.success){
            $("#tblScrap").DataTable().draw();
            $("#dialog_combine_scrap").modal("hide");
        }else{
            fn.notify.warnbox(response.msg,"Oops...");
        }
    },"json");
    return false;
};