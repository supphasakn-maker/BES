	fn.app.datapanel.master.dialog_change_pmdc = function(id) {
		$.ajax({
			url: "apps/datapanel/view/dialog.master.change_pmdc.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_change_pmdc_master"});
			}
		});
	};

	fn.app.datapanel.master.change_pmdc = function(){
		$.post("apps/datapanel/xhr/action-change_pmdc-master.php",$("form[name=form_change_pmdcmaster]").serialize(),function(response){
			if(response.success){
				$("#tblMaster").DataTable().draw();
				$("#dialog_change_pmdc_master").modal("hide");
				fn.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
