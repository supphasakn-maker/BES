var accctrl = {
	address : {
		initial : fn.noaccess,
		load_country : fn.noaccess,
		load_city : fn.noaccess,
		load_district : fn.noaccess,
		load_subdistrict : fn.noaccess
	},
	group : {
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		dialog_permission : fn.noaccess,
		dialog_role : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		save_permission : fn.noaccess,
		save_role : fn.noaccess
	},
	user : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	account : {
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess,
		select_organization : fn.noaccess
	}
};

$.extend(fn.app,{accctrl:accctrl});