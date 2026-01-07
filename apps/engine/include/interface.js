var engine = {
	file : {
		dialog_file : fn.noaccess,
		upload : fn.noaccess,
		clear : fn.noaccess
	}
};

$.extend(fn.app,{engine:engine});