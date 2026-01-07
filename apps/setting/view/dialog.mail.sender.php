<div class="modal fade" id="dialog_sendmail" data-backdrop="static">
  	<div class="modal-dialog">
		<form id="form_sendmail" class="form-horizontal" role="form" onsubmit="fn.app.accctrl.group.add();return false;">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Send Mail</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Header</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="txtHeader" name="txtHeader" placeholder="Title">
					</div>
				</div>
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Body</label>
					<div class="col-sm-10">
						<textarea name="txtBody" class="form-control"></textarea>
					</div>
				</div>
		    </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
	  	</div><!-- /.modal-content -->
		</form>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
