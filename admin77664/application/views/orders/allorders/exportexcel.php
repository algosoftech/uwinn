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
        .export-progress {
            width: min(420px, 90%);
            margin: 30px auto 15px;
        }
        .progress-track {
            height: 28px;
            overflow: hidden;
            background: #e9ecef;
            border-radius: 20px;
        }
        .progress-bar {
            width: 0;
            height: 100%;
            background: #0071c5;
            border-radius: 20px;
            transition: width .3s ease;
        }
        #progressPercent {
            margin-top: 12px;
            font-size: 18px;
            font-weight: 600;
        }
        #progressCount {
            margin: 20px 0 4px;
            color: #0071c5;
            font-size: 42px;
        }
        #downloadLink.disabled {
            color: #999;
            pointer-events: none;
            text-decoration: none;
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
                            <div class="export-progress" id="exportProgress">
                                <div class="progress-track">
                                    <div class="progress-bar" id="progressBar"></div>
                                </div>
                                <div id="progressPercent">0%</div>
                            </div>
                            <p id="progressMessage">Preparing order report, please wait...</p>
                            <div id="progressCount">0</div>
                            <p id="progressDetail">Generating file (0)...</p>
                            <span style="text-align: center; margin: auto;">if not, <a id="downloadLink" class="download-button disabled" href="#">Click here to download report.</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const downloadLink    = document.getElementById('downloadLink');
    const progressBar     = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const progressMessage = document.getElementById('progressMessage');
    const progressCount   = document.getElementById('progressCount');
    const progressDetail  = document.getElementById('progressDetail');
    var currentPage       = <?=json_encode($current_page);?>;
    var totalPage         = <?=json_encode($total_page);?>;
    var searchField       = <?=json_encode($searchField);?>;
    var searchValue       = <?=json_encode($searchValue);?>;
    var fromDate          = <?=json_encode($fromDate);?>;
    var toDate            = <?=json_encode($toDate);?>;
    var cancelled_order   = <?=json_encode($cancelled_order);?>;
    var productIds          = <?=json_encode($productIds);?>;
    var allData           = [];
    var responsesReceived = 0;
    var downloadUrl       = '';
    var downloadFilename  = '';

    function updateProgress(percent, message) {
        percent = Math.max(0, Math.min(100, Math.round(percent)));
        progressBar.style.width = percent + '%';
        progressPercent.textContent = percent + '%';
        if (message) {
            progressMessage.textContent = message;
        }
        progressCount.textContent = responsesReceived;
        progressDetail.textContent = 'Generating file (' + responsesReceived + ' of ' + totalPage + ')...';
    }

    function showError(message) {
        progressMessage.textContent = message || 'Report generation failed. Please try again.';
        progressMessage.style.color = '#dc3545';
        progressDetail.textContent = '';
    }

    function GETDATA() {
        if (currentPage > totalPage) {
            prepareDownload();
            return;
        }

        var requestedPage = currentPage++;
        $.ajax({
            url: "<?=getCurrentControllerPath('exportexcelApi');?>",
            type: 'POST',
            dataType: 'json',
            data: { pageno: requestedPage, searchField: searchField, searchValue: searchValue, fromDate: fromDate, toDate: toDate, cancelled_order: cancelled_order, productIds: productIds },
            success: function(data) {
                if (!Array.isArray(data)) {
                    showError('Invalid response received while preparing the report.');
                    return;
                }

                allData = allData.concat(data);
                responsesReceived++;
                updateProgress((responsesReceived / Math.max(totalPage, 1)) * 90);

                // Load the next page only after the current page completes.
                setTimeout(GETDATA, 100);
            },
            error: function() {
                showError('Failed to load report data. Please try again.');
            }
        });
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

    function prepareDownload() {
        updateProgress(95, 'Generating Excel file, please wait...');

        // Let the browser render 95% before the CPU-heavy Excel conversion starts.
        setTimeout(function() {
            try {
                const curdate = new Date().toISOString().slice(0, 10);
                downloadFilename = 'U-WIN-Data-' + curdate + '.xlsx';
                const excelBlob = convertToExcel(allData);
                downloadUrl = URL.createObjectURL(excelBlob);

                downloadLink.href = downloadUrl;
                downloadLink.download = downloadFilename;
                downloadLink.classList.remove('disabled');

                updateProgress(100, 'Your report is ready. Download is starting...');
                progressDetail.textContent = 'File generated successfully.';
                downloadLink.click();
            } catch (error) {
                console.error(error);
                showError('Excel file generation failed. Please try again.');
            }
        }, 50);
    }

    window.addEventListener('beforeunload', function() {
        if (downloadUrl) {
            URL.revokeObjectURL(downloadUrl);
        }
    });

    updateProgress(0);
    GETDATA();
</script>
</body>
</html>
