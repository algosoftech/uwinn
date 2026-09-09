<!DOCTYPE html>
<html>
<head>
    <title>Download Combined Statement</title>
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
        .timer-circle.is-done {
            border-color: #28a745;
            color: #28a745;
        }
        .timer-circle.is-waiting {
            font-size: 1.1em;
        }
        #statusText.error-text {
            color: #dc3545;
        }
        .date-info {
            margin-top: 12px;
            font-size: 0.95em;
            color: #555;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Export Combined Excel</a></li>
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
                            <h1>Download Combined Report</h1>
                            <div class="timer-circle" id="timerCircle">
                                <span id="time"><?= (int)($total_page == 1 ? 5 : (5 * $total_page + 5)) ?></span>
                                <div id="circle"></div>
                            </div>
                            <p id="statusText">Preparing combined report (Hourly winners + Big Winners)...</p>
                            <p class="date-info">
                                Date range: <?=htmlspecialchars($voucher_from_display ?? '')?> to <?=htmlspecialchars($voucher_to_display ?? '')?>
                                <br>
                                Expected Hourly winners: <strong><?= (int)($hourly_count ?? 0) ?></strong>
                                &nbsp;|&nbsp; Expected Big Winners: <strong><?= (int)($big_winners_count ?? $lotto_count ?? 0) ?></strong>
                            </p>
                            <span style="text-align: center; margin: auto;">if download does not start,
                                <a id="downloadLink" class="download-button" href="javascript:void(0)">click here to download report</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var timeDisplay = document.getElementById('time');
        var timerCircle = document.getElementById('timerCircle');
        var statusText = document.getElementById('statusText');
        var downloadLink = document.getElementById('downloadLink');

        var apiUrl = <?=json_encode($api_url ?? '')?>;
        var hourlyTotalPage = <?=json_encode((int)($hourly_total_page ?? 0))?>;
        var bigWinnersTotalPage = <?=json_encode((int)($big_winners_total_page ?? $lotto_total_page ?? 0))?>;
        var hourlyExpectedCount = <?=json_encode((int)($hourly_count ?? 0))?>;
        var bigWinnersExpectedCount = <?=json_encode((int)($big_winners_count ?? $lotto_count ?? 0))?>;
        var totalPage = <?=json_encode((int)($total_page ?? 1))?>;
        var fromDate = <?=json_encode($fromDate ?? '')?>;
        var toDate = <?=json_encode($toDate ?? '')?>;
        var searchField = <?=json_encode($searchField ?? '')?>;
        var searchValue = <?=json_encode($searchValue ?? '')?>;
        var cancelled_order = <?=json_encode($cancelled_order ?? '')?>;

        var downloadComplete = false;
        var fetchStarted = false;
        var waitingSeconds = 0;
        var timeLeft = totalPage == 1 ? 5 : (5 * totalPage + 5);

        var hourlyData = [];
        var bigWinnersData = [];
        var hourlyCurrentPage = 1;
        var bigWinnersCurrentPage = 1;

        function setStatus(message, isError) {
            if (statusText) {
                statusText.textContent = message;
                statusText.classList.toggle('error-text', !!isError);
            }
        }

        function markDownloadComplete(hourlyCount, bigWinnersCount, totalRows) {
            downloadComplete = true;
            setStatus('Download complete. Total rows: ' + totalRows +
                ' (Hourly winners: ' + hourlyCount + ', Big Winners: ' + bigWinnersCount + ').');
            if (timeDisplay) {
                timeDisplay.textContent = 'Done';
            }
            if (timerCircle) {
                timerCircle.classList.add('is-done');
                timerCircle.classList.remove('is-waiting');
            }
        }

        var combinedExportHeaders = [
            'SL.NO', 'ORDER ID', 'RETAILER', 'POS NUMBER', 'DRAW DATE',
            'GAME NAME', 'PRIZE MONEY', 'PURCHASE DATE', 'AREA', 'BIND WITH'
        ];

        function isMeaningfulRow(row) {
            if (!row || typeof row !== 'object') {
                return false;
            }
            return Object.keys(row).some(function (key) {
                if (key === 'Sl.No' || key === 'SL.NO') {
                    return false;
                }
                var val = row[key];
                if (val === undefined || val === null) {
                    return false;
                }
                var str = String(val).trim();
                return str !== '' && str !== 'N/A' && str !== '0' && str !== '0.00';
            });
        }

        function normalizeExportGameName(name) {
            var n = String(name || '').replace(/\s+/g, ' ').trim();
            if (!n || n.toUpperCase() === 'N/A') {
                return 'N/A';
            }
            if (/^4\s*you$/i.test(n)) {
                return '4YOU';
            }
            var compact = n.toLowerCase().replace(/[\s_\-]+/g, '').replace(/plus|＋/g, '+');
            if (compact === 'max3+1' || compact === 'max31' || compact === 'max3plus1') {
                return 'MAX3+1';
            }
            return n;
        }

        function backfillGameNamesByBatch(rows) {
            var batchCounts = {};
            (rows || []).forEach(function (row) {
                var batchId = row && row['BATCH ID'] !== undefined && row['BATCH ID'] !== null
                    ? String(row['BATCH ID']).trim()
                    : '';
                var gameName = normalizeExportGameName(
                    row && row['GAME NAME'] !== undefined && row['GAME NAME'] !== null
                        ? String(row['GAME NAME']).trim()
                        : ''
                );
                if (!batchId || !gameName || gameName.toUpperCase() === 'N/A') {
                    return;
                }
                if (!batchCounts[batchId]) {
                    batchCounts[batchId] = {};
                }
                batchCounts[batchId][gameName] = (batchCounts[batchId][gameName] || 0) + 1;
            });

            var batchBest = {};
            Object.keys(batchCounts).forEach(function (batchId) {
                var bestName = '';
                var bestCount = -1;
                Object.keys(batchCounts[batchId]).forEach(function (gameName) {
                    if (batchCounts[batchId][gameName] > bestCount) {
                        bestCount = batchCounts[batchId][gameName];
                        bestName = gameName;
                    }
                });
                if (bestName) {
                    batchBest[batchId] = bestName;
                }
            });

            return (rows || []).map(function (row) {
                var next = Object.assign({}, row);
                var batchId = next['BATCH ID'] !== undefined && next['BATCH ID'] !== null
                    ? String(next['BATCH ID']).trim()
                    : '';
                // Force entire batch onto majority game name (matches Voucher CSV batch)
                if (batchId && batchBest[batchId]) {
                    next['GAME NAME'] = batchBest[batchId];
                } else if (next['GAME NAME']) {
                    next['GAME NAME'] = normalizeExportGameName(next['GAME NAME']);
                }
                return next;
            });
        }

        function dedupeRowsByOrderId(rows) {
            var seen = {};
            var out = [];
            (rows || []).forEach(function (row) {
                var docId = row && row['DOC ID'] !== undefined && row['DOC ID'] !== null
                    ? String(row['DOC ID']).trim()
                    : '';
                var voucherId = row && row['VOUCHER ID'] !== undefined && row['VOUCHER ID'] !== null
                    ? String(row['VOUCHER ID']).trim()
                    : '';
                var code = row && row['MATCH CODE'] !== undefined && row['MATCH CODE'] !== null
                    ? String(row['MATCH CODE']).trim()
                    : '';
                var orderId = row && row['ORDER ID'] !== undefined && row['ORDER ID'] !== null
                    ? String(row['ORDER ID']).trim()
                    : '';
                var batchId = row && row['BATCH ID'] !== undefined && row['BATCH ID'] !== null
                    ? String(row['BATCH ID']).trim()
                    : '';
                var prize = row && row['PRIZE MONEY'] !== undefined && row['PRIZE MONEY'] !== null
                    ? String(row['PRIZE MONEY']).trim()
                    : '';
                var key;
                if (docId) {
                    key = 'd:' + docId;
                } else if (voucherId && voucherId !== '0') {
                    // voucher_id is unique per winner — keep every prize line
                    key = 'v:' + voucherId;
                } else {
                    key = ('o:' + batchId + '|' + orderId + '|' + code + '|' + prize).toLowerCase();
                }
                if (seen[key]) {
                    return;
                }
                seen[key] = true;
                out.push(row);
            });
            return out;
        }

        function mergeExportRows(hourly, bigWinners) {
            // Big winners first so dedupe keeps lotto row on order_id collision
            var allRows = dedupeRowsByOrderId(
                backfillGameNamesByBatch((bigWinners || []).concat(hourly || []))
            ).filter(isMeaningfulRow);
            if (!allRows.length) {
                return [{'No data': ''}];
            }
            allRows.sort(function (a, b) {
                return (parseFloat(b['PRIZE MONEY']) || 0) - (parseFloat(a['PRIZE MONEY']) || 0);
            });
            return allRows.map(function (row, index) {
                var normalized = {'SL.NO': index + 1};
                combinedExportHeaders.forEach(function (header) {
                    if (header === 'SL.NO') {
                        return;
                    }
                    var raw = row[header] !== undefined && row[header] !== null ? row[header] : '';
                    if (header === 'PRIZE MONEY') {
                        var amount = parseFloat(String(raw).replace(/,/g, ''));
                        normalized[header] = isNaN(amount) ? 0 : amount;
                    } else if (header === 'POS NUMBER') {
                        var pos = parseInt(String(raw).replace(/,/g, ''), 10);
                        normalized[header] = isNaN(pos) ? raw : pos;
                    } else if (header === 'GAME NAME') {
                        // Keep as plain text (MAX3+1 must not become Excel formula / blank)
                        normalized[header] = normalizeExportGameName(raw);
                    } else {
                        normalized[header] = raw;
                    }
                });
                return normalized;
            });
        }

        function getCombinedExportFilename() {
            var datePart = '';
            if (fromDate) {
                var match = String(fromDate).match(/^(\d{4}-\d{2}-\d{2})/);
                if (match) {
                    datePart = match[1];
                }
            }
            if (!datePart) {
                datePart = new Date().toISOString().slice(0, 10);
            }
            return 'Big Winners ' + datePart + '.xlsx';
        }

        function buildExcelFile() {
            if (typeof XLSX === 'undefined') {
                setStatus('Download failed: Excel library not loaded.', true);
                return;
            }
            setStatus('Building Excel file...');
            try {
                var combinedRows = mergeExportRows(hourlyData, bigWinnersData);
                var workbook = XLSX.utils.book_new();
                var sheet = XLSX.utils.json_to_sheet(combinedRows);
                // Force GAME NAME column to text so values like MAX3+1 stay visible in filters
                var gameNameCol = null;
                Object.keys(sheet).forEach(function (addr) {
                    if (!/^[A-Z]+1$/.test(addr)) {
                        return;
                    }
                    var cell = sheet[addr];
                    if (cell && String(cell.v) === 'GAME NAME') {
                        gameNameCol = addr.replace(/1$/, '');
                    }
                });
                if (gameNameCol) {
                    Object.keys(sheet).forEach(function (addr) {
                        if (addr.indexOf(gameNameCol) !== 0 || addr === gameNameCol + '1') {
                            return;
                        }
                        if (!new RegExp('^' + gameNameCol + '[0-9]+$').test(addr)) {
                            return;
                        }
                        var cell = sheet[addr];
                        if (!cell) {
                            return;
                        }
                        cell.t = 's';
                        cell.v = String(cell.v == null ? '' : cell.v);
                        delete cell.w;
                        delete cell.f;
                    });
                }
                XLSX.utils.book_append_sheet(workbook, sheet, 'Combined Report');
                XLSX.writeFile(workbook, getCombinedExportFilename());
                markDownloadComplete(hourlyData.length, bigWinnersData.length, combinedRows.length);
            } catch (error) {
                setStatus('Excel build failed: ' + (error.message || error), true);
            }
        }

        function buildAjaxData(source, pageNo) {
            return {
                source: source,
                pageno: pageNo,
                fromDate: fromDate,
                toDate: toDate,
                searchField: searchField,
                searchValue: searchValue,
                cancelled_order: cancelled_order
            };
        }

        function fetchApiPage(source, pageNo) {
            return $.ajax({
                url: apiUrl,
                type: 'POST',
                dataType: 'json',
                timeout: 300000,
                data: buildAjaxData(source, pageNo)
            }).then(function (data) {
                if (data && data.error) {
                    throw new Error(data.error);
                }
                return Array.isArray(data) ? data : [];
            });
        }

        function fetchBigWinnersPage() {
            if (bigWinnersTotalPage < 1 || bigWinnersCurrentPage > bigWinnersTotalPage) {
                buildExcelFile();
                return;
            }

            var label = 'Loading Big Winners... page ' + bigWinnersCurrentPage + '/' + bigWinnersTotalPage +
                ' (loaded ' + bigWinnersData.length + ')';
            setStatus(label);

            fetchApiPage('big_winners', bigWinnersCurrentPage).then(function (rows) {
                bigWinnersData = bigWinnersData.concat(rows);
                bigWinnersCurrentPage++;
                if (bigWinnersCurrentPage > bigWinnersTotalPage) {
                    buildExcelFile();
                    return;
                }
                fetchBigWinnersPage();
            }).fail(function (xhr) {
                var detail = xhr.responseText ? xhr.responseText.substring(0, 180) : 'Request failed';
                setStatus('Failed to load Big Winners: ' + detail, true);
            });
        }

        function fetchHourlyPage() {
            if (hourlyCurrentPage > hourlyTotalPage) {
                bigWinnersCurrentPage = 1;
                fetchBigWinnersPage();
                return;
            }

            var hourlyLabel = hourlyTotalPage > 0
                ? ('Loading Hourly winners... page ' + hourlyCurrentPage + '/' + hourlyTotalPage)
                : 'Loading Hourly winners...';
            setStatus(hourlyLabel);

            fetchApiPage('hourly', hourlyCurrentPage).then(function (rows) {
                hourlyData = hourlyData.concat(rows);
                hourlyCurrentPage++;
                fetchHourlyPage();
            }).fail(function (xhr) {
                var detail = xhr.responseText ? xhr.responseText.substring(0, 180) : 'Request failed';
                setStatus('Failed to load Hourly winners: ' + detail, true);
            });
        }

        function startPaginatedExport() {
            if (fetchStarted || !apiUrl) {
                return;
            }
            fetchStarted = true;
            hourlyData = [];
            bigWinnersData = [];
            hourlyCurrentPage = 1;
            bigWinnersCurrentPage = 1;
            fetchHourlyPage();
        }

        function updateTimer() {
            if (downloadComplete) {
                return;
            }

            if (fetchStarted) {
                waitingSeconds++;
                if (timeDisplay) {
                    timeDisplay.textContent = waitingSeconds;
                }
                if (timerCircle) {
                    timerCircle.classList.add('is-waiting');
                }
            } else if (timeLeft > 0) {
                if (timeDisplay) {
                    timeDisplay.textContent = timeLeft;
                }
                timeLeft--;
            } else {
                startPaginatedExport();
            }

            setTimeout(updateTimer, 1000);
        }

        if (downloadLink) {
            downloadLink.addEventListener('click', function (e) {
                e.preventDefault();
                if (!downloadComplete) {
                    fetchStarted = false;
                    timeLeft = 0;
                    startPaginatedExport();
                }
            });
        }

        if (hourlyExpectedCount === 0 && bigWinnersExpectedCount === 0) {
            setStatus('Warning: No Hourly winners or Big Winners for this date range.');
        } else if (hourlyExpectedCount === 0) {
            setStatus('Note: No Hourly winners for this date range. Big Winners will still export.');
        } else if (bigWinnersExpectedCount === 0) {
            setStatus('Note: No Big Winners for this date range. Hourly winners will still export.');
        }

        updateTimer();
    })();
</script>
</body>
</html>
