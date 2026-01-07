<?php

global $ui_form, $os;
?>

<style>
    :root {
        --primary-color: #00204E;
        --primary-light: #1a3a6b;
        --primary-dark: #001836;
        --accent-color: #4a90e2;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --light-bg: #f8f9fa;
        --white: #ffffff;
        --text-dark: #2c3e50;
        --border-color: #e9ecef;
        --shadow: 0 0.125rem 0.25rem rgba(0, 32, 78, 0.1);
        --shadow-lg: 0 0.5rem 1rem rgba(0, 32, 78, 0.15);
    }

    /* Global Styling */
    body {
        background-color: var(--white);
        color: var(--text-dark);
    }

    /* Container Styling */
    .container-fluid {
        background-color: var(--white);
        min-height: 100vh;
        padding-top: 1rem;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        padding: 1.5rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 1rem 1rem;
        box-shadow: var(--shadow-lg);
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Table Form Styling */
    .table-form {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .table-form td {
        vertical-align: middle;
        border-color: var(--border-color);
        padding: 1rem;
    }

    .table-form td:first-child {
        background: linear-gradient(135deg, var(--light-bg) 0%, #e3f2fd 100%);
        font-weight: 600;
        color: var(--primary-color);
        width: 35%;
        border-right: 2px solid var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
    }

    .table-form tr {
        transition: all 0.2s ease;
    }

    .table-form tr:hover {
        background-color: rgba(0, 32, 78, 0.02);
        transform: scale(1.001);
    }

    /* Form Controls */
    .form-control {
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: var(--white);
        min-height: 48px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 32, 78, 0.25);
        background: var(--light-bg);
    }

    .form-control:hover {
        border-color: var(--primary-light);
    }

    /* Input Group */
    .input-group-text {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        border: 2px solid var(--primary-color);
        font-weight: 600;
        border-radius: 0.5rem 0 0 0.5rem;
        padding: 0.75rem 1rem;
        min-height: 48px;
        display: flex;
        align-items: center;
    }

    .input-group .form-control {
        border-left: none;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    /* Submit Button */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        border: none;
        border-radius: 0.5rem;
        padding: 0.875rem 2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: var(--shadow);
        min-height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* Filter Section */
    .btn-area {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        overflow: visible;
    }

    .form-inline {
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
    }

    .form-inline label {
        color: var(--primary-color);
        font-weight: 600;
        margin: 0;
        white-space: nowrap;
    }

    .form-inline .form-control {
        margin: 0;
        min-width: 150px;
    }

    .form-inline .btn {
        white-space: nowrap;
    }

    /* Table Container */
    .table-container {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }

    /* Table Styling */
    .table {
        margin-bottom: 0;
        border-radius: 1rem;
        overflow: hidden;
    }

    .table thead.bg-dark th {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%) !important;
        color: white !important;
        border: none !important;
        padding: 1rem 0.75rem !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        font-size: 0.875rem !important;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background: rgba(0, 32, 78, 0.02) !important;
        transform: scale(1.001);
    }

    .table tbody tr:nth-child(even) {
        background-color: rgba(0, 32, 78, 0.01);
    }

    .table tbody td {
        padding: 0.875rem 0.75rem !important;
        border-color: var(--border-color) !important;
        vertical-align: middle;
    }

    /* DataTable Styling */
    .dataTables_wrapper {
        padding: 1.5rem;
    }

    .dataTables_length,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate {
        margin: 0.75rem 0;
    }

    .dataTables_length select,
    .dataTables_filter input {
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        margin: 0 0.5rem;
        font-size: 0.875rem;
        min-height: 40px;
        transition: border-color 0.3s ease;
    }

    .dataTables_filter input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.15rem rgba(0, 32, 78, 0.25);
    }

    .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem !important;
        margin: 0 0.125rem !important;
        border-radius: 0.375rem !important;
        border: 1px solid var(--border-color) !important;
        background: var(--white) !important;
        color: var(--primary-color) !important;
        min-height: 40px !important;
        transition: all 0.3s ease !important;
    }

    .dataTables_paginate .paginate_button:hover {
        background: var(--primary-color) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
        transform: translateY(-1px) !important;
    }

    .dataTables_paginate .paginate_button.current {
        background: var(--primary-color) !important;
        color: white !important;
        border-color: var(--primary-color) !important;
        box-shadow: var(--shadow) !important;
    }

    /* Currency Display */
    .currency-display {
        font-weight: 600;
        color: var(--primary-color);
    }

    /* Action Buttons */
    .action-btn {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
        border: none;
        margin: 0 0.125rem;
        min-height: 32px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .action-btn-edit {
        background: linear-gradient(135deg, var(--accent-color) 0%, #357abd 100%);
        color: white;
    }

    .action-btn-delete {
        background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
        color: white;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow);
    }

    /* Loading States */
    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .btn-loading {
        position: relative;
        color: transparent !important;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-up {
        animation: slideInUp 0.3s ease-out;
    }

    /* Row Alignment */
    .row.d-md-flex {
        align-items: stretch;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0.5rem;
        }

        .table-form td {
            padding: 0.75rem;
        }

        .table-form td:first-child {
            width: 100%;
            display: block;
            border-right: none;
            border-bottom: 1px solid var(--primary-color);
            text-align: center;
            padding: 0.5rem;
            font-size: 0.8rem;
        }

        .table-form td:not(:first-child) {
            display: block;
            width: 100%;
            border-left: none;
            padding: 0.5rem;
        }

        .form-inline {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .form-inline .form-control {
            margin: 0;
            width: 100%;
            min-width: auto;
        }

        .form-inline label {
            margin-bottom: 0.25rem;
        }

        .btn-area {
            padding: 1rem;
        }

        .table-responsive {
            border-radius: 1rem;
            overflow: hidden;
        }

        .table {
            font-size: 0.8rem;
        }

        .table th,
        .table td {
            padding: 0.5rem 0.25rem !important;
        }

        .dataTables_wrapper {
            padding: 1rem;
        }

        .dataTables_length,
        .dataTables_filter {
            text-align: center;
            margin-bottom: 1rem;
        }

        .dataTables_length select,
        .dataTables_filter input {
            width: 100%;
            max-width: 200px;
            margin: 0.25rem 0;
        }

        .dataTables_info {
            text-align: center;
            font-size: 0.75rem;
            margin: 0.75rem 0;
        }

        .dataTables_paginate {
            text-align: center;
        }

        .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.375rem !important;
            margin: 0.125rem !important;
            font-size: 0.75rem !important;
            min-height: 36px !important;
        }
    }

    @media (max-width: 480px) {

        .table-form,
        .btn-area,
        .table-container {
            border-radius: 0.5rem;
        }

        .table {
            font-size: 0.7rem;
        }

        .table th,
        .table td {
            padding: 0.375rem 0.125rem !important;
        }

        .dataTables_paginate .paginate_button {
            padding: 0.375rem 0.25rem !important;
            font-size: 0.7rem !important;
            min-height: 32px !important;
        }
    }

    /* Touch-friendly improvements */
    @media (pointer: coarse) {
        .form-control {
            min-height: 50px;
            font-size: 16px;
        }

        .btn-primary {
            min-height: 56px;
            padding: 1rem 1.5rem;
        }

        .dataTables_paginate .paginate_button {
            min-height: 44px !important;
            padding: 0.75rem 1rem !important;
        }
    }

    /* Success/Error States */
    .input-success {
        border-color: var(--success-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    }

    .input-error {
        border-color: var(--danger-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Card styling for better mobile experience */
    @media (max-width: 768px) {

        .col-md-4,
        .col-md-8 {
            padding: 0.5rem;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }
    }
</style>
<div class="container-fluid mt-3">
    <div class="row d-md-flex flex-md-row flex-column">
        <div class="col-md-4 col-12 mb-md-0 mb-3">
            <form name="holiday" method="post" class="mb-3 animate-slide-up" onsubmit="fn.app.holiday_announce.holiday.add();return false;">
                <table class="table table-bordered table-form">
                    <tbody>
                        <tr>
                            <td><label>วันที่หยุด</label></td>
                            <td colspan="3">
                                <?php
                                $today = date("Y-m-d");
                                echo '<input type="date" name="PublicHoliday" class="form-control pl-3" placeholder="Holiday Date" value="' . $today . '">';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><label>รายละเอียด</label></td>
                            <td colspan="3"><input type="text" name="Descripiton" class="form-control" placeholder="รายละเอียด"></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-save mr-2"></i>
                    ทำรายการ
                </button>
            </form>
        </div>
        <div class="col-md-8 col-12">
            <div class="mb-3 animate-slide-up">
                <div class="btn-area btn-group">
                    <form name="filter" class="form-inline" onsubmit="return false;">
                        <label class="mr-sm-2"><i class="fas fa-calendar-alt mr-1"></i>Year</label>
                        <?php
                        $current_year = date("Y");
                        $next_year = $current_year + 1;
                        echo '<select name="year" class="form-control mr-sm-2">';
                        echo '<option value="' . $current_year . '">' . $current_year . '</option>';
                        echo '<option value="' . $next_year . '">' . $next_year . '</option>';
                        echo '</select>';
                        ?>
                        <button type="button" class="btn btn-primary mr-2" onclick='$("#tblSilver").DataTable().draw();'>
                            <i class="fas fa-search mr-2"></i>
                            Lookup
                        </button>
                    </form>
                </div>
            </div>
            <div class="table-container animate-slide-up">
                <div class="table-responsive">
                    <table id="tblSilver" class="table table-striped table-bordered table-hover table-middle" width="100%">
                        <thead class="bg-dark">
                            <tr>
                                <th class="text-center text-white">
                                    DATE TIME
                                </th>
                                <th class="text-center text-white">
                                    DATE
                                </th>
                                <th class="text-center text-white">

                                    DETAIL
                                </th>
                                <th class="text-center text-white">

                                    ACTION
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>