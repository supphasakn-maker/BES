var production_over = {
    adjust: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_remove: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess
    },
    type: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_remove: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess
    },
};
$.extend(fn.app, { production_over: production_over });
