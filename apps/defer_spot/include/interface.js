var defer_spot = {
    defer: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_remove: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess
    },
};
$.extend(fn.app, { defer_spot: defer_spot });
