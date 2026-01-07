fn.app.announce.announce_silver.recalculate = function () {
    const $f = $('form[name=rate]');

    function toNumber(v) {
        if (v == null) return 0;
        v = String(v).trim();

        let negative = false;
        if (/^\(.*\)$/.test(v)) {
            negative = true;
            v = v.slice(1, -1);
        }

        v = v
            .replace(/[\u0E3F฿]/g, '')
            .replace(/\u2212/g, '-')
            .replace(/[–—]/g, '-')
            .replace(/,/g, '')
            .replace(/\s+/g, '');
        const n = parseFloat(v);
        if (isNaN(n)) return 0;
        return negative ? -n : n;
    }

    function writeNumber(name, val, decimals = 2) {
        if (window.fn && fn.ui && fn.ui.numberic && typeof fn.ui.numberic.format === 'function') {
            $f.find(`input[name="${name}"]`).val(fn.ui.numberic.format(val, decimals));
        } else {
            $f.find(`input[name="${name}"]`).val(Number(val).toFixed(decimals));
        }
    }


    const spot = toNumber($f.find('input[name="rate_spot"]').val());
    const exchange = toNumber($f.find('input[name="rate_exchange"]').val());
    const discount = toNumber($f.find('input[name="rate_pmdc"]').val());
    const changeBuy = toNumber($f.find('input[name="buy"]').val());


    const total = ((spot + discount) * 32.1507) * exchange;

    const sell = total;
    const buy = sell - changeBuy;


    writeNumber('sell1', sell, 2);
    writeNumber('buy1', buy, 2);
};


fn.app.announce.announce_silver.recalculate();
