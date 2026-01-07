fn.app.profit_loss.daily.search = function () {
    $.post("apps/profit_loss/xhr/action-search-daily.php", $("form[name=filter]").serialize(), function (response) {
        $("#output").html(response);
    }, "html");
    return false;
};

// เรียกฟังก์ชัน search() ครั้งแรกเมื่อหน้าโหลด เพื่อแสดงข้อมูลเริ่มต้น (ถ้าต้องการ)
$(document).ready(function () {
    fn.app.profit_loss.daily.search();
});