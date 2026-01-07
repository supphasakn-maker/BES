var holiday_announce = {
    holiday: {
        dialog_lookup: fn.noaccess,
        dialog: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_remove: fn.noaccess,
        dialog_approve: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess,
        recalculate: fn.noaccess,
        recal: fn.noaccess
    },
};
$.extend(fn.app, { holiday_announce: holiday_announce });