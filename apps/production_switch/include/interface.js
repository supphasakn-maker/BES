var production_switch = {
    switch: {
        dialog_lookup: fn.noaccess,
        dialog_add: fn.noaccess,
        dialog_prepare: fn.noaccess,
        dialog_edit: fn.noaccess,
        dialog_remove: fn.noaccess,
        dialog_approve: fn.noaccess,
        dialog_turn: fn.noaccess,
        add: fn.noaccess,
        edit: fn.noaccess,
        remove: fn.noaccess,
        prepare: fn.noaccess,
        turn: fn.noaccess,
        approve: fn.noaccess,
        select: fn.noaccess,
    }
};
$.extend(fn.app, { production_switch: production_switch });
