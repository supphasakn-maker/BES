var database = {
	company : {
		bank : {
			dialog_lookup : fn.noaccess,
			dialog_add : fn.noaccess,
			dialog_edit : fn.noaccess,
			dialog_remove : fn.noaccess,
			add : fn.noaccess,
			edit : fn.noaccess,
			remove : fn.noaccess
		},
		currency : {
			dialog_lookup : fn.noaccess,
			dialog_add : fn.noaccess,
			dialog_edit : fn.noaccess,
			dialog_remove : fn.noaccess,
			add : fn.noaccess,
			edit : fn.noaccess,
			remove : fn.noaccess
		},
		product : {
			dialog_lookup : fn.noaccess,
			dialog_add : fn.noaccess,
			dialog_edit : fn.noaccess,
			dialog_remove : fn.noaccess,
			add : fn.noaccess,
			edit : fn.noaccess,
			remove : fn.noaccess
		},
		payitem : {
			dialog_lookup : fn.noaccess,
			dialog_add : fn.noaccess,
			dialog_edit : fn.noaccess,
			dialog_remove : fn.noaccess,
			add : fn.noaccess,
			edit : fn.noaccess,
			remove : fn.noaccess
		}
	},
	address : {
		initial : fn.noaccess,
		load_country : fn.noaccess,
		load_city : fn.noaccess,
		load_district : fn.noaccess,
		load_subdistrict : fn.noaccess
	},
	country : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	city : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	district : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	subdistrict : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	industry : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	},
	unit : {
		dialog_lookup : fn.noaccess,
		dialog_add : fn.noaccess,
		dialog_edit : fn.noaccess,
		dialog_remove : fn.noaccess,
		add : fn.noaccess,
		edit : fn.noaccess,
		remove : fn.noaccess
	}
};

$.extend(fn.app,{database:database});