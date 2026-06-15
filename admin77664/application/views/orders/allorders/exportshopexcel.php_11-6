<!DOCTYPE html>
<html>
<head>
    <title>Download Shop Statement</title>
    <style>
        .container {
            text-align: center;
            background: #fff;
            padding: 50px;
        }
        .timer-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid #0071c5;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            font-size: 1.5em;
            position: relative;
        }
        .timer-circle span {
            position: absolute;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.min.js"></script>
</head>
<body>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title"></div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Export Shop Excel</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="container">
                            <h1>Download Statement</h1>
                            <div class="timer-circle">
                                <span id="time">52</span>
                            </div>
                            <p>Thanks! Your shop excel download will start in few seconds...</p>
                            <span style="text-align: center; margin: auto;">if not, <a id="downloadLink" class="download-button" href="javascript:void(0);">Click here to download report.</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<iframe id="shopExcelDownloadFrame" name="shopExcelDownloadFrame" style="display:none;width:0;height:0;border:0" title="download"></iframe>

<script>
    const timeDisplay = document.getElementById('time');
    const downloadLink = document.getElementById('downloadLink');
    var currentPage = <?=json_encode($current_page);?>;
    var totalPage = <?=json_encode($total_page);?>;
    var searchField = <?=json_encode($searchField);?>;
    var searchValue = <?=json_encode($searchValue);?>;
    var fromDate = <?=json_encode($fromDate);?>;
    var toDate = <?=json_encode($toDate);?>;
    var cancelled_order = <?=json_encode($cancelled_order);?>;
    var productIds = <?=json_encode($productIds);?>;
    var productTitles = <?=json_encode($productTitles);?>;
    var apiUrl = <?=json_encode(getCurrentControllerPath('exportshopexcelApi'));?>;
    var serverDownloadUrl = <?=json_encode(getCurrentControllerPath('exportshopexcelDownload'));?>;
    let timeLeft = totalPage == 1 ? 5 : 5 * totalPage + 5;
    var shopMap = {};
    var responsesReceived = 0;
    var exportFinished = false;
    var lastExportData = [];

    function mergeShopRows(rows) {
        if (!Array.isArray(rows)) {
            return;
        }
        rows.forEach(function(row) {
            var key = row.shopKey;
            if (!key) {
                return;
            }
            if (!shopMap[key]) {
                shopMap[key] = Object.assign({}, row);
                productTitles.forEach(function(title) {
                    if (shopMap[key][title] === undefined || shopMap[key][title] === null || shopMap[key][title] === '') {
                        shopMap[key][title] = 0;
                    }
                });
                return;
            }
            productTitles.forEach(function(title) {
                shopMap[key][title] = (parseInt(shopMap[key][title], 10) || 0) + (parseInt(row[title], 10) || 0);
            });
        });
    }

    function buildExportRows() {
        var rows = Object.values(shopMap);
        rows.sort(function(a, b) {
            var regionCompare = (a.Region || '').localeCompare(b.Region || '');
            if (regionCompare !== 0) {
                return regionCompare;
            }
            return (a['SHOP NAME'] || '').localeCompare(b['SHOP NAME'] || '');
        });

        return rows.map(function(row) {
            var exportRow = {
                'POS': row.POS,
                'SHOP NAME': row['SHOP NAME'],
                'Region': row.Region,
                'Supervisor': row.Supervisor
            };
            productTitles.forEach(function(title) {
                exportRow[title] = parseInt(row[title], 10) || 0;
            });
            return exportRow;
        });
    }

    function getExportFilename() {
        return 'shop-purchase-report-' + new Date().toISOString().slice(0, 10) + '.xlsx';
    }

    function applyHeaderStyles(ws, headers) {
        var staticHeaders = ['POS', 'SHOP NAME', 'Region', 'Supervisor'];
        var productColors = ['FFFF00', '92D050', 'FFC000', 'FF0000', '00B0F0', '7030A0', '00B050', 'C00000', 'FF66CC', '9999FF'];

        headers.forEach(function(header, index) {
            var cellAddress = XLSX.utils.encode_cell({ r: 0, c: index });
            if (!ws[cellAddress]) {
                ws[cellAddress] = { t: 's', v: header };
            }

            var bgColor = '1F3864';
            var fontColor = 'FFFFFF';
            if (staticHeaders.indexOf(header) === -1) {
                bgColor = productColors[(index - 4) % productColors.length];
                fontColor = '000000';
            }

            ws[cellAddress].s = {
                font: { bold: true, sz: 12, color: { rgb: fontColor } },
                fill: { patternType: 'solid', fgColor: { rgb: bgColor } },
                alignment: { horizontal: 'center', vertical: 'center' }
            };
        });

        ws['!cols'] = headers.map(function() { return { wch: 20 }; });
        ws['!rows'] = [{ hpt: 30 }];
    }

    function downloadClientExcel(data, filename) {
        if (typeof XLSX === 'undefined' || !XLSX.utils || !XLSX.writeFile) {
            throw new Error('Excel library not loaded');
        }

        var ws = XLSX.utils.json_to_sheet(data);
        var headers = Object.keys(data[0] || {});
        if (headers.length && typeof ws !== 'undefined') {
            applyHeaderStyles(ws, headers);
        }

        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Shop Purchase');
        XLSX.writeFile(wb, filename);
    }

    function triggerServerDownload() {
        var iframe = document.getElementById('shopExcelDownloadFrame');
        if (!iframe) {
            return;
        }
        var separator = serverDownloadUrl.indexOf('?') >= 0 ? '&' : '?';
        iframe.src = serverDownloadUrl + separator + '_=' + new Date().getTime();
    }

    function finishExport() {
        if (exportFinished) {
            return;
        }
        exportFinished = true;
        clearInterval(intervalId);

        var exportData = buildExportRows();
        if (exportData.length === 0) {
            var emptyRow = { 'POS': '', 'SHOP NAME': '', 'Region': '', 'Supervisor': '' };
            productTitles.forEach(function(title) {
                emptyRow[title] = 0;
            });
            exportData = [emptyRow];
        }

        lastExportData = exportData;
        var filename = getExportFilename();
        var clientOk = false;

        try {
            downloadClientExcel(exportData, filename);
            clientOk = true;
        } catch (err) {
            console.error(err);
        }

        if (!clientOk) {
            triggerServerDownload();
        }
    }

    function parseApiResponse(data) {
        var parsed = typeof data === 'string' ? JSON.parse(data) : data;
        if (parsed && parsed.error) {
            throw new Error(parsed.error);
        }
        if (!Array.isArray(parsed)) {
            throw new Error('Invalid export response');
        }
        return parsed;
    }

    downloadLink.addEventListener('click', function(e) {
        e.preventDefault();
        if (lastExportData.length) {
            try {
                downloadClientExcel(lastExportData, getExportFilename());
            } catch (err) {
                triggerServerDownload();
            }
            return;
        }
        triggerServerDownload();
    });

    function GETDATA() {
        if (currentPage > totalPage) {
            return;
        }

        $.ajax({
            url: apiUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                pageno: currentPage || 1,
                searchField: searchField,
                searchValue: searchValue,
                fromDate: fromDate,
                toDate: toDate,
                cancelled_order: cancelled_order,
                productIds: productIds
            },
            success: function(data) {
                try {
                    var rows = parseApiResponse(data);
                    mergeShopRows(rows);
                    responsesReceived++;
                    if (responsesReceived >= totalPage) {
                        finishExport();
                    }
                } catch (err) {
                    alert(err.message || 'Export data process failed.');
                    clearInterval(intervalId);
                }
            },
            error: function() {
                alert('Failed to load export data. Please try again.');
                clearInterval(intervalId);
            }
        });
        currentPage++;
    }

    function updateTimer() {
        if (timeLeft > 0) {
            timeLeft--;
            timeDisplay.textContent = timeLeft;
            setTimeout(updateTimer, 1000);
        } else {
            var timerCircle = document.querySelector('.timer-circle');
            if (timerCircle) {
                timerCircle.classList.add('d-none');
            }
        }
    }

    updateTimer();
    var APIHITTIMEGAP = Math.max(500, (timeLeft / Math.max(totalPage, 1)) * 1000);
    var intervalId = setInterval(GETDATA, APIHITTIMEGAP);
    GETDATA();
</script>
</body>
</html>
