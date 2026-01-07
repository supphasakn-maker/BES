var production_round = {
	round : {
		dialog_split : fn.noaccess,
		append_split : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_approve : fn.noaccess,
		remove : fn.noaccess,
		split : fn.noaccess,
		approve : fn.noaccess
	}
};
$.extend(fn.app,{production_round:production_round});