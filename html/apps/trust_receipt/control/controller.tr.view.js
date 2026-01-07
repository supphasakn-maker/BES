


	fn.app.trust_receipt.tr.load = function() {
		$.post("apps/trust_receipt/xhr/action-load-table.php",$("form[name=filter]").serialize(),function(response){
			$("#tr_zone").html(response)
			/*
			$("#tblTRMain").DataTable({
				//responsive: true,
				"bStateSave": true,
				"autoWidth" : true
			});
			*/
		},"html");
	}
	
	fn.app.trust_receipt.tr.dialog_interest = function(id) {
		$.ajax({
			url: "apps/trust_receipt/view/dialog.tr.interest.php",
			data: {id:id},
			type: "POST",
			dataType: "html",
			success: function(html){
				$("body").append(html);
				fn.ui.modal.setup({dialog_id : "#dialog_interest"});
				
				
			}
		});
	};