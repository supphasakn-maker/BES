<div class="modal fade" id="dialog_language" data-backdrop="static">
  	<div class="modal-dialog">
		<form id="form_language" class="form-horizontal" role="form" onsubmit="fn.app.setting.profile.change_language();return false;">
    	<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4>Change Language</h4>
      		</div>
		    <div class="modal-body">
				<div class="form-group">
					<label for="txtName" class="col-sm-2 control-label">Lanugage</label>
					<div class="col-sm-10">
						<select name="cbbLang" class="form-control">
							<option value="en">English</option>
							<option value="th">ภาษาไทย</option>
						</select>
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
