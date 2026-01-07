<div class="card p-4 py-5">
    <div class="btn-area-transfer mb-2"></div>
    <table id="tblQuickOrder" class="datatable table table-striped table-sm table-bordered nowrap">
        <thead style="background: linear-gradient(135deg, #00204E 0%, #003875 50%, #00204E 100%);">
            <tr>
                <th class="text-center text-white font-weight-bold"><?php echo date('Y-m-d'); ?></th>
                <th class="text-center text-white font-weight-bold">PO</th>
                <th class="text-left text-white font-weight-bold">CUSTOMER</th>
                <th class="text-center text-white font-weight-bold">BARS / BOX</th>
                <th class="text-center text-white font-weight-bold">PRICE</th>
                <th class="text-center text-white font-weight-bold">NET</th>
                <th class="text-center text-white font-weight-bold">STATUS</th>
                <th class="text-center text-white font-weight-bold">PLATFORM</th>
                <th class="text-center text-white font-weight-bold">SALES</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="3">ยอดรวม</th>
                <th class="text-center" id="tAmount">0</th>
                <th class="text-center" id="tPrice">0.00</th>
                <th class="text-center" id="tValue">0.00</th>
                <th class="text-center" colspan="3"></th>
            </tr>
            <tr>
                <th class="text-right" colspan="3">จำนวนกล่องทั้งหมด</th>
                <th class="text-center" id="tBox">0</th>
                <th colspan="5"></th>
            </tr>
        </tfoot>
    </table>
</div>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 32, 78, 0.15);
        overflow: hidden;
        margin: 0 auto;
    }

    #tblQuickOrder tfoot tr {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
        font-weight: bold;
    }

    #tblQuickOrder tfoot th {
        border-top: 2px solid #00204E;
        color: #00204E;
    }

    .badge-info {
        background-color: #17a2b8;
        color: white;
        font-size: 0.75em;
        padding: 0.25em 0.5em;
        border-radius: 0.25rem;
    }

    #tblQuickOrder th:nth-child(4),
    #tblQuickOrder td:nth-child(4) {
        width: 80px !important;
        max-width: 80px !important;
        min-width: 60px !important;
        text-align: center !important;
    }

    #tblQuickOrder .btn i {
        font-size: 0.90em !important;
    }

    #tblQuickOrder .btn-xs {
        padding: 0.125rem 0.25rem !important;
        font-size: 0.99rem !important;
        line-height: 1.2 !important;
        border-radius: 0.15rem !important;
    }

    #tblQuickOrder th:nth-child(1),
    #tblQuickOrder td:nth-child(1) {
        width: 100px !important;
        /* Date/Time */
    }

    #tblQuickOrder th:nth-child(2),
    #tblQuickOrder td:nth-child(2) {
        width: 120px !important;
        /* PO */
    }

    #tblQuickOrder th:nth-child(5),
    #tblQuickOrder td:nth-child(5) {
        width: 100px !important;
        /* PRICE */
    }

    #tblQuickOrder th:nth-child(6),
    #tblQuickOrder td:nth-child(6) {
        width: 120px !important;
        /* NET */
    }

    #tblQuickOrder th:nth-child(7),
    #tblQuickOrder td:nth-child(7) {
        width: 160px !important;
        /* STATUS */
    }

    #tblQuickOrder th:nth-child(8),
    #tblQuickOrder td:nth-child(8) {
        width: 100px !important;
        /* PLATFORM */
    }

    #tblQuickOrder th:nth-child(9),
    #tblQuickOrder td:nth-child(9) {
        width: 80px !important;
        /* SALES */
    }
</style>