	fn.app.datapanel.master.dialog_change_spot = function(id) {
		$.ajax({
			url: "apps/datapanel/view/dialog.master.change_spot.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_change_spot_master"});
			}
		});
	};

	fn.app.datapanel.master.change_spot = function(){
		$.post("apps/datapanel/xhr/action-change_spot-master.php",$("form[name=form_change_spotmaster]").serialize(),function(response){
			if(response.success){
				$("#tblMaster").DataTable().draw();
				$("#dialog_change_spot_master").modal("hide");
				fn.reload();
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
