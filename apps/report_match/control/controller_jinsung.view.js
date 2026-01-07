$('input[name=date]').change(function () {
    App.startLoading();
    var date = $(this).val();
    $.post("apps/report_match/xhr/action-load-jinsung.php",
        { date: date },
        function (html) {
            $("#display_area").html(html);
            //fn.app.sigmargin.overview.calculate();
            App.stopLoading();
        }, "html");
});

$('input[name=date]').change();

