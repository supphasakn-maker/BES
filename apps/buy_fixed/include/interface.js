var buy_fixed = {
    buy: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_remove: fn.noaccess,
        dialog_split: fn.noaccess,
        dialog_combine: fn.noaccess,
        dialog_purchase: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess,
        split: fn.noaccess,
        combine: fn.noaccess,
        purchase: fn.noaccess
    },
    sell: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_edit_usd: fn.noaccess,
        dialog_remove: fn.noaccess,
        dialog_purchase: fn.noaccess,
        dialog_split: fn.noaccess,
        dialog_combine: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess,
        split: fn.noaccess,
        combine: fn.noaccess,
        purchase: fn.noaccess
    },
};
$.extend(fn.app, { buy_fixed: buy_fixed });
