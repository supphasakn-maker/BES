var tracking_bwd = {
    order: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_edit_tracking: fn.noaccess,
        dialog_remove: fn.noaccess,
        dialog_split: fn.noaccess,
        dialog_postpone: fn.noaccess,

        add: fn.noaccess,
        edit: fn.noaccess,
        edit_tracking: fn.noaccess,
        remove: fn.noaccess,
        split: fn.noaccess,
        postpone: fn.noaccess,
        date_update: fn.noaccess,
        date_previous: fn.noaccess,
        date_next: fn.noaccess
    },
    order_bar: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_edit_tracking: fn.noaccess,
        dialog_remove: fn.noaccess,
        dialog_split: fn.noaccess,
        dialog_postpone: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        edit_tracking: fn.noaccess,
        remove: fn.noaccess,
        split: fn.noaccess,
        postpone: fn.noaccess,
        date_update: fn.noaccess,
        date_previous: fn.noaccess,
        date_next: fn.noaccess
    },
};
$.extend(fn.app, { tracking_bwd: tracking_bwd });
