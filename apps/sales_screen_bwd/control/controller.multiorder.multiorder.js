(function ($, window, document, undefined) {
    'use strict';

    if (!window.fn) window.fn = {};
    if (!fn.app) fn.app = {};
    if (!fn.app.sales_screen_bwd) fn.app.sales_screen_bwd = {};

    window.SalesScreenApp = window.SalesScreenApp || {};
    SalesScreenApp.customerManager = {
        __searchInProgress: false,
        __lastSearchKey: null,


        searchCustomerByPhone: function (phone) {
            if (!phone || phone.length < 9) return;

            const searchKey = 'phone:' + phone;
            if (this.__lastSearchKey === searchKey) {
                return;
            }
            this.__lastSearchKey = searchKey;

            if (this.__searchInProgress) {
                console.log('Search already in progress, skipping...');
                return;
            }
            this.__searchInProgress = true;

            const $phoneInput = $('[name="phone"]');
            $phoneInput.addClass('searching-customer');
            const self = this;

            $.ajax({
                type: 'POST',
                url: 'apps/sales_screen_bwd/xhr/action-search-customer.php',
                data: { search: phone },
                dataType: 'json',
                success: function (response) {
                    $phoneInput.removeClass('searching-customer');

                    if (response.success && response.found && response.customer) {
                        self.fillCustomerData(response.customer);
                    } else {
                    }
                },
                error: function () {
                    $phoneInput.removeClass('searching-customer');
                    console.log('ไม่พบข้อมูลลูกค้าหรือเกิดข้อผิดพลาด');
                    self.__lastSearchKey = null;
                },
                complete: function () {
                    self.__searchInProgress = false;
                }
            });
        },

        searchCustomerByUsername: function (username) {
            if (!username || username.length < 3) return;

            const searchKey = 'username:' + username;
            if (this.__lastSearchKey === searchKey) {
                return;
            }
            this.__lastSearchKey = searchKey;

            if (this.__searchInProgress) {
                return;
            }
            this.__searchInProgress = true;

            const $usernameInput = $('[name="username"]');
            $usernameInput.addClass('searching-customer');
            const self = this;

            $.ajax({
                type: 'POST',
                url: 'apps/sales_screen_bwd/xhr/action-search-customer.php',
                data: { search: username },
                dataType: 'json',
                success: function (response) {
                    $usernameInput.removeClass('searching-customer');

                    if (response.success && response.found && response.customer) {
                        self.fillCustomerData(response.customer);
                    } else {
                        console.log('Customer not found');
                    }
                },
                error: function () {
                    $usernameInput.removeClass('searching-customer');
                    self.__lastSearchKey = null;
                },
                complete: function () {
                    self.__searchInProgress = false;
                }
            });
        },

        fillCustomerData: function (customer) {
            const $phoneInput = $('[name="phone"]');
            const $usernameInput = $('[name="username"]');

            $phoneInput.addClass('customer-found');
            $usernameInput.addClass('customer-found');

            setTimeout(function () {
                $phoneInput.removeClass('customer-found');
                $usernameInput.removeClass('customer-found');
            }, 2000);

            if (customer.customer_name) $('[name="customer_name"]').val(customer.customer_name);
            if (customer.phone) $('[name="phone"]').val(customer.phone);
            if (customer.username) $('[name="username"]').val(customer.username);
            if (customer.shipping_address) $('[name="shipping_address"]').val(customer.shipping_address);
            if (customer.billing_address) $('[name="billing_address"]').val(customer.billing_address);

            if (customer.shipping_address) {
                if (fn.app &&
                    fn.app.sales_screen_bwd &&
                    fn.app.sales_screen_bwd.multiorder &&
                    typeof fn.app.sales_screen_bwd.multiorder.calculateTotal === 'function') {
                    fn.app.sales_screen_bwd.multiorder.calculateTotal();
                }
            }

            if (typeof fn !== 'undefined' &&
                typeof fn.notify !== 'undefined' &&
                typeof fn.notify.successbox === 'function') {
                fn.notify.successbox('✅ พบข้อมูลลูกค้าและกรอกข้อมูลเรียบร้อยแล้ว');
            }
        }
    };
    fn.app.sales_screen_bwd.multiorder = {
        __isSubmitting: false,
        __lastRemoveAt: 0,
        __removeCooldownMs: 400,

        remoteAreaPostalCodes: [
            '20120',
            '23170',
            '57170', '57180', '57260',
            '58000', '58110', '58120', '58130', '58140', '58150',
            '63150', '63170',
            '71180', '71240',
            '81150', '81210',
            '82160',
            '83000', '83001', '83002', '83100', '83110', '83111', '83120', '83130', '83150', '83151',
            '84140', '84280', '84310', '84320', '84330', '84360',
            '94000', '94001', '94110', '94120', '94130', '94140', '94150', '94160', '94170', '94180', '94190', '94220', '94230',
            '95000', '95001', '95110', '95120', '95130', '95140', '95150', '95160', '95170',
            '96000', '96110', '96120', '96130', '96140', '96150', '96160', '96170', '96180', '96190', '96210', '96220'
        ],

        extractPostalCode: function (address) {
            if (!address) return null;
            const matches = address.match(/\b(\d{5})\b/g);
            if (matches && matches.length > 0) {
                return matches[matches.length - 1];
            }
            return null;
        },

        isRemoteArea: function (postalCode) {
            if (!postalCode) return false;
            return this.remoteAreaPostalCodes.includes(postalCode);
        },

        hasWoodenBox: function () {
            let hasWooden = false;
            $('.item-row').each(function () {
                const productType = $(this).find('[name*="[product_type]"]').val();
                if (productType && ['17', '18', '19', '20'].includes(productType)) {
                    hasWooden = true;
                    return false;
                }
            });
            return hasWooden;
        },

        hasWoodenBoxInItems: function (items) {
            for (let i = 0; i < items.length; i++) {
                if (['17', '18', '19', '20'].includes(items[i].product_type)) {
                    return true;
                }
            }
            return false;
        },

        hasPremiumBox: function () {
            let hasPremium = false;
            $('.item-row').each(function () {
                const productType = $(this).find('[name*="[product_type]"]').val();
                if (productType && ['13', '14', '15', '16', '21', '22', '23', '24', '25'].includes(productType)) {
                    hasPremium = true;
                    return false;
                }
            });
            return hasPremium;
        },

        hasPremiumBoxInItems: function (items) {
            for (let i = 0; i < items.length; i++) {
                if (['13', '14', '15', '16', '21', '22', '23', '24', '25'].includes(items[i].product_type)) {
                    return true;
                }
            }
            return false;
        },
        splitIntoBoxes: function (items) {
            const boxes = [];
            let currentBox = { items: [], total: 0 };
            const remainingItems = [];

            for (let i = 0; i < items.length; i++) {
                const item = items[i];
                let unitPrice = item.price;
                if (item.discount > 0) {
                    unitPrice = unitPrice * (1 - item.discount / 100);
                }
                const itemTotal = item.amount * unitPrice;

                if (currentBox.total + itemTotal <= 50000) {
                    currentBox.items.push({
                        product_id: item.product_id,
                        product_type: item.product_type,
                        amount: item.amount,
                        price: item.price,
                        discount: item.discount,
                        ai: item.ai || "0"  
                    });
                    currentBox.total += itemTotal;
                }
                else if (currentBox.total < 50000) {
                    const remainingSpace = 50000 - currentBox.total;
                    const canFitAmount = Math.floor(remainingSpace / unitPrice);

                    if (canFitAmount > 0) {
                        currentBox.items.push({
                            product_id: item.product_id,
                            product_type: item.product_type,
                            amount: canFitAmount,
                            price: item.price,
                            discount: item.discount,
                            ai: item.ai || "0"  
                        });
                        currentBox.total += canFitAmount * unitPrice;

                        const remaining = item.amount - canFitAmount;
                        if (remaining > 0) {
                            remainingItems.push({
                                product_id: item.product_id,
                                product_type: item.product_type,
                                amount: remaining,
                                price: item.price,
                                discount: item.discount,
                                ai: item.ai || "0" 
                            });
                        }
                    } else {
                        remainingItems.push(item);
                    }
                }
                else {
                    remainingItems.push(item);
                }
            }

            if (currentBox.items.length > 0) {
                boxes.push(currentBox);
            }

            if (remainingItems.length > 0) {
                currentBox = { items: [], total: 0 };

                for (let i = 0; i < remainingItems.length; i++) {
                    const item = remainingItems[i];
                    let unitPrice = item.price;
                    if (item.discount > 0) {
                        unitPrice = unitPrice * (1 - item.discount / 100);
                    }
                    const itemTotal = item.amount * unitPrice;

                    if (currentBox.total + itemTotal > 50000 && currentBox.items.length > 0) {
                        boxes.push(currentBox);
                        currentBox = { items: [], total: 0 };
                    }

                    currentBox.items.push({
                        product_id: item.product_id,
                        product_type: item.product_type,
                        amount: item.amount,
                        price: item.price,
                        discount: item.discount,
                        ai: item.ai || "0"  
                    });
                    currentBox.total += itemTotal;
                }

                if (currentBox.items.length > 0) {
                    boxes.push(currentBox);
                }
            }

            return boxes;
        },
        calculateShippingPerBox: function (boxItems, boxTotal, boxNumber, isRemote, orderableType) {
            let baseShipping = 0;
            if (boxTotal >= 1 && boxTotal <= 14999) {
                baseShipping = 50;
            } else if (boxTotal >= 15000 && boxTotal <= 50000) {
                baseShipping = 100;
            } else if (boxTotal > 50000) {
                baseShipping = 100;
            }

            let woodenBoxCount = 0;
            let premiumBoxCount = 0;

            for (let i = 0; i < boxItems.length; i++) {
                const item = boxItems[i];
                const productType = parseInt(item.product_type);
                const amount = parseFloat(item.amount) || 0;

                if ([17, 18, 19, 20].indexOf(productType) !== -1) {
                    woodenBoxCount += amount;
                }

                if ([13, 14, 15, 16, 21, 22, 23, 24, 25].indexOf(productType) !== -1) {
                    premiumBoxCount += amount;
                }
            }

            let boxFee = 0;
            boxFee += woodenBoxCount * 100;
            boxFee += premiumBoxCount * 25;

            let remoteFee = 0;

            if (isRemote && orderableType === 'post_office') {
                remoteFee = 50;
            }

            const total = baseShipping + boxFee + remoteFee;

            return {
                box_number: boxNumber,
                base: baseShipping,
                box_fee: boxFee,
                remote_fee: remoteFee,
                total: total,
                wooden_count: woodenBoxCount,
                premium_count: premiumBoxCount
            };
        },
        calculateItemTotal: function ($row) {
            const platform = $('[name="platform"]').val();
            const isMarketplace = ['Shopee', 'Lazada', 'TikTok'].includes(platform);

            const amount = parseFloat($row.find('[name*="[amount]"]').val()) || 0;
            const price = parseFloat($row.find('[name*="[price]"]').val()) || 0;
            const discountType = parseInt($row.find('[name*="[discount]"]').val()) || 0;
            const hasEngrave = $row.find('[name*="[engrave]"]:checked').val() === 'สลักข้อความบนแท่งเงิน';
            const hasAI = $row.find('[name*="[ai]"]').val() == '1';

            let unitPrice = price;

            if (discountType === 5) unitPrice = price * 0.95;
            else if (discountType === 10) unitPrice = price * 0.90;
            else if (discountType === 15) unitPrice = price * 0.85;
            else if (discountType === 20) unitPrice = price * 0.80;
            else if (discountType === 25) unitPrice = price * 0.75;
            else if (discountType === 30) unitPrice = price * 0.70;

            let itemTotal = amount * unitPrice;

            if (!isMarketplace) {
                if (hasEngrave) itemTotal += (amount * 300);
                if (hasAI) itemTotal += (amount * 400);
            }

            $row.find('.item-total-display').val(itemTotal.toFixed(2));

            const isSplitting = $row.data('is-splitting');
            if (!isSplitting && itemTotal > 50000) {
                $row.data('is-splitting', true);
                this.autoSplitItemIfNeeded($row, itemTotal, amount, unitPrice, hasEngrave, hasAI);
                $row.data('is-splitting', false);
            }

            return itemTotal;
        },

        autoSplitItemIfNeeded: function ($row, itemTotal, amount, unitPriceAfterDiscount, hasEngrave, hasAI) {
            const self = this;
            const platform = $('[name="platform"]').val();
            const isMarketplace = ['Shopee', 'Lazada', 'TikTok'].includes(platform);

            const orderableType = $('#orderable_type').val();
            if (orderableType === 'receive_at_company' || orderableType === 'receive_at_luckgems') {
                return;
            }


            if (itemTotal <= 50000) {
                return;
            }

            let unitPriceTotal = unitPriceAfterDiscount;
            if (!isMarketplace) {
                if (hasEngrave) unitPriceTotal += 300;
                if (hasAI) unitPriceTotal += 400;
            }

            const maxPerBox = Math.floor(50000 / unitPriceTotal);

            if (maxPerBox <= 0 || amount <= maxPerBox) {
                return;
            }
            const items = [];
            let remainingQty = amount;

            while (remainingQty > 0) {
                const qtyForThisBox = Math.min(remainingQty, maxPerBox);
                items.push(qtyForThisBox);
                remainingQty -= qtyForThisBox;
            }


            $(document).off('change.multiorder input.multiorder', '[name*="[amount]"]');
            $(document).off('change.multiorder input.multiorder', '[name*="[price]"]');
            $(document).off('change.multiorder input.multiorder', '[name*="[discount]"]');

            $row.find('[name*="[amount]"]').val(items[0]);

            const firstItemTotal = items[0] * unitPriceTotal;
            $row.find('.item-total-display').val(firstItemTotal.toFixed(2));

            for (let i = 1; i < items.length; i++) {
                const $container = $('#items-container');

                const productId = $row.find('[name*="[product_id]"]').val();
                const productType = $row.find('[name*="[product_type]"]').val();
                const price = $row.find('[name*="[price]"]').val();
                const discount = $row.find('[name*="[discount]"]').val();
                const engraveValue = $row.find('[name*="[engrave]"]:checked').val();
                const carving = $row.find('[name*="[carving]"]').val();
                const font = $row.find('[name*="[font]"]').val();
                const ai = $row.find('[name*="[ai]"]').val();

                const $newRow = $row.clone(false, false);

                const currentCount = $('.item-row').length;
                const newCounter = currentCount;

                $newRow.find('[name]').each(function () {
                    const oldName = $(this).attr('name');
                    const newName = oldName.replace(/\[\d+\]/, '[' + newCounter + ']');
                    $(this).attr('name', newName);
                });

                $newRow.find('[id]').each(function () {
                    const oldId = $(this).attr('id');
                    if (!oldId) return;
                    const newId = oldId.replace(/_\d+$/, '_' + newCounter);
                    $(this).attr('id', newId);
                });

                $newRow.find('[for]').each(function () {
                    const oldFor = $(this).attr('for');
                    if (!oldFor) return;
                    const newFor = oldFor.replace(/_\d+$/, '_' + newCounter);
                    $(this).attr('for', newFor);
                });

                $newRow.find('[name*="[product_id]"]').val(productId);
                $newRow.find('[name*="[product_type]"]').val(productType);
                $newRow.find('[name*="[price]"]').val(price);
                $newRow.find('[name*="[discount]"]').val(discount);
                $newRow.find('[name*="[ai]"]').val(ai);

                $newRow.find('[name*="[engrave]"]').prop('checked', false);
                if (engraveValue === 'สลักข้อความบนแท่งเงิน') {
                    $newRow.find('[name*="[engrave]"][value="สลักข้อความบนแท่งเงิน"]').prop('checked', true);
                    $newRow.find('.carving-input').attr('readonly', false).val(carving);
                    $newRow.find('.font-select').prop('disabled', false).val(font);
                } else {
                    $newRow.find('[name*="[engrave]"][value="ไม่สลักข้อความบนแท่งเงิน"]').prop('checked', true);
                    $newRow.find('.carving-input').attr('readonly', true).val('');
                    $newRow.find('.font-select').prop('disabled', true).val('');
                }

                $newRow.find('.item-row-header').html(
                    '<i class="fas fa-cube mr-2"></i>รายการที่ ' + (currentCount + 1) +
                    ' <span class="badge badge-warning ml-2">แยกอัตโนมัติ</span>'
                );

                $newRow.find('[name*="[amount]"]').val(items[i]);

                const newItemTotal = items[i] * unitPriceTotal;
                $newRow.find('.item-total-display').val(newItemTotal.toFixed(2));

                $container.append($newRow);
                if (productId && productType) {
                    $.ajax({
                        type: 'POST',
                        url: 'apps/sales_screen_bwd/xhr/action-load-Type.php',
                        data: 'id=' + productId,
                        success: function (html) {
                            $newRow.find('.product-type-select').html(html);
                            $newRow.find('[name*="[product_type]"]').val(productType);
                        }
                    });
                }
            }

            setTimeout(function () {
                $(document).on('change.multiorder input.multiorder',
                    '#items-container [name*="[amount]"], ' +
                    '#items-container [name*="[price]"], ' +
                    '#items-container [name*="[discount]"]',
                    function (e) {
                        e.stopPropagation();
                        const $row = $(this).closest('.item-row');
                        self.calculateItemTotal($row);
                        self.calculateTotal();
                    }
                );

            }, 200);

            self.updateRemoveButtons();
            self.renumberItems();

            setTimeout(function () {
                self.calculateTotal();
            }, 300);

            const message = 'เพิ่มกล่องอัตโนมัติ ' + items.length + ' รายการ\nเนื่องจากรายการสินค้ายอดเกิน 50,000 บาท';

            if (typeof fn !== 'undefined' &&
                typeof fn.notify !== 'undefined' &&
                typeof fn.notify.infobox === 'function') {
                fn.notify.infobox(message);
            } else {
                alert(message);
            }
        },

        renumberItems: function () {
            $('.item-row').each(function (index) {
                const $header = $(this).find('.item-row-header');
                const hasBadge = $header.find('.badge').length > 0;

                if (hasBadge) {
                    $header.html(
                        '<i class="fas fa-cube mr-2"></i>รายการที่ ' + (index + 1) +
                        ' <span class="badge badge-warning ml-2">แยกอัตโนมัติ</span>'
                    );
                } else {
                    $header.html('<i class="fas fa-cube mr-2"></i>รายการที่ ' + (index + 1));
                }
            });
        },

        calculateAutoShipping: function (subtotalAfterDiscount) {
            const shippingMethod = $('#shipping').val();
            const orderableType = $('#orderable_type').val();
            const shippingAddress = $('#shipping_address').val() || '';


            const postalCode = this.extractPostalCode(shippingAddress);
            const isRemote = this.isRemoteArea(postalCode);


            const manualRemoteFee = parseInt($('#remote_area_fee').val()) || 0;
            const isRemoteActual = manualRemoteFee > 0 ? true : isRemote;


            if (shippingMethod === '4') {
                return {
                    baseShipping: 0,
                    boxFee: 0,
                    remoteFee: 0,
                    total: 0,
                    numBoxes: 1,
                    postalCode: postalCode,
                    isRemote: isRemoteActual,
                    shippingPerBox: [{
                        box_number: 0,
                        base: 0,
                        box_fee: 0,
                        remote_fee: 0,
                        total: 0,
                        wooden_count: 0,
                        premium_count: 0
                    }]
                };
            }

            if (orderableType === 'receive_at_company' || orderableType === 'receive_at_luckgems') {
                return {
                    baseShipping: 0,
                    boxFee: 0,
                    remoteFee: 0,
                    total: 0,
                    numBoxes: 1,
                    postalCode: postalCode,
                    isRemote: isRemoteActual,
                    shippingPerBox: [{
                        box_number: 0,
                        base: 0,
                        box_fee: 0,
                        remote_fee: 0,
                        total: 0,
                        wooden_count: 0,
                        premium_count: 0
                    }]
                };
            }

            if (shippingMethod === '1') {
                const hasWooden = this.hasWoodenBox();
                const hasPremium = this.hasPremiumBox();

                let woodenCount = 0;
                let premiumCount = 0;

                $('.item-row').each(function () {
                    const productType = parseInt($(this).find('[name*="[product_type]"]').val());
                    const amount = parseFloat($(this).find('[name*="[amount]"]').val()) || 0;

                    if ([17, 18, 19, 20].includes(productType)) {
                        woodenCount += amount;
                    }
                    if ([13, 14, 15, 16, 21, 22, 23, 24, 25].includes(productType)) {
                        premiumCount += amount;
                    }
                });

                const boxFee = hasWooden ? 100 : (hasPremium ? 25 : 0);
                const remoteFee = (isRemoteActual && orderableType === 'post_office') ? 50 : 0;

                return {
                    baseShipping: 50,
                    boxFee: boxFee,
                    remoteFee: remoteFee,
                    total: 50 + boxFee + remoteFee,
                    numBoxes: 1,
                    postalCode: postalCode,
                    isRemote: isRemoteActual,
                    shippingPerBox: [{
                        box_number: 0,
                        base: 50,
                        box_fee: boxFee,
                        remote_fee: remoteFee,
                        total: 50 + boxFee + remoteFee,
                        wooden_count: woodenCount,
                        premium_count: premiumCount
                    }]
                };
            }

            if (shippingMethod === '2') {

                const hasWooden = this.hasWoodenBox();
                const hasPremium = this.hasPremiumBox();

                let woodenCount = 0;
                let premiumCount = 0;

                $('.item-row').each(function () {
                    const productType = parseInt($(this).find('[name*="[product_type]"]').val());
                    const amount = parseFloat($(this).find('[name*="[amount]"]').val()) || 0;

                    if ([17, 18, 19, 20].includes(productType)) {
                        woodenCount += amount;
                    }
                    if ([13, 14, 15, 16, 21, 22, 23, 24, 25].includes(productType)) {
                        premiumCount += amount;
                    }
                });

                const boxFee = hasWooden ? 100 : (hasPremium ? 25 : 0);
                const remoteFee = (isRemoteActual && orderableType === 'post_office') ? 50 : 0;

                return {
                    baseShipping: 100,
                    boxFee: boxFee,
                    remoteFee: remoteFee,
                    total: 100 + boxFee + remoteFee,
                    numBoxes: 1,
                    postalCode: postalCode,
                    isRemote: isRemoteActual,
                    shippingPerBox: [{
                        box_number: 0,
                        base: 100,
                        box_fee: boxFee,
                        remote_fee: remoteFee,
                        total: 100 + boxFee + remoteFee,
                        wooden_count: woodenCount,
                        premium_count: premiumCount
                    }]
                };
            }


            const allItems = [];
            $('.item-row').each(function () {
                const productId = $(this).find('[name*="[product_id]"]').val();
                const productType = $(this).find('[name*="[product_type]"]').val();
                const amount = parseFloat($(this).find('[name*="[amount]"]').val() || 0);
                const price = parseFloat($(this).find('[name*="[price]"]').val() || 0);
                const discount = parseInt($(this).find('[name*="[discount]"]').val() || 0);
                const ai = $(this).find('[name*="[ai]"]').val() || "0";  

                if (productId && productType && !isNaN(amount) && amount > 0 && !isNaN(price) && price >= 0) {
                    allItems.push({
                        product_id: productId,
                        product_type: productType,
                        amount: amount,
                        price: price,
                        discount: discount,
                        ai: ai 
                    });
                }
            });

            if (allItems.length === 0) {
                return {
                    baseShipping: 0,
                    boxFee: 0,
                    remoteFee: 0,
                    total: 0,
                    numBoxes: 0,
                    postalCode: postalCode,
                    isRemote: isRemoteActual,
                    shippingPerBox: []
                };
            }

            const boxes = this.splitIntoBoxes(allItems);
            const shippingPerBox = [];
            let totalShipping = 0;

            const self = this;
            boxes.forEach(function (box, index) {
                const boxShipping = self.calculateShippingPerBox(
                    box.items,
                    box.total,
                    index,
                    isRemoteActual,
                    orderableType
                );


                shippingPerBox.push(boxShipping);
                totalShipping += boxShipping.total;
            });

            let totalRemoteFee = 0;
            shippingPerBox.forEach(function (ship) {
                totalRemoteFee += ship.remote_fee;
            });


            return {
                baseShipping: totalShipping,
                boxFee: 0,
                remoteFee: totalRemoteFee,
                total: totalShipping,
                numBoxes: boxes.length,
                postalCode: postalCode,
                isRemote: isRemoteActual,
                shippingPerBox: shippingPerBox,
                boxes: boxes
            };
        },
        calculateTotal: function () {
            const platform = $('[name="platform"]').val();
            const isMarketplace = ['Shopee', 'Lazada', 'TikTok'].includes(platform);

            let subtotal = 0;
            let totalDiscount = 0;
            let engraveCost = 0;
            let aiCost = 0;

            $('.item-row').each(function () {
                const $row = $(this);
                const amount = parseFloat($row.find('[name*="[amount]"]').val()) || 0;
                const price = parseFloat($row.find('[name*="[price]"]').val()) || 0;
                const discountType = parseInt($row.find('[name*="[discount]"]').val()) || 0;
                const hasEngrave = $row.find('[name*="[engrave]"]:checked').val() === 'สลักข้อความบนแท่งเงิน';
                const hasAI = $row.find('[name*="[ai]"]').val() == '1';

                const itemTotal = amount * price;
                let itemDiscount = 0;

                if (discountType === 5) itemDiscount = itemTotal * 0.05;
                else if (discountType === 10) itemDiscount = itemTotal * 0.10;
                else if (discountType === 15) itemDiscount = itemTotal * 0.15;
                else if (discountType === 20) itemDiscount = itemTotal * 0.20;
                else if (discountType === 25) itemDiscount = itemTotal * 0.25;
                else if (discountType === 30) itemDiscount = itemTotal * 0.30;

                subtotal += itemTotal;
                totalDiscount += itemDiscount;

                if (!isMarketplace) {
                    if (hasEngrave) engraveCost += (amount * 300);
                    if (hasAI) aiCost += (amount * 400);
                }

                const finalItemTotal = (itemTotal - itemDiscount) +
                    (!isMarketplace && hasEngrave ? amount * 300 : 0) +
                    (!isMarketplace && hasAI ? amount * 400 : 0);
                $row.find('.item-total-display').val(finalItemTotal.toFixed(2));
            });

            const subtotalAfterDiscount = subtotal - totalDiscount;
            const shippingData = this.calculateAutoShipping(subtotalAfterDiscount);
            const shippingCost = shippingData.total;
            const fee = parseFloat($('#fee').val() || 0);

            const grandTotal = subtotalAfterDiscount + engraveCost + aiCost + shippingCost - fee;


            $('#subtotal-amount').text(subtotal.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#discount-amount').text(totalDiscount.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#subtotal-after-discount').text(subtotalAfterDiscount.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            $('#engrave-amount').text(engraveCost.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#ai-amount').text(aiCost.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            $('#shipping-amount').text(shippingCost.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#fee-amount').text(fee.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#grand-total').text(grandTotal.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            this.displayShippingBreakdown(shippingData);

            return {
                subtotal: subtotal,
                discount: totalDiscount,
                subtotalAfterDiscount: subtotalAfterDiscount,
                engraveCost: engraveCost,
                aiCost: aiCost,
                shipping: shippingCost,
                fee: fee,
                grandTotal: grandTotal,
                shippingData: shippingData
            };
        },
        displayShippingBreakdown: function (shippingData) {
            const $container = $('#shipping-breakdown');

            if (!$container.length) return;

            if (!shippingData ||
                !shippingData.shippingPerBox ||
                !Array.isArray(shippingData.shippingPerBox) ||
                shippingData.shippingPerBox.length === 0) {
                $container.html('').hide();
                return;
            }

            let html = '<div class="card mt-3">';
            html += '<div class="card-header bg-info text-white">';
            html += '<h5 class="mb-0"><i class="fas fa-shipping-fast mr-2"></i>รายละเอียดค่าจัดส่ง</h5>';
            html += '</div><div class="card-body">';

            if (shippingData.postalCode) {
                const badgeClass = shippingData.isRemote ? 'badge-warning' : 'badge-success';
                const remoteText = shippingData.isRemote ? 'พื้นที่ห่างไกล' : 'พื้นที่ปกติ';
                html += '<div class="mb-3"><strong>รหัสไปรษณีย์:</strong> ';
                html += '<span class="badge ' + badgeClass + ' badge-remote">';
                html += shippingData.postalCode + ' (' + remoteText + ')</span></div>';
            }

            if (shippingData.numBoxes > 1) {
                html += '<div class="alert alert-info mb-3"><i class="fas fa-info-circle mr-2"></i>';
                html += '<strong>Order นี้แบ่งเป็น ' + shippingData.numBoxes + ' กล่อง</strong>';
                html += ' (เนื่องจากมูลค่ารวมเกิน 50,000 บาท)</div>';
            }

            html += '<table class="table table-bordered table-sm">';
            html += '<thead class="thead-light"><tr>';
            html += '<th class="text-center" width="80">กล่องที่</th>';
            html += '<th class="text-right">ค่าส่งฐาน</th>';
            html += '<th class="text-right">ค่ากล่องพิเศษ</th>';
            html += '<th class="text-right">ค่าพื้นที่ห่างไกล</th>';
            html += '<th class="text-right" width="120"><strong>รวม</strong></th>';
            html += '</tr></thead><tbody>';

            let totalBase = 0,
                totalBoxFee = 0,
                totalRemoteFee = 0,
                totalShipping = 0;

            shippingData.shippingPerBox.forEach(function (box) {
                const boxNumber = (box.box_number !== undefined) ? box.box_number : 0;
                const base = parseFloat(box.base) || 0;
                const boxFee = parseFloat(box.box_fee) || 0;
                const remoteFee = parseFloat(box.remote_fee) || 0;
                const total = parseFloat(box.total) || 0;
                const woodenCount = parseFloat(box.wooden_count) || 0;
                const premiumCount = parseFloat(box.premium_count) || 0;

                html += '<tr>';
                html += '<td class="text-center"><strong>' + (boxNumber + 1) + '</strong></td>';
                html += '<td class="text-right">' + base.toLocaleString('th-TH', {
                    minimumFractionDigits: 2
                }) + ' ฿</td>';

                html += '<td class="text-right">';
                if (woodenCount > 0 || premiumCount > 0) {
                    const details = [];
                    if (woodenCount > 0) {
                        details.push('ไม้ ' + woodenCount + '×100');
                    }
                    if (premiumCount > 0) {
                        details.push('พิเศษ ' + premiumCount + '×25');
                    }
                    html += '<span title="' + details.join(', ') + '">';
                    html += boxFee.toLocaleString('th-TH', { minimumFractionDigits: 2 }) + ' ฿';
                    html += '</span>';
                } else {
                    html += '0.00 ฿';
                }
                html += '</td>';

                html += '<td class="text-right">';
                if (remoteFee > 0) {
                    html += '<span class="text-warning">' + remoteFee.toLocaleString('th-TH', {
                        minimumFractionDigits: 2
                    }) + ' ฿</span>';
                } else {
                    html += '0.00 ฿';
                }
                html += '</td>';
                html += '<td class="text-right"><strong>' + total.toLocaleString('th-TH', {
                    minimumFractionDigits: 2
                }) + ' ฿</strong></td>';
                html += '</tr>';

                totalBase += base;
                totalBoxFee += boxFee;
                totalRemoteFee += remoteFee;
                totalShipping += total;
            });

            html += '<tr class="table-info font-weight-bold">';
            html += '<td class="text-center">รวมทั้งหมด</td>';
            html += '<td class="text-right">' + totalBase.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            }) + ' ฿</td>';
            html += '<td class="text-right">' + totalBoxFee.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            }) + ' ฿</td>';
            html += '<td class="text-right">';
            if (totalRemoteFee > 0) {
                html += '<span class="text-warning">' + totalRemoteFee.toLocaleString('th-TH', {
                    minimumFractionDigits: 2
                }) + ' ฿</span>';
            } else {
                html += '0.00 ฿';
            }
            html += '</td>';
            html += '<td class="text-right"><strong class="text-primary" style="font-size: 1.1rem;">';
            html += totalShipping.toLocaleString('th-TH', {
                minimumFractionDigits: 2
            }) + ' ฿</strong></td>';
            html += '</tr></tbody></table></div></div>';

            $container.html(html).show();
        },

        validateForm: function () {
            const customerName = ($('[name="customer_name"]').val() || '').trim();
            const phone = ($('[name="phone"]').val() || '').trim();
            const username = ($('[name="username"]').val() || '').trim();
            const platform = $('[name="platform"]').val();
            const vat_type = $('[name="vat_type"]').val();
            const orderable_type = $('[name="orderable_type"]').val();

            const orderPlatform = ($('[name="order_platform"]').val() || '').trim();
            const marketplacePlatforms = ['Shopee', 'Lazada', 'TikTok', 'SilverNow'];


            if (!customerName) {
                alert("กรุณากรอกชื่อลูกค้า");
                $('[name="customer_name"]').focus();
                return false;
            }
            if (!phone && !username) {
                alert("กรุณากรอกเบอร์โทรศัพท์หรือ Username");
                $('[name="phone"]').focus();
                return false;
            }
            if (!platform) {
                alert("กรุณาเลือก Platform");
                $('[name="platform"]').focus();
                return false;
            }
            if (marketplacePlatforms.includes(platform) && !orderPlatform) {
                alert("กรุณากรอกเลข Order จาก Platform");
                $('[name="order_platform"]').focus();
                return false;
            }
            if (!vat_type) {
                alert("กรุณาเลือกการเสีย Vats");
                $('[name="vat_type"]').focus();
                return false;
            }
            if (!orderable_type) {
                alert("กรุณาเลือกรูปแบบการจัดส่ง");
                $('[name="orderable_type"]').focus();
                return false;
            }

            const feeStr = ($('[name="fee"]').val() || '').trim();
            if (feeStr !== '') {
                const feeVal = parseFloat(feeStr.replace(/,/g, ''));
                if (isNaN(feeVal) || feeVal < 0) {
                    alert("กรุณากรอกค่าธรรมเนียมเป็นตัวเลขที่ไม่ติดลบ");
                    $('[name="fee"]').focus();
                    return false;
                }
            }

            let hasValidItem = false;
            $('.item-row').each(function () {
                const $row = $(this);
                const productId = $row.find('[name*="[product_id]"]').val();
                const productType = $row.find('[name*="[product_type]"]').val();
                const amountRaw = ($row.find('[name*="[amount]"]').val() || '').toString().replace(/,/g, '').trim();
                const priceRaw = ($row.find('[name*="[price]"]').val() || '').toString().replace(/,/g, '').trim();
                const amount = parseFloat(amountRaw);
                const price = parseFloat(priceRaw);


                if (productId && productType && !isNaN(amount) && amount > 0 && !isNaN(price) && price >= 0) {
                    hasValidItem = true;
                }
            });

            if (!hasValidItem) {
                alert("กรุณาเพิ่มรายการสินค้าที่ถูกต้องอย่างน้อย 1 รายการ");
                return false;
            }
            return true;
        },

        collectFormData: function () {
            const feeVal = parseFloat((($('[name="fee"]').val() || '0').toString().replace(/,/g, '').trim())) || 0;

            const formData = {
                customer_name: ($('[name="customer_name"]').val() || '').trim(),
                phone: ($('[name="phone"]').val() || '').trim(),
                username: ($('[name="username"]').val() || '').trim(),
                platform: $('[name="platform"]').val(),
                order_platform: ($('[name="order_platform"]').val() || '').trim(),
                vat_type: $('[name="vat_type"]').val(),
                date: $('[name="date"]').val(),
                delivery_date: $('[name="delivery_date"]').val(),
                shipping: $('[name="shipping"]').val(),
                fee: feeVal,
                shipping_address: ($('[name="shipping_address"]').val() || '').trim(),
                billing_address: ($('[name="billing_address"]').val() || '').trim(),
                orderable_type: ($('[name="orderable_type"]').val() || '').trim(),
                comment: ($('[name="comment"]').val() || '').trim(),
                remote_area_fee: parseInt($('#remote_area_fee').val()) || 0,
                items: []
            };

            console.log('🔍 Collecting form data...');

            $('.item-row').each(function (index) {
                const $row = $(this);
                const productId = $row.find('[name*="[product_id]"]').val();
                const productType = $row.find('[name*="[product_type]"]').val();
                const amount = parseFloat((($row.find('[name*="[amount]"]').val() || '').toString().replace(/,/g, '').trim()));
                const price = parseFloat((($row.find('[name*="[price]"]').val() || '').toString().replace(/,/g, '').trim()));

                const $aiInput = $row.find('[name*="[ai]"]');
                const aiValue = $aiInput.val() || "0";

                console.log('Row #' + (index + 1) + ' AI Input:', {
                    selector: '[name*="[ai]"]',
                    found: $aiInput.length,
                    value: aiValue,
                    type: typeof aiValue,
                    attrName: $aiInput.attr('name')
                });

                if (productId && productType && !isNaN(amount) && amount > 0 && !isNaN(price) && price >= 0) {
                    const itemData = {
                        product_id: productId,
                        product_type: productType,
                        amount: amount,
                        price: price,
                        discount: $row.find('[name*="[discount]"]').val() || "0",
                        engrave: $row.find('[name*="[engrave]"]:checked').val() || 'ไม่สลักข้อความบนแท่งเงิน',
                        font: $row.find('[name*="[font]"]').val() || '',
                        carving: (($row.find('[name*="[carving]"]').val() || '').trim()),
                        ai: aiValue  
                    };

                    console.log('✅ Item #' + (index + 1) + ' pushed with AI:', itemData.ai);
                    console.log('   Full item data:', itemData);

                    formData.items.push(itemData);
                }
            });


            const shippingData = this.calculateAutoShipping(0);
            if (shippingData.boxes && shippingData.boxes.length > 0) {
                formData.boxes = shippingData.boxes;
                console.log('📦 Boxes added to formData:', shippingData.boxes.length);
            }
            if (shippingData.shippingPerBox && shippingData.shippingPerBox.length > 0) {
                formData.shipping_breakdown = {
                    numBoxes: shippingData.numBoxes,
                    shippingPerBox: shippingData.shippingPerBox,
                    postalCode: shippingData.postalCode,
                    isRemote: shippingData.isRemote
                };
            }

            return formData;
        },

        submitOrder: function () {
            const self = this;

            if (self.__isSubmitting) return;
            self.__isSubmitting = true;

            self.proceedSubmitOrder();
        },

        proceedSubmitOrder: function () {
            const self = this;
            const formData = self.collectFormData();

            console.log('🚀 Submitting order with formData:', formData);

            const $btn = $('#btn-submit, .btn-submit').first();
            const oldHtml = $btn.length ? $btn.html() : null;

            if ($btn.length) {
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>กำลังทำรายการ...');
            }

            $.post(
                "apps/sales_screen_bwd/xhr/action-add-multi-order.php",
                { data: JSON.stringify(formData) },
                function (response) {
                    console.log('📥 Server response:', response);

                    if (response.success) {
                        const successMsg = 'สร้าง Order สำเร็จ!\n' +
                            'Order Code: ' + response.order_code + '\n' +
                            'จำนวนกล่อง: ' + response.num_boxes + ' กล่อง\n' +
                            'ยอดรวมสุทธิ: ' + response.total_net.toLocaleString('th-TH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' บาท';

                        if (typeof fn !== 'undefined' &&
                            typeof fn.notify !== 'undefined' &&
                            typeof fn.notify.successbox === 'function') {
                            fn.notify.successbox(successMsg);
                        } else {
                            alert(successMsg);
                        }
                        setTimeout(function () {
                            try {
                                if ($.fn.DataTable.isDataTable('#tblQuickOrder')) {
                                    $('#tblQuickOrder').DataTable().ajax.reload(null, false);
                                }
                            } catch (e) {
                            }

                            try {
                                if ($.fn.DataTable.isDataTable('#tblOrdersList')) {
                                    $('#tblOrdersList').DataTable().ajax.reload(null, false);
                                }
                            } catch (e) {
                            }

                            try {
                                if ($.fn.DataTable.isDataTable('#tblOrder')) {
                                    $('#tblOrder').DataTable().ajax.reload(null, false);
                                    console.log('✅ Reloaded tblOrder');
                                }
                            } catch (e) {
                            }
                        }, 500);

                        setTimeout(function () {
                            self.resetForm();
                        }, 1000);


                    } else {
                        const errorMsg = 'เกิดข้อผิดพลาด: ' + (response.msg || 'Unknown error');

                        if (typeof fn !== 'undefined' &&
                            typeof fn.notify !== 'undefined' &&
                            typeof fn.notify.errorbox === 'function') {
                            fn.notify.errorbox(errorMsg);
                        } else {
                            alert(errorMsg);
                        }
                    }
                },
                "json"
            ).fail(function (xhr, status, error) {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });

                const errorMsg = "เกิดข้อผิดพลาดในการเชื่อมต่อ: " + error +
                    "\nStatus: " + xhr.status +
                    "\nResponse: " + xhr.responseText.substring(0, 200);

                if (typeof fn !== 'undefined' &&
                    typeof fn.notify !== 'undefined' &&
                    typeof fn.notify.errorbox === 'function') {
                    fn.notify.errorbox(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }).always(function () {
                self.__isSubmitting = false;
                if ($btn.length) {
                    $btn.prop('disabled', false).html(oldHtml);
                }
            });
        },

        add: function () {
            if (!this.validateForm()) return false;

            const self = this;

            if (typeof fn.dialog !== 'undefined' && typeof fn.dialog.confirmbox === 'function') {
                fn.dialog.confirmbox("Confirmation", "ยืนยันการสร้าง Order หรือไม่?", function () {
                    self.submitOrder();
                });
            } else {
                if (confirm("ยืนยันการสร้าง Order หรือไม่?")) {
                    self.submitOrder();
                }
            }
            return false;
        },

        resetForm: function () {
            const $form = $("form[name=form_multi_order]");
            if ($form.length) $form[0].reset();

            const firstItem = $('#items-container .item-row:first');
            $('#items-container .item-row').not(':first').remove();

            firstItem.find('select').prop('selectedIndex', 0);
            firstItem.find('input[type="text"], input[type="number"]').val('');
            firstItem.find('input[name*="[amount]"]').val('1');
            firstItem.find('input[type="radio"][value="ไม่สลักข้อความบนแท่งเงิน"]').prop('checked', true);
            firstItem.find('.carving-input').attr('readonly', true).val('');
            firstItem.find('.font-select').prop('disabled', true);
            firstItem.find('.item-total-display').val('0.00');
            firstItem.find('.product-type-select').html('<option value="">-- เลือกสินค้าก่อน --</option>');

            $('[name="fee"]').val('0');
            if ($('#fee-amount').length) $('#fee-amount').text('0.00');

            $('[name="order_platform"]').val('');

            $('#shipping-breakdown').html('').hide();

            const today = new Date().toISOString().split('T')[0];
            const deliveryDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            $('[name="date"]').val(today);
            $('[name="delivery_date"]').val(deliveryDate);

            $('#shipping').val('auto');
            $('#remote_area_fee').val('0');

            this.updateRemoveButtons();
            this.calculateTotal();
        },

        updateRemoveButtons: function () {
            const itemCount = $('.item-row').length;
            if (itemCount <= 1) {
                $('.btn-remove-item').prop('disabled', true).addClass('disabled');
            } else {
                $('.btn-remove-item').prop('disabled', false).removeClass('disabled');
            }
        },

        addItem: function () {
            const $container = $('#items-container');
            const $firstRow = $container.find('.item-row:first');

            if (!$firstRow.length) {
                return;
            }

            const $newRow = $firstRow.clone(false, false);

            const currentCount = $('.item-row').length;
            const newCounter = currentCount;

            $newRow.find('[name]').each(function () {
                const oldName = $(this).attr('name');
                const newName = oldName.replace(/\[\d+\]/, '[' + newCounter + ']');
                $(this).attr('name', newName);
            });

            $newRow.find('[id]').each(function () {
                const oldId = $(this).attr('id');
                if (!oldId) return;
                const newId = oldId.replace(/_\d+$/, '_' + newCounter);
                $(this).attr('id', newId);
            });

            $newRow.find('[for]').each(function () {
                const oldFor = $(this).attr('for');
                if (!oldFor) return;
                const newFor = oldFor.replace(/_\d+$/, '_' + newCounter);
                $(this).attr('for', newFor);
            });

            $newRow.find('.item-row-header').html('<i class="fas fa-cube mr-2"></i>รายการที่ ' + (currentCount + 1));

            $newRow.find('select').prop('selectedIndex', 0);
            $newRow.find('input[type="text"], input[type="number"]').val('');
            $newRow.find('input[name*="[amount]"]').val('1');
            $newRow.find('[name*="[ai]"]').val('0');
            $newRow.find('input[type="radio"]').prop('checked', false);
            $newRow.find('input[type="radio"][value="ไม่สลักข้อความบนแท่งเงิน"]').prop('checked', true);

            $newRow.find('.carving-input').attr('readonly', true).val('');
            $newRow.find('.font-select').prop('disabled', true).prop('selectedIndex', 0);
            $newRow.find('.item-total-display').val('0.00');
            $newRow.find('.product-type-select').html('<option value="">-- เลือกสินค้าก่อน --</option>');

            $container.append($newRow);

            this.updateRemoveButtons();
            this.calculateItemTotal($newRow);
            this.calculateTotal();

        },

        initEvents: function () {
            const self = this;

            $(document).off('.multiorder');

            $(document).on('submit.multiorder', 'form[name="form_multi_order"]', function (e) {
                e.preventDefault();
                e.stopPropagation();
                self.add();
                return false;
            });

            $(document).on('click.multiorder', '.btn-submit, #btn-submit', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('form[name="form_multi_order"]').trigger('submit');
                return false;
            });

            $(document).on('click.multiorder', '.btn-add-item, #btn-add-item', function (e) {
                e.preventDefault();
                e.stopPropagation();
                self.addItem();
                return false;
            });

            $(document).on('click.multiorder', '.btn-remove-item', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const now = Date.now();
                if (now - self.__lastRemoveAt < self.__removeCooldownMs) {
                    return false;
                }

                self.__lastRemoveAt = now;

                const $row = $(this).closest('.item-row');
                if (!$row.length) {
                    return false;
                }

                const itemCount = $('.item-row').length;
                if (itemCount <= 1) {
                    alert('ต้องมีรายการสินค้าอย่างน้อย 1 รายการ');
                    return false;
                }

                $row.remove();

                $('.item-row').each(function (index) {
                    $(this).find('.item-row-header').html(
                        '<i class="fas fa-cube mr-2"></i>รายการที่ ' + (index + 1)
                    );
                });

                self.updateRemoveButtons();
                self.calculateTotal();
                return false;
            });

            $(document).on('change.multiorder', '#items-container .product-select', function (e) {
                e.stopPropagation();
                const $row = $(this).closest('.item-row');
                const productId = $(this).val();
                const productTypeSelect = $row.find('.product-type-select');

                if (productId) {
                    $.ajax({
                        type: 'POST',
                        url: 'apps/sales_screen_bwd/xhr/action-load-Type.php',
                        data: 'id=' + productId,
                        success: function (html) {
                            productTypeSelect.html(html);
                        }
                    });
                } else {
                    productTypeSelect.html('<option value="">-- เลือกสินค้าก่อน --</option>');
                }
                self.calculateItemTotal($row);
                self.calculateTotal();
            });

            $(document).on('change.multiorder input.multiorder',
                '#items-container [name*="[product_type]"], ' +
                '#items-container [name*="[amount]"], ' +
                '#items-container [name*="[price]"], ' +
                '#items-container [name*="[discount]"]',
                function (e) {
                    e.stopPropagation();
                    const $row = $(this).closest('.item-row');
                    self.calculateItemTotal($row);
                    self.calculateTotal();
                }
            );

            $(document).on('change.multiorder', '#items-container [name*="[engrave]"]', function (e) {
                e.stopPropagation();
                const $row = $(this).closest('.item-row');
                const isEngrave = $(this).val() === 'สลักข้อความบนแท่งเงิน';
                $row.find('.carving-input').attr('readonly', !isEngrave);
                $row.find('.font-select').prop('disabled', !isEngrave);
                if (!isEngrave) {
                    $row.find('.carving-input').val('');
                    $row.find('.font-select').prop('selectedIndex', 0);
                }
                self.calculateItemTotal($row);
                self.calculateTotal();
            });

            $(document).on('change.multiorder', '#items-container [name*="[ai]"]', function (e) {
                e.stopPropagation();
                const $row = $(this).closest('.item-row');
                const aiValue = $(this).val();
                console.log('🔄 AI changed to:', aiValue);
                self.calculateItemTotal($row);
                self.calculateTotal();
            });

            $(document).on('change.multiorder input.multiorder', '#shipping, #remote_area_fee, #orderable_type, #shipping_address', function () {
                self.calculateTotal();
            });

            $(document).on('change.multiorder', '[name="platform"]', function () {
                $('.item-row').each(function () {
                    self.calculateItemTotal($(this));
                });
                self.calculateTotal();
            });

            $(document).on('change.multiorder input.multiorder', '#fee', function () {
                self.calculateTotal();
            });

            this.bindCustomerSearch();

        },

        bindCustomerSearch: function () {
            const self = this;

            let phoneTimeout = null;
            let usernameTimeout = null;

            $(document).off('input.multiorderCustomer blur.multiorderCustomer keyup.multiorderCustomer', '[name="phone"], [name="username"]');

            $(document).on('input.multiorderCustomer', '[name="phone"]', function () {
                const $input = $(this);
                const phone = $input.val().trim();

                if (phoneTimeout) {
                    clearTimeout(phoneTimeout);
                }

                if (phone.length >= 9) {
                    phoneTimeout = setTimeout(function () {
                        SalesScreenApp.customerManager.searchCustomerByPhone(phone);
                    }, 800);
                }
            });

            $(document).on('blur.multiorderCustomer', '[name="phone"]', function () {
                const phone = $(this).val().trim();


                if (phoneTimeout) {
                    clearTimeout(phoneTimeout);
                }

                if (phone.length >= 9) {
                    SalesScreenApp.customerManager.searchCustomerByPhone(phone);
                }
            });

            $(document).on('keyup.multiorderCustomer', '[name="phone"]', function (e) {
                if (e.keyCode === 13) { 
                    const phone = $(this).val().trim();
                    if (phone.length >= 9) {
                        SalesScreenApp.customerManager.searchCustomerByPhone(phone);
                    }
                }
            });

            $(document).on('input.multiorderCustomer', '[name="username"]', function () {
                const $input = $(this);
                const username = $input.val().trim();


                if (usernameTimeout) {
                    clearTimeout(usernameTimeout);
                }

                if (username.length >= 3) {
                    usernameTimeout = setTimeout(function () {
                        SalesScreenApp.customerManager.searchCustomerByUsername(username);
                    }, 800);
                }
            });

            $(document).on('blur.multiorderCustomer', '[name="username"]', function () {
                const username = $(this).val().trim();

                if (usernameTimeout) {
                    clearTimeout(usernameTimeout);
                }

                if (username.length >= 3) {
                    SalesScreenApp.customerManager.searchCustomerByUsername(username);
                }
            });

            $(document).on('keyup.multiorderCustomer', '[name="username"]', function (e) {
                if (e.keyCode === 13) { 
                    const username = $(this).val().trim();
                    if (username.length >= 3) {
                        SalesScreenApp.customerManager.searchCustomerByUsername(username);
                    }
                }
            });

            setTimeout(function () {
                const $phone = $('[name="phone"]');
                const $username = $('[name="username"]');

                if ($phone.length === 0) {
                    console.error('❌ ERROR: Phone input not found in DOM!');
                }
                if ($username.length === 0) {
                    console.error('❌ ERROR: Username input not found in DOM!');
                }

                if ($phone.length > 0 && $username.length > 0) {
                    console.log('Customer search bindings verified and ready');
                }
            }, 100);
        },

        initDefaults: function () {
            const today = new Date().toISOString().split('T')[0];
            const deliveryDate = new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

            if ($('[name="date"]').val() === '') {
                $('[name="date"]').val(today);
            }
            if ($('[name="delivery_date"]').val() === '') {
                $('[name="delivery_date"]').val(deliveryDate);
            }
            if ($('#shipping').val() === '') {
                $('#shipping').val('auto');
            }

            this.updateRemoveButtons();

            const $firstRow = $('#items-container .item-row:first');
            if ($firstRow.length) {
                this.calculateItemTotal($firstRow);
            }
            this.calculateTotal();
        },

        init: function () {
            const self = this;

            if (!$('form[name="form_multi_order"]').length) {
                console.warn('⚠️ ไม่พบฟอร์ม form_multi_order ในหน้า');
                return;
            }


            this.initEvents();
            this.initDefaults();

            setTimeout(function () {
                const $phone = $('[name="phone"]');
                const $username = $('[name="username"]');

                if ($phone.length > 0) {
                    console.log('✅ Phone input found');
                } else {
                    console.error('❌ Phone input NOT found!');
                }

                if ($username.length > 0) {
                    console.log('✅ Username input found');
                } else {
                    console.error('❌ Username input NOT found!');
                }

                self.bindCustomerSearch();
            }, 500);

        }
    };
    $(function () {
        if (fn.app &&
            fn.app.sales_screen_bwd &&
            fn.app.sales_screen_bwd.multiorder &&
            typeof fn.app.sales_screen_bwd.multiorder.init === 'function') {

            fn.app.sales_screen_bwd.multiorder.init();
        }
    });

})(jQuery, window, document);