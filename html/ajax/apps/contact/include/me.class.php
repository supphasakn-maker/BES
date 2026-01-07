<?php

class meClass extends widgeteer {

    private $dbc = null;
    private $abox = null;
    private $app = "contact";
    private $view = "organization";
    private $header_meta = array(
        array('organization', "Organization", 'fa fa-lg fa-building'),
        array('customer', "Customer", 'fa fa-lg fa-user'),
        array('supplier', "Supplier", 'fa fa-lg fa-cubes'),
        array('contact', "Contact", 'fa fa-lg fa-credit-card')
    );

    function __construct($dbc, $abox) {
        $this->dbc = $dbc;
        $this->abox = $abox;

        $this->header_meta = array(
            array('organization', $this->abox->tr('main.organization'), 'fa fa-lg fa-building'),
            array('customer', $this->abox->tr('main.customer'), 'fas fa-user'),
            array('supplier', $this->abox->tr('main.supplier'), 'fas fa-cubes'),
            array('contact', $this->abox->tr('main.contact'), 'fas fa-credit-card')
        );
    }

    function setView($view) {
        $this->view = $view;
    }

    function getView() {
        return $this->view;
    }

    function PageBreadcrumb() {
        echo '<h1 class="page-title txt-color-blueDark"> ';
        echo '<i class="fa-fw fa fa-home"></i> Home ';
        echo '<span> > Contact</span> ';
        foreach ($this->header_meta as $header) {
            if ($header[0] == $this->view) {
                echo '<span> > ' . $header[1] . '</span>';
            }
        }
        echo '</h1>';
    }

    function widgetHeader() {
        echo '<ul class="nav nav-tabs">';
        foreach ($this->header_meta as $header) {
            echo '<li' . ($header[0] == $this->view ? ' class="active"' : '') . '>';
            echo '<a href="#apps/contact/index.php?view=' . $header[0] . '">';
            echo '<i class="' . $header[2] . '"></i>';
            echo '<span class="hidden-mobile hidden-tablet"> ' . $header[1] . ' </span>';
            echo '</a>';
            echo '</li>';
        }
        echo '</ul>';
    }

    function widgetBody() {
        $dbc = $this->dbc;
        $abox = $this->abox;
        echo '<div class="tab-content">';
        echo '<div class="tab-pane fade in active" id="' . $this->app . '_' . $this->view . '">';
        switch ($this->view) {
            case "customer":
                include_once "view/page.customer.php";
                break;
            case "organization":
                include_once "view/page.organization.php";
                break;
            case "supplier":
                include_once "view/page.supplier.php";
                break;
            case "contact":
                include_once "view/page.contact.php";
                break;
        }
        echo '</div>';
        echo '</div>';
    }

}

?>