<?php
	global $os;
?>
<div class="btn-area btn-group mb-2"></div>
<table id="tblOrganization" class="table table-striped table-bordered table-hover table-middle" width="100%">
    <thead>
        <tr>
            <th class="text-center hidden-xs">
                <span type="checkall" control="chk_organization" class="far fa-lg fa-square"></span>
            </th>
            <th class="text-center"><?php echo $os->tr("organization.organization"); ?></th>
            <th class="text-center hidden-xs"><?php echo $os->tr("organization.email"); ?></th>
            <th class="text-center"><?php echo $os->tr("organization.phone"); ?></th>
            <th class="text-center hidden-xs"><?php echo $os->tr("organization.fax"); ?></th>
            <th class="text-center hidden-xs"><?php echo $os->tr("organization.industry"); ?></th>
            <th class="text-center hidden-xs"><?php echo $os->tr("organization.type"); ?></th>
            <th class="text-center hidden-xs"><?php echo $os->tr("organization.updated"); ?></th>
            <th class="text-center"><?php echo $os->tr("organization.action"); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
