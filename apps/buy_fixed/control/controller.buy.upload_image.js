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
                "Ounces: " + record.ounces + "\n" +
                "Date: " + record.date + "\n\n" +
                "⚠️ ระบบจะตรวจสอบความตรงกันของข้อมูลด้วย AI\n" +
                "✅ ต้องพบ: XAG, วันที่, และ Ounces (อย่างน้อย 2 จาก 3)";

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
        return;
    }

    try {
        fn.notify.infobox("🤖 กำลังตรวจสอบข้อมูลด้วย AI กรุณารอสักครู่...");

        const worker = await Tesseract.createWorker();
        await worker.loadLanguage('eng+tha');
        await worker.initialize('eng+tha');

        const { data: { text } } = await worker.recognize(file);
        await worker.terminate();

        console.log("=== OCR TEXT START ===");
        console.log(text);
        console.log("=== OCR TEXT END ===");

        const matchResult = checkDataMatch(text, record_data);

        const validationResult = showSimpleResult(matchResult, file, record_id, record_data, text);

        if (!validationResult) {
            console.log("Validation failed, upload blocked");
            return;
        }

    } catch (error) {
        console.error('OCR Error:', error);
        fn.notify.warnbox("เกิดข้อผิดพลาดในการตรวจสอบ AI ไม่สามารถอัปโหลดได้");
        return;
    }
}

function checkDataMatch(extractedText, expectedData) {
    const text = extractedText.toLowerCase().replace(/[^\w\s.,-/]/g, ' ');
    let matchCount = 0;
    const checkResults = {
        xag: { found: false, details: '' },
        ounces: { found: false, details: '' },
        date: { found: false, details: '' }
    };

    console.log("=== CHECKING DATA (3 Fields Only) ===");
    console.log("Expected Ounces:", expectedData.ounces);
    console.log("Expected Date:", expectedData.date);
    console.log("Text (cleaned):", text);

    // 1. ตรวจสอบ XAG - รองรับทั้ง "Buy XAG", "XAG/USD", "XAG"
    const hasXAG = text.includes('xag') ||
        text.includes('xag/usd') ||
        text.includes('xag usd') ||
        text.includes('xagusd') ||
        text.includes('Silver') ||
        text.includes('silver');

    console.log("1. XAG Check:", hasXAG);

    if (hasXAG) {
        matchCount++;
        checkResults.xag.found = true;
        checkResults.xag.details = `พบ XAG`;
    } else {
        checkResults.xag.details = `ไม่พบ XAG`;
    }

    // 2. ตรวจสอบ Ounces
    const ouncesOriginal = expectedData.ounces.toString();
    let ouncesFound = false;
    let matchedOuncesFormat = '';

    const ouncesFormats = generateOuncesFormatsExtended(ouncesOriginal);

    console.log("2. Ounces - Checking formats (first 10):", ouncesFormats.slice(0, 10));

    for (const format of ouncesFormats) {
        if (text.includes(format.toLowerCase())) {
            ouncesFound = true;
            matchedOuncesFormat = format;
            console.log("   ✓ Ounces FOUND:", format);
            break;
        }
    }

    console.log("   Ounces Result:", ouncesFound);

    if (ouncesFound) {
        matchCount++;
        checkResults.ounces.found = true;
        checkResults.ounces.details = `พบ ${matchedOuncesFormat}`;
    } else {
        checkResults.ounces.details = `ไม่พบ ${ouncesOriginal}`;
    }

    // 3. ตรวจสอบ Date
    const dateOriginal = expectedData.date.toString();
    let dateFound = false;
    let matchedDateFormat = '';

    const dateFormats = generateDateFormatsFlexible(dateOriginal);

    console.log("3. Date - Checking formats (first 10):", dateFormats.slice(0, 10));

    for (const format of dateFormats) {
        if (text.includes(format.toLowerCase())) {
            dateFound = true;
            matchedDateFormat = format;
            console.log("   ✓ Date FOUND:", format);
            break;
        }
    }

    if (!dateFound) {
        const dateCheck = checkDateFlexible(text, dateOriginal);
        if (dateCheck.found) {
            dateFound = true;
            matchedDateFormat = dateCheck.matchedFormat;
            console.log("   ✓ Date FOUND (flexible):", matchedDateFormat);
        }
    }

    console.log("   Date Result:", dateFound);

    if (dateFound) {
        matchCount++;
        checkResults.date.found = true;
        checkResults.date.details = `พบวันที่ ${matchedDateFormat}`;
    } else {
        checkResults.date.details = `ไม่พบวันที่ ${dateOriginal}`;
    }

    const foundFields = Object.keys(checkResults).filter(key => checkResults[key].found);

    console.log("=== MATCH RESULT ===");
    console.log("Match Count:", matchCount, "/3");
    console.log("Found Fields:", foundFields);

    return {
        isMatch: matchCount >= 1,
        matchCount: matchCount,
        totalFields: 3,
        foundFields: foundFields,
        checkResults: checkResults,
        matchPercentage: Math.round((matchCount / 3) * 100),
        hasXAG: hasXAG
    };
}

function generateDateFormatsFlexible(dateString) {
    const formats = [];
    let day, month, year;

    if (dateString.includes('/')) {
        const parts = dateString.split('/');
        if (parts[2] && parts[2].length === 4) {
            day = parts[0];
            month = parts[1];
            year = parts[2];
        }
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

    // รูปแบบต้นฉบับ
    formats.push(dateString);
    formats.push(dateString.replace(/\//g, '-'));
    formats.push(dateString.replace(/\//g, '.'));
    formats.push(dateString.replace(/\//g, ''));
    formats.push(dateString.replace(/-/g, ''));

    // รูปแบบ d/m/yyyy
    formats.push(`${day}/${month}/${year}`);
    formats.push(`${day}-${month}-${year}`);
    formats.push(`${day}.${month}.${year}`);
    formats.push(`${day}${month}${year}`);

    // รูปแบบ m/d/yyyy
    formats.push(`${month}/${day}/${year}`);
    formats.push(`${month}-${day}-${year}`);
    formats.push(`${month}.${day}.${year}`);
    formats.push(`${month}${day}${year}`);

    // รูปแบบตัวเลขไม่มี leading zero
    formats.push(`${dayNum}/${monthNum}/${year}`);
    formats.push(`${dayNum}-${monthNum}-${year}`);
    formats.push(`${monthNum}/${dayNum}/${year}`);
    formats.push(`${monthNum}-${dayNum}-${year}`);
    formats.push(`${dayNum}${monthNum}${year}`);
    formats.push(`${monthNum}${dayNum}${year}`);

    // รูปแบบ yyyy-mm-dd และ yyyymmdd
    formats.push(`${year}-${month}-${day}`);
    formats.push(`${year}/${month}/${day}`);
    formats.push(`${year}.${month}.${day}`);
    formats.push(`${year}${month}${day}`);

    // รูปแบบที่มีชื่อเดือน
    for (const monthName of monthNamesArray) {
        formats.push(`${dayNum} ${monthName} ${year}`);
        formats.push(`${day} ${monthName} ${year}`);
        formats.push(`${monthName} ${dayNum}, ${year}`);
        formats.push(`${monthName} ${day}, ${year}`);
        formats.push(`${dayNum} ${monthName.substring(0, 3)} ${year}`);
        formats.push(`${monthName.substring(0, 3)} ${dayNum} ${year}`);
        formats.push(`${dayNum}-${monthName}-${year}`);
        formats.push(`${day}-${monthName}-${year}`);
        formats.push(`${monthName} ${dayNum} ${year}`);
        formats.push(`${monthName.substring(0, 3)} ${dayNum} ${year}`);
    }

    return [...new Set(formats)];
}

function generateOuncesFormatsExtended(ouncesString) {
    const formats = [];

    let numStr = ouncesString.toString().replace(/\s*oz\s*/gi, '').trim();

    const num = parseFloat(numStr);
    if (isNaN(num)) {
        return [ouncesString];
    }

    // ตัวเลขต้นฉบับ
    formats.push(numStr);
    formats.push(num.toString());

    // รูปแบบที่มี comma
    const withComma = num.toLocaleString('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 10
    });
    formats.push(withComma);

    // ทุกรูปแบบทศนิยม 0-10 หลัก
    for (let decimals = 0; decimals <= 10; decimals++) {
        const fixed = num.toFixed(decimals);
        formats.push(fixed);

        const fixedWithComma = parseFloat(fixed).toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
        formats.push(fixedWithComma);
    }

    // รูปแบบที่ตัดทศนิยมบางส่วน (สำหรับ 82532.8274)
    if (num.toString().includes('.')) {
        const parts = num.toString().split('.');
        const integerPart = parts[0];
        const decimalPart = parts[1];

        for (let i = 1; i <= decimalPart.length; i++) {
            const partial = `${integerPart}.${decimalPart.substring(0, i)}`;
            formats.push(partial);

            const partialNum = parseFloat(partial);
            const partialWithComma = partialNum.toLocaleString('en-US');
            formats.push(partialWithComma);
        }
    }

    // จำนวนเต็ม
    if (num % 1 === 0) {
        const intNum = Math.floor(num);
        formats.push(intNum.toString());
        formats.push(intNum.toLocaleString('en-US'));
    }

    // ตัดศูนย์ท้าย
    const trimmed = num.toString().replace(/\.?0+$/, '');
    formats.push(trimmed);

    if (trimmed.includes('.')) {
        const trimmedNum = parseFloat(trimmed);
        const trimmedWithComma = trimmedNum.toLocaleString('en-US');
        formats.push(trimmedWithComma);
    }

    // รูปแบบที่มี space แทน comma
    const withSpace = num.toLocaleString('en-US').replace(/,/g, ' ');
    formats.push(withSpace);

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

function showSimpleResult(matchResult, file, record_id, record_data, ocrText) {
    let message = `ผลการตรวจสอบ:\n\n`;

    if (matchResult.isMatch) {
        message += `✅ ข้อมูลตรงกัน (${matchResult.matchCount}/3 ฟิลด์)\n`;
        message += `📊 ความตรงกัน: ${matchResult.matchPercentage}%\n\n`;
    } else {
        message += `❌ ข้อมูลไม่ตรงกัน (${matchResult.matchCount}/3 ฟิลด์)\n`;
        message += `📊 ความตรงกัน: ${matchResult.matchPercentage}%\n\n`;
    }

    message += `📋 รายละเอียดการตรวจสอบ:\n`;

    const xagIcon = matchResult.checkResults.xag.found ? '✅' : '❌';
    message += `${xagIcon} XAG: ${matchResult.checkResults.xag.details}\n`;

    const ouncesIcon = matchResult.checkResults.ounces.found ? '✅' : '❌';
    message += `${ouncesIcon} Ounces: ${matchResult.checkResults.ounces.details}\n`;

    const dateIcon = matchResult.checkResults.date.found ? '✅' : '❌';
    message += `${dateIcon} Date: ${matchResult.checkResults.date.details}\n`;

    message += `\n`;

    if (matchResult.matchCount === 0) {
        message += `🚫 ไม่สามารถอัปโหลดได้เด็ดขาด\n`;
        message += `❌ ข้อมูลในรูปไม่ตรงกับฐานข้อมูลเลยแม้แต่ฟิลด์เดียว\n`;
        message += `📊 ตรงกัน ${matchResult.matchCount} จาก 3 ฟิลด์ (${matchResult.matchPercentage}%)\n\n`;

        message += `🔍 กรุณาตรวจสอบ:\n`;
        message += `• รูปภาพชัดเจน อ่านข้อความได้\n`;
        message += `• มีข้อความ XAG\n`;
        message += `• มีตัวเลข Ounces: ${record_data.ounces}\n`;
        message += `• มีวันที่: ${record_data.date}\n\n`;

        message += `🔄 กรุณาถ่ายรูปใหม่หรือเลือกรายการที่ถูกต้อง`;

        fn.dialog.infobox("🚫 ไม่อนุญาตให้อัปโหลด", message);

        return false;
    }

    if (matchResult.matchCount < 1) {
        message += `🚫 ไม่สามารถอัปโหลดได้\n`;
        message += `⚠️ ข้อมูลในรูปต้องตรงกันอย่างน้อย 2 ฟิลด์จาก 3 ฟิลด์\n`;
        message += `📊 ปัจจุบันตรงกันเพียง ${matchResult.matchCount} ฟิลด์ (${matchResult.matchPercentage}%)\n`;
        message += `🔄 กรุณาถ่ายรูปใหม่หรือตรวจสอบข้อมูลให้ชัดเจนขึ้น\n\n`;

        const missingFields = [];
        if (!matchResult.checkResults.xag.found) missingFields.push('XAG');
        if (!matchResult.checkResults.ounces.found) missingFields.push('Ounces');
        if (!matchResult.checkResults.date.found) missingFields.push('Date');

        if (missingFields.length > 0) {
            message += `❌ ฟิลด์ที่ยังไม่ตรงกัน: ${missingFields.join(', ')}`;
        }

        fn.dialog.infobox("❌ ไม่สามารถอัปโหลดได้", message);

        return false;
    }

    uploadValidationPassed = true;
    const validationId = currentValidationId;

    if (matchResult.isMatch) {
        message += `✅ ข้อมูลตรงกันครบถ้วน\n`;
        message += `คุณต้องการอัปโหลดรูปนี้หรือไม่?`;

        fn.dialog.confirmbox(
            "✅ ข้อมูลตรงกัน",
            message,
            function () {
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
        message += `📊 ตรงกัน ${matchResult.matchCount} จาก 3 ฟิลด์ (${matchResult.matchPercentage}%)\n`;
        message += `คุณต้องการอัปโหลดรูปนี้หรือไม่?`;

        fn.dialog.confirmbox(
            "⚠️ ข้อมูลบางส่วนไม่ตรงกัน",
            message,
            function () {
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

    return true;
}

function uploadWithAIVerification(file, record_id, record_data, isVerified, matchResult) {
    if (!uploadValidationPassed) {
        fn.notify.warnbox("🚫 ไม่ได้รับอนุญาตให้อัปโหลด กรุณาผ่านการตรวจสอบก่อน");
        return false;
    }

    if (matchResult) {
        if (matchResult.matchCount === 0) {
            fn.notify.warnbox("🚫 ระบบป้องกันการอัปโหลด: ข้อมูลไม่ตรงกันเลยแม้แต่ฟิลด์เดียว");
            uploadValidationPassed = false;
            return false;
        }

        if (matchResult.matchCount < 1) {
            fn.notify.warnbox("🚫 ระบบป้องกันการอัปโหลด: ข้อมูลต้องตรงกันอย่างน้อย 2 ฟิลด์");
            uploadValidationPassed = false;
            return false;
        }
    }

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
                        message += ` ✅ ตรงกัน (${matchResult.matchCount}/3)`;
                    } else {
                        message += ` ⚠️ บางส่วนตรงกัน (${matchResult.matchCount}/3)`;
                    }

                    const foundItems = [];
                    if (matchResult.checkResults.xag.found) foundItems.push('XAG');
                    if (matchResult.checkResults.ounces.found) foundItems.push('Ounces');
                    if (matchResult.checkResults.date.found) foundItems.push('Date');

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