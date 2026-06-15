<!DOCTYPE html>
<html>
<head>
    <title>Download Statement</title>
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Export CSV</a></li>
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
                                <div id="circle"></div>
                            </div>
                            <p>Thanks! Your statement download will start in few seconds...</p>
                            <span style="text-align: center; margin: auto;">if not, <a id="downloadLink" class="download-button" href="#">Click here to download report.</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const timeDisplay     = document.getElementById('time');
    const downloadLink    = document.getElementById('downloadLink');
    var currentPage       = <?=json_encode($current_page);?>;
    var totalPage         = <?=json_encode($total_page);?>;
    var searchField       = <?=json_encode($searchField);?>;
    var searchValue       = <?=json_encode($searchValue);?>;
    var fromDate          = <?=json_encode($fromDate);?>;
    var toDate            = <?=json_encode($toDate);?>;
    var cancelled_order   = <?=json_encode($cancelled_order);?>;
    var productIds          = <?=json_encode($productIds);?>;
    let timeLeft          = totalPage == 1 ? 5 : 5 * totalPage + 5;
    var allData           = [];
    var responsesReceived = 0;

    function GETDATA() {
        if (currentPage <= totalPage) {
            $.ajax({
                url: "<?=getCurrentControllerPath('exportexcelApi');?>",  
                type: 'POST',
                data: { pageno: currentPage || 1, searchField: searchField, searchValue: searchValue, fromDate: fromDate, toDate: toDate , cancelled_order :cancelled_order, productIds: productIds },
                success: function(data) {
                    allData = allData.concat(JSON.parse(data)); // Store the data
                    responsesReceived++;
                    if (responsesReceived == totalPage) {
                        clearInterval(intervalId);

                        const curdate = new Date().toISOString().slice(0, 10).replace(/-/g, '-');
                        const filename = 'U-WIN-Data-' + curdate + '.xlsx';
                        downloadCSV(allData, filename);

                        downloadLink.href = URL.createObjectURL(new Blob([convertToExcel(allData)], { type: 'text/xlsx' }));
                        downloadLink.download = filename;
                    }
                } 
            });
            currentPage++;
        }
    }

    // function convertToCSV(data) {
    //     const array = Array.isArray(data) ? data : JSON.parse(data);
    //     let str = '';

    //     // Extract keys (headers)
    //     let headers = Object.keys(array[0]).join(',');
    //     str += headers + '\r\n';

    //     // Extract values
    //     for (let i = 0; i < array.length; i++) {
    //         let line = '';
    //         for (let index in array[i]) {
    //             if (line !== '') line += ',';
    //             line += array[i][index];
    //         }
    //         str += line + '\r\n';
    //     }

    //     return str;
    // }

    function convertToExcel(data) {
            const array = Array.isArray(data) ? data : JSON.parse(data);
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

    async function downloadCSV(data, filename) {
        /* dynamically import the scripts in the event listener */
          const XLSX = await import("https://cdn.sheetjs.com/xlsx-0.20.3/package/xlsx.mjs");
          const cptable = await import("https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/cpexcel.full.mjs");
          XLSX.set_cptable(cptable);
          const ws = XLSX.utils.json_to_sheet(data);
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
          XLSX.writeFile(wb, filename);
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
    var APIHITTIMEGAP = (timeLeft / totalPage) * 1000;
    var intervalId = setInterval(GETDATA, APIHITTIMEGAP);
</script>
</body>
</html>
