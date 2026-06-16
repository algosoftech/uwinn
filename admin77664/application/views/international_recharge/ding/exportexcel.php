<!DOCTYPE html>
<html>
<head>
    <title>Download Ding Recharge Report</title>
    <style>
        .container {
            text-align: center;
            background: #fff;
            padding: 50px;
        }
        .timer {
            font-size: 2em;
            margin: 20px 0;
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Export</a></li>
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
                            <h1>Download Ding Recharge Report</h1>
                            <div class="timer-circle">
                                <span id="time">5</span>
                                <div id="circle"></div>
                            </div>
                            <p>Thanks! Your report download will start in a few seconds…</p>
                            <span style="text-align: center; margin: auto;">If not, <a id="downloadLink" class="download-button" href="#">click here to download the report</a>.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const timeDisplay     = document.getElementById('time');
    const downloadLink    = document.getElementById('downloadLink');
    var currentPage       = <?= json_encode(isset($current_page) ? (int) $current_page : 1); ?>;
    var totalPage         = <?= json_encode(isset($total_page) ? (int) $total_page : 1); ?>;
    var searchField       = <?= json_encode($searchField ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var searchValue       = (function () {
        try {
            return atob(<?= json_encode(base64_encode((string) ($searchValue ?? '')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
        } catch (e) {
            return '';
        }
    })();
    var fromDate          = <?= json_encode($fromDate ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var toDate            = <?= json_encode($toDate ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var cancelled_order   = '';
    if (totalPage < 1) { totalPage = 1; }
    if (currentPage < 1) { currentPage = 1; }
    let timeLeft          = totalPage == 1 ? 5 : 5 * totalPage + 5;
    var allData           = [];
    var responsesReceived = 0;

    function GETDATA() {
        if (currentPage <= totalPage) {
            $.ajax({
                url: "<?= getCurrentControllerPath('exportexcelApi'); ?>",
                type: 'POST',
                data: { pageno: currentPage, searchField: searchField, searchValue: searchValue, fromDate: fromDate, toDate: toDate, cancelled_order: cancelled_order },
                success: function(data) {
                    var chunk = [];
                    try {
                        chunk = (typeof data === 'string') ? JSON.parse(data) : data;
                    } catch (e) {
                        console.error('Export: invalid response', e);
                        chunk = [];
                    }
                    if (!Array.isArray(chunk)) { chunk = []; }
                    allData = allData.concat(chunk);
                    responsesReceived++;
                    if (responsesReceived == totalPage) {
                        clearInterval(intervalId);

                        const curdate = new Date().toISOString().slice(0, 10).replace(/-/g, '-');
                        const filename = 'ding-recharge-history-' + curdate + '.xlsx';
                        const rows = (allData && allData.length) ? allData : [{ 'Message': 'No matching records for selected filters' }];
                        try {
                            const workbook = XLSX.utils.book_new();
                            const worksheet = XLSX.utils.json_to_sheet(rows);
                            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
                            XLSX.writeFile(workbook, filename);
                        } catch (e) {
                            console.error('Export: XLSX.writeFile failed', e);
                        }
                        downloadLink.href = URL.createObjectURL(new Blob([convertToExcel(rows)], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }));
                        downloadLink.download = filename;
                    }
                },
                error: function () {
                    responsesReceived++;
                    if (responsesReceived == totalPage) {
                        clearInterval(intervalId);
                    }
                }
            });
            currentPage++;
        }
    }

    function convertToExcel(data) {
            const array = Array.isArray(data) ? data : (function () { try { return JSON.parse(data); } catch (e) { return []; } })();
            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(array);
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');
            const wbout = XLSX.write(workbook, { bookType: 'xlsx', type: 'binary' });

            function s2ab(s) {
                const buf = new ArrayBuffer(s.length);
                const view = new Uint8Array(buf);
                for (let i = 0; i < s.length; i++) {
                    view[i] = s.charCodeAt(i) & 0xFF;
                }
                return buf;
            }

            return new Blob([s2ab(wbout)], { type: "application/octet-stream" });
        }

    function updateTimer() {
        if (timeLeft > 0) {
            timeLeft--;
            timeDisplay.textContent = timeLeft;
            setTimeout(updateTimer, 1000);
        } else {
            $('.timer-circle').addClass('d-none');
        }
    }

    updateTimer();
    var APIHITTIMEGAP = totalPage > 0 ? (timeLeft / totalPage) * 1000 : 800;
    GETDATA();
    var intervalId = setInterval(GETDATA, APIHITTIMEGAP);
})();
</script>
</body>
</html>
