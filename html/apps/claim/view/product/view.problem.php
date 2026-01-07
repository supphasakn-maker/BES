<div class="row">
    <div class="col-12">
        <table class="table table-bordered table-form">
            <tbody>
                <tr>
                    <td><label>แนวทางการแก้ไขปัญหา</label></td>
                    <td>
                        <?php
                        $ui_form->EchoItem(array(
                            "name" => "solutions",
                            "type" => "textarea",
                            "caption" => "แนวทางการแก้ไขปัญหา",
                            "placeholder" => "แนวทางการแก้ไขปัญหา",
                            "value" => $claim['solutions'],
                            "rows" => 6,
                        ));
                        ?>
                    </td>
                <tr>
            </tbody>
        </table>
    </div>
</div>