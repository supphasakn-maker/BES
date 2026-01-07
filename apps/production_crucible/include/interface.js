var production_crucible = {
	crucible : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_viewcrucible : fn.noaccess,
		dialog_approve : fn.noaccess,
		add : fn.noaccess,
		viewcrucible : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		approve : fn.noaccess,

	}
};
$.extend(fn.app,{production_crucible:production_crucible});
