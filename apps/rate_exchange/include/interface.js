var rate_exchange = {
	master: {
		dialog_lookup: fn.noaccess,
		dialog_change_scb: fn.noaccess,
		dialog_change_bbl: fn.noaccess,
		dialog_change_kbank: fn.noaccess,
		dialog_change_scb_paid: fn.noaccess,
		dialog_change_bay: fn.noaccess,
		dialog_change_exchange_sigmargin: fn.noaccess,
		change_scb: fn.noaccess,
		change_bbl: fn.noaccess,
		change_kbank: fn.noaccess,
		change_bay: fn.noaccess,
		change_scb_paid: fn.noaccess,
		change_bbl_paid: fn.noaccess,
		change_exchange_sigmargin: fn.noaccess,
	},
};
$.extend(fn.app, { rate_exchange: rate_exchange });
