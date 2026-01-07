var task = {
	pool : {
		dialog_lookup : fn.noaccess,
		dialog_accept : fn.noaccess,
		accept : fn.noaccess
	},
	pending : {
		dialog_lookup : fn.noaccess,
		dialog_comment : fn.noaccess,
		dialog_change_status : fn.noaccess,
		dialog_submit : fn.noaccess,
		comment : fn.noaccess,
		change_status : fn.noaccess,
		submit : fn.noaccess
	},
	done : {
		dialog_lookup : fn.noaccess,
		dialog_view : fn.noaccess,
		view : fn.noaccess
	},
};
$.extend(fn.app,{task:task});
