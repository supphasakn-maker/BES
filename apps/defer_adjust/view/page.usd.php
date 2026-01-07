<?php
global $ui_form, $os;
?>
<div class="row">
    <div class="col-3">
        <form name="form_addusd" class="mb-3" onsubmit="fn.app.defer_adjust.usd.add();return false;">

            <table class="table table-bordered table-form">
                <tbody>
                    <tr>
                        <td><label>BANK</label></td>
                        <td>
                            <?php
                            $ui_form->EchoItem(array(
                                "name" => "bank",
                                "type" => "comboboxdb",
                                "source" => array(
                                    "table" => " bs_banks",
                                    "name" => "name",
                                    "value" => "id",
                                    "where"  => "id in(3,9,7)",
                                )
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label>DATE</label></td>
                        <td>
                            <?php
                            $ui_form->EchoItem(array(
                                "type" => "date",
                                "name" => "date",
                                "caption" => "Date",
                                "class" => "pl-3",
                                "placeholder" => "Purchase Date",
                                "value" => date("Y-m-d")
                            ));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><label>USD</label></td>
                        <td class="pl-2">
                            <?php
                            $ui_form->EchoItem(array(
                                "name" => "usd",
                                "caption" => "VALUE USD",
                                "placeholder" => "0.0000"
                            ));
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <td><label>COMMENT</label></td>
                        <td class="pl-2">
                            <?php
                            $ui_form->EchoItem(array(
                                "name" => "comment",
                                "caption" => "COMMENT",
                                "placeholder" => "COMMENT"
                            ));
                            ?>
                        </td>
                </tbody>
            </table>
            <button class="btn btn-primary" type="submit">ทำรายการ</button>
        </form>
    </div>
    <div class="col-9">
        <table id="tblUSD" class="table table-striped table-bordered table-hover table-middle" width="100%">
            <thead class="bg-dark">
                <tr>
                    <th class="text-center text-white font-weight">DATE</th>
                    <th class="text-center text-white font-weight">BANK</th>
                    <th class="text-center text-white font-weight">USD</th>
                    <th class="text-center text-white font-weight">COMMENT</th>
                    <th class="text-center text-white font-weight">ACTION</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>