<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    <title>Financial Report</title>
    <style>
        /* General Body and Container Styles for Better iPad Experience */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            color: #333;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            margin: 0;
            padding: 20px;
            /* Add some padding around the content */
            box-sizing: border-box;
        }

        /* --- Basic Table Styles (from your original CSS, ensure these are present) --- */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            text-align: center;
            /* Adjust as needed */
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
            /* Smaller padding for compact view */
        }

        .table-dark {
            background-color: #343a40 !important;
            color: #fff;
        }

        /* --- Custom Styles for Responsiveness on iPad Pro --- */

        /* Add a container for tables that allows horizontal scrolling */
        .table-responsive-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
            margin-bottom: 20px;
            /* Space between table containers */
        }

        /* Adjust table cell padding and font size for better readability on iPad */
        .table-middle th,
        .table-middle td {
            font-size: 0.95rem;
            /* Slightly larger font for iPad readability */
            padding: 10px 8px;
            /* Slightly more padding */
            white-space: nowrap;
            /* Prevent text wrapping in cells, allowing horizontal scroll if needed */
        }

        /* Specific adjustments for table headers */
        .table-middle thead th {
            white-space: nowrap;
            /* Keep header text on a single line */
            font-weight: 600;
        }

        /* Style for the checkall span (Font Awesome) */
        .far.fa-lg.fa-square {
            font-size: 1.25em;
            /* Make checkbox icon slightly larger */
            vertical-align: middle;
        }

        /* Form inline adjustments for mobile/tablet */
        .form-inline {
            display: flex;
            /* Use flexbox for form elements */
            flex-wrap: wrap;
            /* Allow items to wrap to next line */
            align-items: center;
            margin-bottom: 1rem;
        }

        .form-inline label {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            /* Space below label if it wraps */
        }

        .form-inline .form-control {
            flex: 1;
            /* Allow input to grow */
            min-width: 150px;
            /* Ensure input is not too small */
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            /* Space below input if it wraps */
        }

        .form-inline button {
            margin-bottom: 0.5rem;
            /* Space below button if it wraps */
        }

        /* --- Media Queries for iPad Pro and other devices --- */

        /* For iPad Pro (Portrait: 768px wide) and similar tablets */
        @media (min-width: 768px) and (max-width: 1024px) {
            body {
                padding: 30px;
                /* More padding on larger tablets */
            }

            .col-6 {
                flex: 0 0 50%;
                /* Ensure 2 columns for .col-6 */
                max-width: 50%;
            }
        }

        /* For larger screens (iPad Pro Landscape, Desktops) */
        @media (min-width: 1025px) {
            body {
                padding: 40px 60px;
                /* Even more padding */
            }

            .col-6 {
                flex: 0 0 50%;
                max-width: 50%;
            }
        }

        /* Flexbox for the row containing col-6 tables to ensure proper stacking on smaller screens */
        .row {
            display: flex;
            flex-wrap: wrap;
            /* Allows columns to wrap */
            margin-right: -15px;
            /* Adjust for Bootstrap's default negative margins */
            margin-left: -15px;
        }

        .col-12,
        .col-6 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            /* Adjust for Bootstrap's default padding */
            padding-left: 15px;
            box-sizing: border-box;
            /* Include padding in element's total width */
        }

        /* Adjust col-6 to stack on smaller screens */
        @media (max-width: 767px) {

            /* On screens smaller than 768px (most phones) */
            .col-6 {
                flex: 0 0 100%;
                /* Make them take full width */
                max-width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="table-responsive-container">
        <table id="tblDefer" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
            <thead>
                <tr>
                    <th class="text-center">Date</th>
                    <th class="text-center">Kgs.</th>
                    <th class="text-center">Price Defer Spot</th>
                    <th class="text-center">Price Spot</th>
                    <th class="text-center">Defer</th>
                    <th class="text-center">Supplier</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-12">
            <hr>
            <form name="adding" class="form-inline mr-2" onsubmit="fn.app.defer_cost.defer.add();return false;">
                <label class="mr-sm-2">วันที่</label>
                <input name="date" type="date" class="form-control mr-sm-2" value="<?php echo date("Y-m-d"); ?>">
                <button type="submit" class="btn btn-danger">Match</button>
            </form>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="table-responsive-container">
                <table id="tblPurchase" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center table-dark" colspan="9">Buy Side</th>
                        </tr>
                        <tr>
                            <th class="text-center hidden-xs">
                                <span type="checkall" control="chk_purchase" class="far fa-lg fa-square"></span>
                            </th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Spot</th>
                            <th class="text-center">Pm/dc</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Spot Value</th>
                            <th class="text-center">Discount Value</th>
                            <th class="text-center">Net Spot Value</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-6">
            <div class="table-responsive-container">
                <table id="tblPurchaseDefer" class="table table-sm table-striped table-bordered table-hover table-middle" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center table-dark" colspan="5">Defer Spot</th>
                        </tr>
                        <tr>
                            <th class="text-center hidden-xs">
                                <span type="checkall" control="chk_new" class="far fa-lg fa-square"></span>
                            </th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Supplier</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>