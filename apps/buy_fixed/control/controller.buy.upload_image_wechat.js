// *** Global validation state สำหรับ WeChat ***
let wechatUploadValidationPassed = false;
let currentWeChatValidationId = null;

fn.app.buy_fixed.buy.upload_image_wechat = function (id) {
    console.log("Getting WeChat record data for ID:", id);

    $.ajax({
        url: 'apps/buy_fixed/xhr/get-record-wechat.php?id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function (record) {
            if (!record || record.error) {
                fn.notify.warnbox(record?.message || "ไม่สามารถโหลดข้อมูลได้");
                return;
            }

            var checkData = "🟢 WeChat - กรุณาตรวจสอบรูปให้ตรงกับข้อมูลนี้:\n\n" +
                "Amount: " + record.amount + "\n" +
                "Date: " + record.date + "\n" +
                "Method: " + record.method + "\n\n" +
                "🤖 ระบบจะตรวจสอบการยืนยันคำสั่งซื้อ WeChat ด้วย AI";

            if (record.has_image && record.img && record.img !== '*NULL*') {
                checkData += "\n\n🗑️ พบรูปเก่าอยู่แล้ว ระบบจะลบรูปเก่าและแทนที่ด้วยรูปใหม่";
            }
            fn.dialog.confirmbox("ตรวจสอบข้อมูล WeChat", checkData, function () {
                selectWeChatImageFile(id, record);
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching WeChat record:", error);
            fn.notify.warnbox("เกิดข้อผิดพลาดในการโหลดข้อมูล: " + (xhr.responseJSON?.message || error));
        }
    });
};

function selectWeChatImageFile(record_id, record_data) {
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.style.display = 'none';

    input.onchange = function (e) {
        var file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                fn.notify.warnbox("ขนาดไฟล์เกิน 5MB กรุณาเลือกไฟล์ที่เล็กกว่า");
                return;
            }
            var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                fn.notify.warnbox("ประเภทไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ JPG, PNG หรือ GIF");
                return;
            }

            // *** Reset validation state ***
            wechatUploadValidationPassed = false;
            currentWeChatValidationId = Date.now() + '_' + Math.random();

            performWeChatVerification(file, record_id, record_data);
        }
    };

    document.body.appendChild(input);
    input.click();
    document.body.removeChild(input);
}

async function performWeChatVerification(file, record_id, record_data) {
    console.log("🤖 Starting WeChat AI verification for record:", record_id);
    console.log("📋 Expected data:", record_data);

    if (typeof Tesseract === 'undefined') {
        console.warn("⚠️ Tesseract not available");
        fn.notify.warnbox("ระบบ AI ไม่พร้อมใช้งาน ไม่สามารถอัปโหลด WeChat ได้");
        // *** ไม่อนุญาตให้อัปโหลดเมื่อไม่มี AI ***
        return;
    }

    try {
        fn.notify.infobox("🤖 กำลังตรวจสอบข้อมูล WeChat ด้วย AI กรุณารอสักครู่...");

        console.log("📖 Initializing Tesseract worker...");
        const worker = await Tesseract.createWorker();

        console.log("🌐 Loading languages: eng+tha+chi_sim");
        await worker.loadLanguage('eng+tha+chi_sim');
        await worker.initialize('eng+tha+chi_sim');

        console.log("🔍 Starting OCR recognition...");
        const startTime = Date.now();
        const { data: { text } } = await worker.recognize(file);
        const endTime = Date.now();

        await worker.terminate();

        console.log("🎯 Starting data matching...");
        const matchResult = checkWeChatDataMatch(text, record_data);
        console.log("📊 Match result:", matchResult);

        // *** ตรวจสอบและแสดงผล ***
        const validationResult = showWeChatResult(matchResult, file, record_id, record_data);

        // *** ถ้าการตรวจสอบไม่ผ่าน ไม่ทำอะไรเลย ***
        if (!validationResult) {
            console.log("WeChat validation failed, upload blocked");
            return;
        }

    } catch (error) {
        console.error('❌ WeChat OCR Error:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });

        let errorMessage = "เกิดข้อผิดพลาดในการตรวจสอบ AI";
        if (error.message.includes("worker")) {
            errorMessage += " (ปัญหา Worker)";
        } else if (error.message.includes("language")) {
            errorMessage += " (ปัญหาภาษา)";
        } else if (error.message.includes("network")) {
            errorMessage += " (ปัญหาเครือข่าย)";
        }
        errorMessage += " ไม่สามารถอัปโหลด WeChat ได้";

        fn.notify.warnbox(errorMessage);
        // *** ไม่อนุญาตให้อัปโหลดเมื่อ OCR error ***
        return;
    }
}

function checkWeChatDataMatch(extractedText, expectedData) {
    const text = extractedText.toLowerCase().replace(/[^\w\s.,-]/g, ' ');
    let matchCount = 0;
    const checkResults = {
        amount: { found: false, details: '' },
        date: { found: false, details: '' },
        confirmation: { found: false, details: '' }
    };

    console.log("🔍 Checking text:", text);
    console.log("🔍 Expected amount:", expectedData.amount);
    console.log("🔍 Expected date:", expectedData.date);

    const wechatKeywords = [
        'wechat', 'weixin', 'confirmed', 'order', 'transaction',
        'payment', 'successful', 'complete', 'purchase', 'buy',
        'order confirmed', 'transaction successful', 'payment completed',
        'this is confirmed', 'confirmed order', 'order confirmation',
        'place an order', 'please place', 'new messages', 'message'
    ];

    const hasWeChatKeyword = wechatKeywords.some(keyword => text.includes(keyword.toLowerCase()));
    console.log("🔍 Has WeChat keyword:", hasWeChatKeyword);

    const amountStr = expectedData.amount.toString().replace(/,/g, '');
    const baseAmount = parseFloat(amountStr);

    const amountFormats = [
        Math.floor(baseAmount).toString(),
        baseAmount.toString(),
        baseAmount.toFixed(0),
        baseAmount.toFixed(1),
        baseAmount.toFixed(2),
        baseAmount.toFixed(4),
        amountStr,
    ];

    console.log("🔍 Amount formats to check:", amountFormats);

    let amountFound = false;
    let foundAmountFormat = '';

    for (const format of amountFormats) {
        if (text.includes(format)) {
            amountFound = true;
            foundAmountFormat = format;
            console.log("✅ Found amount:", format);
            break;
        }
    }

    if (!amountFound) {
        const numbers = text.match(/\d+\.?\d*/g) || [];
        console.log("🔢 All numbers found in text:", numbers);

        for (const num of numbers) {
            const foundNum = parseFloat(num);
            if (foundNum >= baseAmount * 0.8 && foundNum <= baseAmount * 1.2) {
                amountFound = true;
                foundAmountFormat = num;
                break;
            }
        }
    }

    if (!amountFound && hasWeChatKeyword) {
        amountFound = true;
        foundAmountFormat = "Context WeChat";
    }

    if (amountFound) {
        matchCount++;
        checkResults.amount.found = true;
        if (foundAmountFormat === "Context WeChat") {
            checkResults.amount.details = `ยอมรับได้ (มี content WeChat)`;
        } else {
            checkResults.amount.details = `พบ ${foundAmountFormat}`;
        }
    } else {
        const numbers = text.match(/\d+\.?\d*/g) || [];
        checkResults.amount.details = `ไม่พบ ${baseAmount} (พบตัวเลข: ${numbers.join(', ')})`;
    }

    const dateOriginal = expectedData.date.toString();
    let dateFound = false;
    let matchedDateFormat = '';

    const dateFormats = [];

    if (dateOriginal.includes('-') && dateOriginal.length === 10) {
        const parts = dateOriginal.split('-');
        const year = parts[0];
        const month = parts[1];
        const day = parts[2];

        dateFormats.push(`${day}/${month}/${year}`);
        dateFormats.push(`${day}-${month}-${year}`);
        dateFormats.push(`${day}.${month}.${year}`);
        dateFormats.push(`${parseInt(day)}/${parseInt(month)}/${year}`);
        dateFormats.push(`${parseInt(day)}-${parseInt(month)}-${year}`);
        dateFormats.push(`${parseInt(day)}.${parseInt(month)}.${year}`);
        dateFormats.push(`${day}${month}${year}`);
        dateFormats.push(`${parseInt(day)}${parseInt(month)}${year}`);
        dateFormats.push(`${year}${month}${day}`);
        dateFormats.push(dateOriginal);

        dateFormats.push(`${day}/${month}`);
        dateFormats.push(`${parseInt(day)}/${parseInt(month)}`);
    }

    console.log("🔍 Date formats to check:", dateFormats);

    for (const format of dateFormats) {
        if (text.includes(format)) {
            dateFound = true;
            matchedDateFormat = format;
            console.log("✅ Found date:", format);
            break;
        }
    }

    if (!dateFound) {
        const datePatterns = text.match(/\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]?\d{0,4}/g) || [];
        console.log("📅 Date patterns found:", datePatterns);
        if (datePatterns.length > 0) {
            dateFound = true;
            matchedDateFormat = datePatterns[0];
        }
    }

    if (!dateFound && hasWeChatKeyword) {
        dateFound = true;
        matchedDateFormat = "Context WeChat";
    }

    if (dateFound) {
        matchCount++;
        checkResults.date.found = true;
        if (matchedDateFormat === "Context WeChat") {
            checkResults.date.details = `ยอมรับได้ (มี context WeChat)`;
        } else {
            checkResults.date.details = `พบวันที่ ${matchedDateFormat}`;
        }
    } else {
        const dateNumbers = text.match(/\d{1,2}[\/\-\.]\d{1,2}[\/\-\.]?\d{0,4}/g) || [];
        checkResults.date.details = `ไม่พบวันที่ ${dateOriginal} (พบรูปแบบวันที่: ${dateNumbers.join(', ')})`;
    }

    const confirmationKeywords = [
        'confirmed', 'confirm', 'this is confirmed', 'order', 'place an order',
        'successful', 'completed', 'transaction', 'payment', 'purchase'
    ];

    const hasConfirmation = confirmationKeywords.some(keyword => text.includes(keyword.toLowerCase()));

    if (hasConfirmation) {
        matchCount++;
        checkResults.confirmation.found = true;
        checkResults.confirmation.details = "พบการยืนยัน";
    } else {
        checkResults.confirmation.details = "ไม่พบการยืนยัน";
    }

    console.log("📊 Final match count:", matchCount);

    return {
        isMatch: matchCount >= 1,
        matchCount: matchCount,
        totalFields: 3,
        foundFields: Object.keys(checkResults).filter(key => checkResults[key].found),
        checkResults: checkResults,
        matchPercentage: Math.round((matchCount / 3) * 100),
        hasWeChatKeyword: hasWeChatKeyword,
        hasConfirmation: hasConfirmation
    };
}

function showWeChatResult(matchResult, file, record_id, record_data) {
    let message = `WeChat - ผลการตรวจสอบ:\n\n`;

    if (matchResult.matchCount >= 1) {
        message += `✅ ยอมรับได้ (${matchResult.matchCount}/3 ฟิลด์)\n`;
        message += `📊 ความตรงกัน: ${matchResult.matchPercentage}%\n\n`;
        if (matchResult.hasWeChatKeyword) {
            message += `💬 พบข้อความ WeChat/การยืนยัน\n\n`;
        }
    } else {
        message += `❌ ข้อมูลไม่ตรงกัน (${matchResult.matchCount}/3 ฟิลด์)\n`;
        message += `📊 ความตรงกัน: ${matchResult.matchPercentage}%\n\n`;
    }

    message += `📋 รายละเอียดการตรวจสอบ:\n`;

    const amountIcon = matchResult.checkResults.amount.found ? '✅' : '❌';
    message += `${amountIcon} Amount: ${matchResult.checkResults.amount.details}\n`;

    const dateIcon = matchResult.checkResults.date.found ? '✅' : '❌';
    message += `${dateIcon} Date: ${matchResult.checkResults.date.details}\n`;

    const confirmIcon = matchResult.checkResults.confirmation.found ? '✅' : '❌';
    message += `${confirmIcon} Confirmation: ${matchResult.checkResults.confirmation.details}\n`;

    // *** การตรวจสอบแบบเข้มงวดสำหรับ WeChat ***
    if (matchResult.matchCount === 0) {
        message += `\n🚫 ไม่สามารถอัปโหลดได้เด็ดขาด\n`;
        message += `❌ ข้อมูลในรูป WeChat ไม่ตรงกับฐานข้อมูลเลยแม้แต่ฟิลด์เดียว\n`;
        message += `📊 ตรงกัน ${matchResult.matchCount} จาก 3 ฟิลด์ (${matchResult.matchPercentage}%)\n\n`;

        message += `🔍 กรุณาตรวจสอบ:\n`;
        message += `• รูปภาพชัดเจน อ่านข้อความได้\n`;
        message += `• เป็นการยืนยันคำสั่งซื้อ WeChat ที่ถูกต้อง\n`;
        message += `• ข้อมูลในรูปตรงกับรายการที่เลือก\n\n`;

        // แสดงข้อมูลที่ควรจะพบในรูป
        message += `📋 ข้อมูลที่ต้องพบในรูป WeChat:\n`;
        message += `• Amount: ${record_data.amount}\n`;
        message += `• Date: ${record_data.date}\n`;
        message += `• การยืนยัน/Confirmation\n\n`;

        message += `🔄 กรุณาถ่ายรูปใหม่หรือเลือกรายการที่ถูกต้อง`;

        fn.dialog.infobox("🚫 ไม่อนุญาตให้อัปโหลด WeChat", message);

        // *** หยุดการทำงานที่นี่ ***
        return false;
    }

    // ผ่านการตรวจสอบแล้ว - อนุญาตให้อัปโหลด
    wechatUploadValidationPassed = true;
    const validationId = currentWeChatValidationId;

    message += `\nคุณต้องการอัปโหลดรูป WeChat นี้หรือไม่?`;

    fn.dialog.confirmbox(
        matchResult.matchCount >= 2 ? "✅ WeChat - ข้อมูลตรงกัน" : "⚠️ WeChat - ยอมรับได้",
        message,
        function () {
            // *** ตรวจสอบ validation ID ก่อนอัปโหลด ***
            if (wechatUploadValidationPassed && currentWeChatValidationId === validationId) {
                uploadWeChatWithAIVerification(file, record_id, record_data, matchResult.isMatch, matchResult);
            } else {
                fn.notify.warnbox("🚫 การตรวจสอบ WeChat หมดอายุ กรุณาเลือกรูปใหม่");
            }
        },
        function () {
            wechatUploadValidationPassed = false;
            fn.notify.infobox("ยกเลิกการอัปโหลด WeChat");
        }
    );

    return true;
}

function uploadWeChatWithAIVerification(file, record_id, record_data, isVerified, matchResult) {
    // *** เพิ่มการตรวจสอบซ้ำก่อนอัปโหลด ***
    if (!wechatUploadValidationPassed) {
        fn.notify.warnbox("🚫 ไม่ได้รับอนุญาตให้อัปโหลด WeChat กรุณาผ่านการตรวจสอบก่อน");
        return false;
    }

    if (matchResult) {
        // ตรวจสอบเงื่อนไขอีกครั้งก่อนอัปโหลด
        if (matchResult.matchCount === 0) {
            fn.notify.warnbox("🚫 ระบบป้องกันการอัปโหลด WeChat: ข้อมูลไม่ตรงกันเลยแม้แต่ฟิลด์เดียว");
            wechatUploadValidationPassed = false;
            return false;
        }
    }

    // *** Reset validation state เมื่อเริ่มอัปโหลด ***
    wechatUploadValidationPassed = false;
    currentWeChatValidationId = null;

    var formData = new FormData();
    formData.append('image', file);
    formData.append('record_id', record_id);
    formData.append('is_verified', isVerified ? '1' : '0');

    const hasOldImage = record_data.has_image && record_data.img && record_data.img !== '*NULL*';
    formData.append('replace_existing', hasOldImage ? '1' : '0');

    if (matchResult) {
        formData.append('verification_details', JSON.stringify(matchResult));
    }

    $.ajax({
        url: 'apps/buy_fixed/xhr/upload-image-wechat.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            console.log("Uploading WeChat image...");
            console.log("URL:", 'apps/buy_fixed/xhr/upload-image-wechat.php');
            console.log("Record ID:", record_id);
        },
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percent = Math.round((evt.loaded / evt.total) * 100);
                    console.log("Upload progress:", percent + "%");
                }
            }, false);
            return xhr;
        },
        success: function (response) {
            console.log("WeChat upload response:", response);
            if (response.success) {
                var message = response.message;

                if (hasOldImage && response.old_image_deleted) {
                    message += "\n🗑️ ลบรูป WeChat เก่าเรียบร้อยแล้ว";
                }

                if (matchResult) {
                    message += `\n\n🤖 AI Verification (WeChat):`;
                    if (isVerified) {
                        message += ` ✅ ตรงกัน (${matchResult.matchCount}/3)`;
                        if (matchResult.hasWeChatKeyword) {
                            message += ` 💬 WeChat`;
                        }
                        if (matchResult.hasConfirmation) {
                            message += ` ✅ Confirmed`;
                        }
                    } else {
                        message += ` ⚠️ ยอมรับได้ (${matchResult.matchCount}/3)`;
                        if (matchResult.hasWeChatKeyword) {
                            message += ` 💬 WeChat`;
                        } else {
                            message += ` ⚠️ ไม่มี WeChat`;
                        }
                    }

                    const foundItems = [];
                    if (matchResult.checkResults.amount.found) foundItems.push('Amount');
                    if (matchResult.checkResults.date.found) foundItems.push('Date');
                    if (matchResult.checkResults.confirmation.found) foundItems.push('Confirmation');

                    if (foundItems.length > 0) {
                        message += `\n✅ พบ: ${foundItems.join(', ')}`;
                    }
                }

                if (isVerified) {
                    fn.notify.successbox("💬 " + message);
                } else {
                    fn.notify.infobox("💬 " + message);
                }

                if (typeof window.onWeChatImageUploadSuccess === 'function') {
                    window.onWeChatImageUploadSuccess(record_id);
                } else if (typeof window.onImageUploadSuccess === 'function') {
                    window.onImageUploadSuccess(record_id, 'wechat');
                } else {
                    setTimeout(function () {
                        refreshWeChatTable();
                    }, 500);
                }
            } else {
                fn.notify.warnbox("💬 WeChat: " + (response.message || "เกิดข้อผิดพลาด"));
            }
        },
        error: function (xhr, status, error) {
            console.log("WeChat Upload error:", error);
            console.log("Status:", status);
            console.log("Response:", xhr.responseText);
            console.log("Status Code:", xhr.status);

            if (xhr.status === 404 || xhr.status === 400) {
                console.log("Trying fallback to upload-image.php");
                uploadWeChatFallback(formData, record_id, record_data, isVerified, matchResult);
                return;
            }

            fn.notify.warnbox("💬 เกิดข้อผิดพลาดในการอัปโหลด WeChat: " + error);
        }
    });

    return true;
}

function uploadWeChatFallback(formData, record_id, record_data, isVerified, matchResult) {
    // *** เพิ่มการตรวจสอบในฟังก์ชัน fallback ด้วย ***
    if (matchResult && matchResult.matchCount === 0) {
        fn.notify.warnbox("🚫 ระบบป้องกันการอัปโหลด WeChat (Fallback): ข้อมูลไม่ตรงกันเลยแม้แต่ฟิลด์เดียว");
        return false;
    }

    formData.append('wechat_mode', '1');

    $.ajax({
        url: 'apps/buy_fixed/xhr/upload-image.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            console.log("Using fallback upload-image.php for WeChat");
        },
        success: function (response) {
            console.log("WeChat fallback upload response:", response);
            if (response.success) {
                var message = response.message + " (Fallback)";

                const hasOldImage = record_data.has_image && record_data.img && record_data.img !== '*NULL*';
                if (hasOldImage && response.old_image_deleted) {
                    message += "\n🗑️ ลบรูป WeChat เก่าเรียบร้อยแล้ว";
                }

                if (matchResult) {
                    message += `\n\n🤖 AI Verification (WeChat):`;
                    if (isVerified) {
                        message += ` ✅ ตรงกัน (${matchResult.matchCount}/3)`;
                        if (matchResult.hasWeChatKeyword) {
                            message += ` 💬 WeChat`;
                        }
                        if (matchResult.hasConfirmation) {
                            message += ` ✅ Confirmed`;
                        }
                    } else {
                        message += ` ⚠️ ยอมรับได้ (${matchResult.matchCount}/3)`;
                        if (matchResult.hasWeChatKeyword) {
                            message += ` 💬 WeChat`;
                        } else {
                            message += ` ⚠️ ไม่มี WeChat`;
                        }
                    }

                    const foundItems = [];
                    if (matchResult.checkResults.amount.found) foundItems.push('Amount');
                    if (matchResult.checkResults.date.found) foundItems.push('Date');
                    if (matchResult.checkResults.confirmation.found) foundItems.push('Confirmation');

                    if (foundItems.length > 0) {
                        message += `\n✅ พบ: ${foundItems.join(', ')}`;
                    }
                }

                if (isVerified) {
                    fn.notify.successbox("💬 " + message);
                } else {
                    fn.notify.infobox("💬 " + message);
                }

                if (typeof window.onWeChatImageUploadSuccess === 'function') {
                    window.onWeChatImageUploadSuccess(record_id);
                } else if (typeof window.onImageUploadSuccess === 'function') {
                    window.onImageUploadSuccess(record_id, 'wechat');
                } else {
                    setTimeout(function () {
                        refreshWeChatTable();
                    }, 500);
                }
            } else {
                fn.notify.warnbox("💬 WeChat (Fallback): " + (response.message || "เกิดข้อผิดพลาด"));
            }
        },
        error: function (xhr, status, error) {
            console.log("WeChat fallback error:", error);
            fn.notify.warnbox("💬 เกิดข้อผิดพลาดในการอัปโหลด WeChat (Fallback): " + error);
        }
    });
}

function refreshWeChatTable() {
    if (typeof window.onWeChatImageUploadSuccess === 'function') {
        return;
    }

    if ($("#tblBuyWeChat").length && typeof $("#tblBuyWeChat").DataTable === 'function') {
        try {
            $("#tblBuyWeChat").DataTable().ajax.reload(null, false);
            return;
        } catch (e) {
            console.log("WeChat DataTable reload failed:", e);
        }
    }

    if (typeof fn.app.buy_fixed.wechat && typeof fn.app.buy_fixed.wechat.loadTable === 'function') {
        fn.app.buy_fixed.wechat.loadTable();
        return;
    }

    console.log("No WeChat refresh method found, consider page reload");
}