fn.app.announce.announce_silver.recal = function () {
    var sell1 = $('form[name=rate] input[name=sell]').val();
    var buy1 = $('form[name=rate] input[name=buy]').val();

    function cleanNumber(val) {
        if (!val) return 0;
        val = val.toString()
            .replace(/[\u0E3F฿]/g, '')
            .replace(/,/g, '')
            .replace(/\s+/g, '');
        var n = parseFloat(val);
        return isNaN(n) ? 0 : n;
    }

    var sell = cleanNumber(sell1);
    var buy1num = cleanNumber(buy1);

    var buy = sell - buy1num;

    $('form[name=rate] input[name=sell]').val(fn.ui.numberic.format(sell, 2));
    $('form[name=rate] input[name=buy]').val(fn.ui.numberic.format(buy, 2));
};

fn.app.announce.announce_silver.recal();
