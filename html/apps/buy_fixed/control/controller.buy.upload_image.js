// *** Global flag เพื่อติดตามสถานะการตรวจสอบ ***
let uploadValidationPassed = false;
let currentValidationId = null;

fn.app.buy_fixed.buy.upload_image = function (id) {
    console.log("Getting record data for ID:", id);

    $.ajax({
        url: 'apps/buy_fixed/xhr/get-record.php?id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function (record) {
            if (!record || record.error) {
                fn.notify.warnbox(record?.message || "ไม่สามารถโหลดข้อมูลได้");
                return;
            }
            var checkData = "กรุณาตรวจสอบรูปให้ตรงกับข้อมูลนี้:\n\n" +
                "Amount: " + record.amount + "\n" +
                "Ounces: " + record.ounces + "\n" +
                "Date: " + record.date + "\n" +
                "Method: " + record.method + "\n\n" +
                "⚠️ ระบบจะตรวจสอบความตรงกันของข้อมูลด้วย AI";

            if (record.has_image && record.img && record.img !== '*NULL*') {
                checkData += "\n\n🗑️ พบรูปเก่าอยู่แล้ว ระบบจะลบรูปเก่าและแทนที่ด้วยรูปใหม่";
            }
            fn.dialog.confirmbox("ตรวจสอบข้อมูล", checkData, function () {
                selectImageFile(id, record);
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching record:", error);
            fn.notify.warnbox("เกิดข้อผิดพลาดในการโหลดข้อมูล: " + (xhr.responseJSON?.message || error));
        }
    });
};

function selectImageFile(record_id, record_data) {
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
            uploadValidationPassed = false;
            currentValidationId = Date.now() + '_' + Math.random();

            performSimpleVerification(file, record_id, record_data);
        }
    };

    document.body.appendChild(input);
    input.click();
    document.body.removeChild(input);
}

async function performSimpleVerification(file, record_id, record_data) {
    if (typeof Tesseract === 'undefined') {
        fn.notify.warnbox("ระบบ AI ไม่พร้อมใช้งาน ไม่สามารถอัปโหลดได้");
        // *** ไม่อนุญาตให้อัปโหลดเมื่อไม่มี AI ***
        return;
    }

    try {
        fn.notify.infobox("🤖 กำลังตรวจสอบข้อมูลด้วย AI กรุณารอสักครู่...");

        const worker = await Tesseract.createWorker();
        await worker.loadLanguage('eng+tha');
        await worker.initialize('eng+tha');

        const { data: { text } } = await worker.recognize(file);
        await worker.terminate();

        const matchResult = checkDataMatch(text, record_data);

        // *** ตรวจสอบและแสดงผล ***
        const validationResult = showSimpleResult(matchResult, file, record_id, record_data);

        // *** ถ้าการตรวจสอบไม่ผ่าน ไม่ทำอะไรเลย ***
        if (!validationResult) {
            console.log("Validation failed, upload blocked");
            return;
        }

    } catch (error) {
        console.error('OCR Error:', error);
        fn.notify.warnbox("เกิดข้อผิดพลาดในการตรวจสอบ AI ไม่สามารถอัปโหลดได้");
        // *** ไม่อนุญาตให้อัปโหลดเมื่อ OCR error ***
        return;
    }
}

function checkDataMatch(extractedText, expectedData) {
    const text = extractedText.toLowerCase().replace(/[^\w\s.,-]/g, ' ');
    let matchCount = 0;
    const checkResults = {
        amount: { found: false, details: '' },
        ounces: { found: false, details: '' },
        date: { found: false, details: '' },
        method: { found: false, details: '' }
    };

    const hasBuyXAG = text.includes('xag');

    const amountStr = expectedData.amount.toString().replace(/,/g, '');
    const amountDisplay = expectedData.amount.toString();
    const amountFound = text.includes(amountStr.toLowerCase()) || text.includes(amountDisplay.toLowerCase());

    if (amountFound && hasBuyXAG) {
        matchCount++;
        checkResults.amount.found = true;
        checkResults.amount.details = `พบ ${amountDisplay} และ XAG`;
    } else if (amountFound && !hasBuyXAG) {
        checkResults.amount.details = `พบ ${amountDisplay} แต่ไม่พบ XAG`;
    } else if (!amountFound && hasBuyXAG) {
        checkResults.amount.details = `พบ XAG แต่ไม่พบ ${amountDisplay}`;
    } else {
        checkResults.amount.details = `ไม่พบ ${amountDisplay} และ XAG`;
    }

    const ouncesOriginal = expectedData.ounces.toString();
    let ouncesFound = false;
    let matchedOuncesFormat = '';

    const ouncesFormats = generateOuncesFormats(ouncesOriginal);

    for (const format of ouncesFormats) {
        if (text.includes(format.toLowerCase())) {
            ouncesFound = true;
            matchedOuncesFormat = format;
            break;
        }
    }

    const hasOzWord = text.includes('oz') || text.includes('ounce');

    if (ouncesFound && hasBuyXAG) {
        matchCount++;
        checkResults.ounces.found = true;
        checkResults.ounces.details = `พบ ${matchedOuncesFormat} และ XAG`;
    } else if (ouncesFound && !hasBuyXAG) {
        checkResults.ounces.details = `พบ ${matchedOuncesFormat} แต่ไม่พบ XAG`;
    } else if (!ouncesFound && hasBuyXAG && hasOzWord) {
        checkResults.ounces.details = `พบ XAG และ Oz แต่ไม่พบจำนวน ${ouncesOriginal}`;
    } else if (!ouncesFound && hasBuyXAG) {
        checkResults.ounces.details = `พบ XAG แต่ไม่พบ ${ouncesOriginal}`;
    } else {
        checkResults.ounces.details = `ไม่พบ ${ouncesOriginal} และ XAG`;
    }

    const dateOriginal = expectedData.date.toString();
    let dateFound = false;
    let matchedDateFormat = '';

    const dateFormats = generateDateFormats(dateOriginal);

    const fixingTimePatterns = [
        'fixing time',
        'fixing date',
        'fix time',
        'fix date'
    ];

    for (const format of dateFormats) {
        if (text.includes(format.toLowerCase())) {
            dateFound = true;
            matchedDateFormat = format;
            break;
        }
    }

    if (!dateFound) {
        const dateCheck = checkDateFlexible(text, dateOriginal);
        if (dateCheck.found) {
            dateFound = true;
            matchedDateFormat = dateCheck.matchedFormat;
        }
    }

    let hasFixingTime = fixingTimePatterns.some(pattern => text.includes(pattern));

    if (dateFound || hasFixingTime) {
        matchCount++;
        checkResults.date.found = true;
        if (hasFixingTime && dateFound) {
            checkResults.date.details = `พบ Fixing Time และวันที่ ${matchedDateFormat}`;
        } else if (hasFixingTime) {
            checkResults.date.details = `พบ Fixing Time (${dateOriginal})`;
        } else {
            checkResults.date.details = `พบวันที่ ${matchedDateFormat}`;
        }
    } else {
        checkResults.date.details = `ไม่พบวันที่ ${dateOriginal} หรือ Fixing Time`;
    }

    const methodStr = expectedData.method.toString().toLowerCase();
    let methodFound = text.includes(methodStr);

    if (!methodFound && hasFixingTime) {
        const fixingIndex = text.search(/fixing\s*(time|date)/i);
        if (fixingIndex !== -1) {
            const contextStart = Math.max(0, fixingIndex - 50);
            const contextEnd = Math.min(text.length, fixingIndex + 50);
            const context = text.substring(contextStart, contextEnd);

            if (context.includes(methodStr)) {
                methodFound = true;
            }
        }
    }

    if (methodFound) {
        matchCount++;
        checkResults.method.found = true;
        checkResults.method.details = `พบ ${expectedData.method}`;
    } else {
        checkResults.method.details = `ไม่พบ ${expectedData.method}`;
    }

    const foundFields = Object.keys(checkResults).filter(key => checkResults[key].found);

    return {
        isMatch: matchCount >= 2,
        matchCount: matchCount,
        totalFields: 4,
        foundFields: foundFields,
        checkResults: checkResults,
        matchPercentage: Math.round((matchCount / 4) * 100),
        hasFixingTime: hasFixingTime,
        hasBuyXAG: hasBuyXAG
    };
}

function generateDateFormats(dateString) {
    const formats = [];

    let day, month, year;

    if (dateString.includes('/')) {
        const parts = dateString.split('/');
        day = parts[0];
        month = parts[1];
        year = parts[2];
    } else if (dateString.includes('-') && dateString.length === 10) {
        const parts = dateString.split('-');
        year = parts[0];
        month = parts[1];
        day = parts[2];
    } else {
        return [dateString];
    }

    const monthNames = {
        '01': ['jan', 'january'],
        '02': ['feb', 'february'],
        '03': ['mar', 'march'],
        '04': ['apr', 'april'],
        '05': ['may'],
        '06': ['jun', 'june'],
        '07': ['jul', 'july'],
        '08': ['aug', 'august'],
        '09': ['sep', 'september'],
        '10': ['oct', 'october'],
        '11': ['nov', 'november'],
        '12': ['dec', 'december']
    };

    const monthNamesArray = monthNames[month] || [];

    const dayNum = parseInt(day);
    const monthNum = parseInt(month);

    formats.push(dateString);
    formats.push(dateString.replace(/\//g, '-'));
    formats.push(dateString.replace(/\//g, '.'));
    formats.push(dateString.replace(/\//g, ''));

    if (dateString.includes('/')) {
        formats.push(`${year}-${month}-${day}`);
        formats.push(`${year}/${month}/${day}`);
        formats.push(`${year}.${month}.${day}`);
    }

    formats.push(`${dayNum}/${monthNum}/${year}`);
    formats.push(`${dayNum}-${monthNum}-${year}`);
    formats.push(`${dayNum}.${monthNum}.${year}`);

    for (const monthName of monthNamesArray) {
        formats.push(`${dayNum} ${monthName} ${year}`);
        formats.push(`${dayNum}-${monthName}-${year}`);
        formats.push(`${dayNum} ${monthName.substring(0, 3)} ${year}`);
        formats.push(`${monthName} ${dayNum}, ${year}`);
        formats.push(`${monthName.substring(0, 3)} ${dayNum}, ${year}`);
        formats.push(`${monthName} ${dayNum} ${year}`);
        formats.push(`${monthName.substring(0, 3)} ${dayNum} ${year}`);

        formats.push(`${day} ${monthName} ${year}`);
        formats.push(`${day}-${monthName}-${year}`);
        formats.push(`${day} ${monthName.substring(0, 3)} ${year}`);
    }

    return [...new Set(formats)];
}

function generateOuncesFormats(ouncesString) {
    const formats = [];

    let numStr = ouncesString.toString().replace(/\s*oz\s*/gi, '').trim();

    const num = parseFloat(numStr);
    if (isNaN(num)) {
        return [ouncesString];
    }

    formats.push(numStr);
    formats.push(num.toString());

    const withComma = num.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 10
    });
    formats.push(withComma);

    for (let decimals = 0; decimals <= 6; decimals++) {
        const fixed = num.toFixed(decimals);
        formats.push(fixed);

        const fixedWithComma = parseFloat(fixed).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
        formats.push(fixedWithComma);
    }

    if (num % 1 === 0) {
        const intNum = Math.floor(num);
        formats.push(intNum.toString());
        formats.push(intNum.toLocaleString('en-US'));
    }

    const trimmed = num.toString().replace(/\.?0+$/, '');
    formats.push(trimmed);

    if (trimmed.includes('.')) {
        const trimmedNum = parseFloat(trimmed);
        const trimmedWithComma = trimmedNum.toLocaleString('en-US');
        formats.push(trimmedWithComma);
    }

    return [...new Set(formats)];
}

function checkDateFlexible(text, expectedDate) {
    let day, month, year;

    if (expectedDate.includes('/')) {
        const parts = expectedDate.split('/');
        day = parts[0];
        month = parts[1];
        year = parts[2];
    } else if (expectedDate.includes('-') && expectedDate.length === 10) {
        const parts = expectedDate.split('-');
        year = parts[0];
        month = parts[1];
        day = parts[2];
    } else {
        return { found: false, matchedFormat: '' };
    }

    const monthNames = {
        '01': ['jan', 'january', 'มกราคม'],
        '02': ['feb', 'february', 'กุมภาพันธ์'],
        '03': ['mar', 'march', 'มีนาคม'],
        '04': ['apr', 'april', 'เมษายน'],
        '05': ['may', 'พฤษภาคม'],
        '06': ['jun', 'june', 'มิถุนายน'],
        '07': ['jul', 'july', 'กรกฎาคม'],
        '08': ['aug', 'august', 'สิงหาคม'],
        '09': ['sep', 'september', 'กันยายน'],
        '10': ['oct', 'october', 'ตุลาคม'],
        '11': ['nov', 'november', 'พฤศจิกายน'],
        '12': ['dec', 'december', 'ธันวาคม']
    };

    const monthNamesArray = monthNames[month] || [];
    const dayNum = parseInt(day);
    const monthNum = parseInt(month);

    const hasDay = text.includes(day) || text.includes(dayNum.toString());
    const hasYear = text.includes(year);

    let hasMonth = false;
    let foundMonthName = '';

    for (const monthName of monthNamesArray) {
        if (text.includes(monthName.toLowerCase())) {
            hasMonth = true;
            foundMonthName = monthName;
            break;
        }
    }

    if (!hasMonth) {
        hasMonth = text.includes(month) || text.includes(monthNum.toString());
        foundMonthName = month;
    }

    let foundCount = 0;
    if (hasDay) foundCount++;
    if (hasMonth) foundCount++;
    if (hasYear) foundCount++;

    if (foundCount >= 2) {
        let matchedFormat = '';
        if (hasDay && hasMonth && hasYear) {
            matchedFormat = `${dayNum} ${foundMonthName} ${year}`;
        } else if (hasDay && hasMonth) {
            matchedFormat = `${dayNum} ${foundMonthName}`;
        } else if (hasMonth && hasYear) {
            matchedFormat = `${foundMonthName} ${year}`;
        } else if (hasDay && hasYear) {
            matchedFormat = `${dayNum}/${year}`;
        }

        return { found: true, matchedFormat: matchedFormat };
    }

    return { found: false, matchedFormat: '' };
}

function showSimpleResult(matchResult, file, record_id, record_data) {
    let message = `ผลการตรวจสอบ:\n\n`;

    if (matchResult.isMatch) {
        message += `✅ ข้อมูลตรงกัน (${matchResult.matchCount}/4 ฟิลด์)\n`;
        message += `📊 ความตรงกัน: ${matchResult.matchPercentage}%\n\n`;
    } else {
        message += `❌ ข้อมูลไม่ตรงกัน (${matchResult.matchCount}/4 ฟิลด์)\n`;
        message += `📊 ความตรงกัน: ${matchResult.matchPercentage}%\n\n`;
    }

    if (matchResult.hasBuyXAG) {
        message += `🥇 พบข้อความ "XAG" ในรูปภาพ\n\n`;
    } else {
        message += `⚠️ ไม่พบข้อความ "XAG" ในรูปภาพ\n\n`;
    }

    message += `📋 รายละเอียดการตรวจสอบ:\n`;

    const amountIcon = matchResult.checkResults.amount.found ? '✅' : '❌';
    message += `${amountIcon} Amount: ${matchResult.checkResults.amount.details}\n`;

    const ouncesIcon = matchResult.checkResults.ounces.found ? '✅' : '❌';
    message += `${ouncesIcon} Ounces: ${matchResult.checkResults.ounces.details}\n`;

    const dateIcon = matchResult.checkResults.date.found ? '✅' : '❌';
    message += `${dateIcon} Date: ${matchResult.checkResults.date.details}\n`;

    const methodIcon = matchResult.checkResults.method.found ? '✅' : '❌';
    message += `${methodIcon} Method: ${matchResult.checkResults.method.details}\n`;

    if (matchResult.hasFixingTime) {
        message += `\n🕐 พบข้อความ "Fixing Time" ในรูปภาพ\n`;
    }

    message += `\n`;

    // *** การตรวจสอบแบบเข้มงวด - ถ้าข้อมูลไม่ตรงกันเลย ไม่ให้อัพโหลดเด็ดขาด ***

    // เงื่อนไข 1: ต้องมีข้อมูลตรงกันอย่างน้อย 1 ฟิลด์
    if (matchResult.matchCount === 0) {
        message += `🚫 ไม่สามารถอัปโหลดได้เด็ดขาด\n`;
        message += `❌ ข้อมูลในรูปไม่ตรงกับฐานข้อมูลเลยแม้แต่ฟิลด์เดียว\n`;
        message += `📊 ตรงกัน ${matchResult.matchCount} จาก 4 ฟิลด์ (${matchResult.matchPercentage}%)\n\n`;

        message += `🔍 กรุณาตรวจสอบ:\n`;
        message += `• รูปภาพชัดเจน อ่านข้อความได้\n`;
        message += `• เป็นใบเสร็จที่ถูกต้อง\n`;
        message += `• ข้อมูลในรูปตรงกับรายการที่เลือก\n\n`;

        // แสดงข้อมูลที่ควรจะพบในรูป
        message += `📋 ข้อมูลที่ต้องพบในรูป:\n`;
        message += `• Amount: ${record_data.amount}\n`;
        message += `• Ounces: ${record_data.ounces}\n`;
        message += `• Date: ${record_data.date}\n`;
        message += `• Method: ${record_data.method}\n\n`;

        message += `🔄 กรุณาถ่ายรูปใหม่หรือเลือกรายการที่ถูกต้อง`;

        fn.dialog.infobox("🚫 ไม่อนุญาตให้อัปโหลด", message);

        // *** หยุดการทำงานที่นี่ - ไม่ให้ไปต่อ ***
        return false;
    }

    // เงื่อนไข 2: ต้องมีข้อมูลตรงกันอย่างน้อย 2 ฟิลด์
    if (matchResult.matchCount < 2) {
        message += `🚫 ไม่สามารถอัปโหลดได้\n`;
        message += `⚠️ ข้อมูลในรูปต้องตรงกันอย่างน้อย 2 ฟิลด์จาก 4 ฟิลด์\n`;
        message += `📊 ปัจจุบันตรงกันเพียง ${matchResult.matchCount} ฟิลด์ (${matchResult.matchPercentage}%)\n`;
        message += `🔄 กรุณาถ่ายรูปใหม่หรือตรวจสอบข้อมูลให้ชัดเจนขึ้น`;

        // แสดงฟิลด์ที่ยังไม่ตรงกัน
        const missingFields = [];
        if (!matchResult.checkResults.amount.found) missingFields.push('Amount');
        if (!matchResult.checkResults.ounces.found) missingFields.push('Ounces');
        if (!matchResult.checkResults.date.found) missingFields.push('Date');
        if (!matchResult.checkResults.method.found) missingFields.push('Method');

        if (missingFields.length > 0) {
            message += `\n❌ ฟิลด์ที่ยังไม่ตรงกัน: ${missingFields.join(', ')}`;
        }

        fn.dialog.infobox("❌ ไม่สามารถอัปโหลดได้", message);

        // *** หยุดการทำงานที่นี่ - ไม่ให้ไปต่อ ***
        return false;
    }

    // ผ่านการตรวจสอบแล้ว - อนุญาตให้อัปโหลด
    uploadValidationPassed = true;
    const validationId = currentValidationId;

    if (matchResult.isMatch) {
        message += `✅ ข้อมูลตรงกันครบถ้วน\n`;
        message += `คุณต้องการอัปโหลดรูปนี้หรือไม่?`;

        fn.dialog.confirmbox(
            "✅ ข้อมูลตรงกัน",
            message,
            function () {
                // *** ตรวจสอบ validation ID ก่อนอัปโหลด ***
                if (uploadValidationPassed && currentValidationId === validationId) {
                    uploadWithAIVerification(file, record_id, record_data, true, matchResult);
                } else {
                    fn.notify.warnbox("🚫 การตรวจสอบหมดอายุ กรุณาเลือกรูปใหม่");
                }
            },
            function () {
                uploadValidationPassed = false;
                fn.notify.infobox("ยกเลิกการอัปโหลด");
            }
        );
    } else {
        message += `⚠️ ข้อมูลบางส่วนไม่ตรงกัน แต่ผ่านเกณฑ์ขั้นต่ำแล้ว\n`;
        message += `📊 ตรงกัน ${matchResult.matchCount} จาก 4 ฟิลด์ (${matchResult.matchPercentage}%)\n`;
        message += `คุณต้องการอัปโหลดรูปนี้หรือไม่?`;

        fn.dialog.confirmbox(
            "⚠️ ข้อมูลบางส่วนไม่ตรงกัน",
            message,
            function () {
                // *** ตรวจสอบ validation ID ก่อนอัปโหลด ***
                if (uploadValidationPassed && currentValidationId === validationId) {
                    uploadWithAIVerification(file, record_id, record_data, false, matchResult);
                } else {
                    fn.notify.warnbox("🚫 การตรวจสอบหมดอายุ กรุณาเลือกรูปใหม่");
                }
            },
            function () {
                uploadValidationPassed = false;
                fn.notify.infobox("ยกเลิกการอัปโหลด");
            }
        );
    }

    // *** ส่งค่ากลับเพื่อบอกว่าผ่านการตรวจสอบแล้ว ***
    return true;
}

function uploadWithAIVerification(file, record_id, record_data, isVerified, matchResult) {
    // *** เพิ่มการตรวจสอบซ้ำก่อนอัปโหลด ***
    if (!uploadValidationPassed) {
        fn.notify.warnbox("🚫 ไม่ได้รับอนุญาตให้อัปโหลด กรุณาผ่านการตรวจสอบก่อน");
        return false;
    }

    if (matchResult) {
        // ตรวจสอบเงื่อนไขอีกครั้งก่อนอัปโหลด
        if (matchResult.matchCount === 0) {
            fn.notify.warnbox("🚫 ระบบป้องกันการอัปโหลด: ข้อมูลไม่ตรงกันเลยแม้แต่ฟิลด์เดียว");
            uploadValidationPassed = false;
            return false;
        }

        if (matchResult.matchCount < 2) {
            fn.notify.warnbox("🚫 ระบบป้องกันการอัปโหลด: ข้อมูลต้องตรงกันอย่างน้อย 2 ฟิลด์");
            uploadValidationPassed = false;
            return false;
        }
    }

    // *** Reset validation state เมื่อเริ่มอัปโหลด ***
    uploadValidationPassed = false;
    currentValidationId = null;

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
        url: 'apps/buy_fixed/xhr/upload-image.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percent = Math.round((evt.loaded / evt.total) * 100);
                    // แสดง progress ถ้าต้องการ
                }
            }, false);
            return xhr;
        },
        success: function (response) {
            if (response.success) {
                var message = response.message;

                if (hasOldImage && response.old_image_deleted) {
                    message += "\n🗑️ ลบรูปเก่าเรียบร้อยแล้ว";
                }

                if (matchResult) {
                    message += `\n\n🤖 AI Verification:`;
                    if (isVerified) {
                        message += ` ✅ ตรงกัน (${matchResult.matchCount}/4)`;
                        if (matchResult.hasBuyXAG) {
                            message += ` 🥇 XAG`;
                        }
                        if (matchResult.hasFixingTime) {
                            message += ` 🕐 Fixing Time`;
                        }
                    } else {
                        message += ` ❌ ไม่ตรงกัน (${matchResult.matchCount}/4)`;
                        if (matchResult.hasBuyXAG) {
                            message += ` 🥇 XAG`;
                        } else {
                            message += ` ⚠️ ไม่มี XAG`;
                        }
                    }

                    const foundItems = [];
                    if (matchResult.checkResults.amount.found) foundItems.push('Amount');
                    if (matchResult.checkResults.ounces.found) foundItems.push('Ounces');
                    if (matchResult.checkResults.date.found) foundItems.push('Date');
                    if (matchResult.checkResults.method.found) foundItems.push('Method');

                    if (foundItems.length > 0) {
                        message += `\n✅ พบ: ${foundItems.join(', ')}`;
                    }
                }

                if (isVerified) {
                    fn.notify.successbox(message);
                } else {
                    fn.notify.infobox(message);
                }

                if (typeof window.onImageUploadSuccess === 'function') {
                    window.onImageUploadSuccess(record_id);
                } else {
                    setTimeout(function () {
                        refreshPurchaseTable();
                    }, 500);
                }
            } else {
                fn.notify.warnbox(response.message || "เกิดข้อผิดพลาด");
            }
        },
        error: function (xhr, status, error) {
            console.log("Upload error:", error);
            fn.notify.warnbox("เกิดข้อผิดพลาดในการอัปโหลด: " + error);
        }
    });

    return true;
}

function refreshPurchaseTable() {
    if (typeof window.onImageUploadSuccess === 'function') {
        return;
    }

    if ($("#tblPurchase").length && typeof $("#tblPurchase").DataTable === 'function') {
        try {
            $("#tblPurchase").DataTable().ajax.reload(null, false);
            return;
        } catch (e) {
            console.log("DataTable reload failed:", e);
        }
    }

    if (typeof fn.app.buy_fixed.buy.loadTable === 'function') {
        fn.app.buy_fixed.buy.loadTable();
        return;
    }

    console.log("No refresh method found, consider page reload");
}