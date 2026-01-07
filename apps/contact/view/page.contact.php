<?php
	global $os;
?>
<div class="btn-area btn-group mb-2"></div>
<table id="tblContact" class="table table-striped table-bordered table-hover table-middle" width="100%">
    <thead>
        <tr>
            <th class="text-center hidden-xs">
                <span type="checkall" control="chk_contact" class="far fa-lg fa-square"></span>
            </th>
            <th class="text-center" width="40"><?php echo $os->tr("contact.avatar"); ?></th>
            <th class="text-center"><?php echo $os->tr("contact.name"); ?></th>
            <th class="text-center"><?php echo $os->tr("contact.date_of_birth"); ?></th>
            <th class="text-center"><?php echo $os->tr("contact.gender"); ?></th>
            <th class="text-center" width="180"><?php echo $os->tr("contact.contact"); ?></th>
            <th class="text-center"><?php echo $os->tr("contact.citizen_id"); ?></th>
            <th class="text-center"><?php echo $os->tr("contact.action"); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
