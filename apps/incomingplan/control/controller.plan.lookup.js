	fn.app.incomingplan.plan.dialog_lookup = function(id) {
		$.ajax({
			url: "apps/incomingplan/view/dialog.plan.lookup.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_lookup_plan"});
			}
		});
	};

	fn.app.incomingplan.plan.lookup = function(){
		$.post("apps/incomingplan/xhr/action-lookup-plan.php",$("form[name=form_lookupplan]").serialize(),function(response){
			if(response.success){
				$("#tblPlan").DataTable().draw();
				$("#dialog_lookup_plan").modal("hide");
			}else{
				fn.notify.warnbox(response.msg,"Oops...");
			}
		},"json");
		return false;
	};
