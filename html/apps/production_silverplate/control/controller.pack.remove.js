

fn.app.production_silverplate.pack.remove = function (id) {
	bootbox.confirm("Are you sure to remove?", function (result) {
		if (result) {
			$.post("apps/production_silverplate/xhr/action-remove-pack.php", { id: id }, function (response) {
				$("#tblPacking").DataTable().draw();
			});
		}
	});

};
