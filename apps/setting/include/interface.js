var setting = {
	profile : {
		address : {
			initial : fn.noaccess,
			load_country : fn.noaccess,
			load_city : fn.noaccess,
			load_district : fn.noaccess,
			load_subdistrict : fn.noaccess
		},
		dialog_edit : fn.noaccess,
		edit : fn.noaccess,
		dialog_language : fn.noaccess,
		change_language : fn.noaccess,
		mail : {
			dialog_setting : fn.noaccess,
			save_setting : fn.noaccess,
			dialog_sendmail : fn.noaccess,
			sendmail : fn.noaccess,
			testsever : fn.noaccess
		},
		change_avatar : fn.noaccess,
		detail : {
			dialog_edit : fn.noaccess,
			edit : fn.noaccess
		}
	},
	company : {
		save_core  : fn.noaccess,
		dialog_edit : fn.noaccess,
		save : fn.noaccess,
		change_icon : fn.noaccess,
		remove_icon : fn.noaccess
	},
	sales : fn.noaccess,
	overview : {
		
	},
	system : {
		save_document : fn.noaccess,
		save_general : fn.noaccess
	}
	
};

$.extend(fn.app,{setting:setting});