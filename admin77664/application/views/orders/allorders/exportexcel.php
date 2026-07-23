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
        #progressMessage.error-text {
            color: #dc3545;
        }
        #downloadLink.disabled {
            color: #999;
            pointer-events: none;
            text-decoration: none;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                            <p id="progressDetail">Generating file (0 of <?= (int)($total_page ?? 0) ?>)...</p>
                            <span style="text-align: center; margin: auto;">if not, <a id="downloadLink" class="download-button disabled" href="javascript:void(0);">Click here to download report.</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<iframe id="exportDownloadFrame" name="exportDownloadFrame" style="display:none;width:0;height:0;border:0" title="download"></iframe>

<script>
    (function () {
        var progressBar = document.getElementById('progressBar');
        var progressPercent = document.getElementById('progressPercent');
        var progressMessage = document.getElementById('progressMessage');
        var progressCount = document.getElementById('progressCount');
        var progressDetail = document.getElementById('progressDetail');
        var downloadLink = document.getElementById('downloadLink');
        var downloadFrame = document.getElementById('exportDownloadFrame');

        var totalPage = <?=json_encode((int)($total_page ?? 1));?>;
        var progressApiUrl = <?=json_encode($progress_api_url ?? '');?>;
        var serverDownloadUrl = <?=json_encode($server_download_url ?? '');?>;

        var pollTimer = null;
        var downloadStarted = false;
        var downloadFinished = false;

        function updateProgressView(data) {
            var percent = parseInt(data.percent, 10);
            if (isNaN(percent)) {
                percent = 0;
            }

            progressBar.style.width = percent + '%';
            progressPercent.textContent = percent + '%';
            progressCount.textContent = data.current_page || 0;

            if (data.total_page) {
                progressDetail.textContent = 'Generating file (' + (data.current_page || 0) + ' of ' + data.total_page + ')...';
            }

            if (data.message) {
                progressMessage.textContent = data.message;
            }

            if (data.status === 'error') {
                progressMessage.classList.add('error-text');
                stopPolling();
            }

            if (data.status === 'done') {
                downloadFinished = true;
                progressMessage.classList.remove('error-text');
                downloadLink.classList.remove('disabled');
                stopPolling();
            }
        }

        function pollProgress() {
            if (!progressApiUrl) {
                return;
            }

            $.ajax({
                url: progressApiUrl,
                type: 'GET',
                dataType: 'json',
                cache: false,
                success: function (data) {
                    if (!data || typeof data !== 'object') {
                        return;
                    }
                    updateProgressView(data);
                }
            });
        }

        function startPolling() {
            pollProgress();
            pollTimer = setInterval(pollProgress, 1000);
        }

        function stopPolling() {
            if (pollTimer) {
                clearInterval(pollTimer);
                pollTimer = null;
            }
        }

        function triggerDownload() {
            if (!serverDownloadUrl || downloadStarted) {
                return;
            }
            downloadStarted = true;
            downloadLink.classList.remove('disabled');
            downloadFrame.src = serverDownloadUrl + (serverDownloadUrl.indexOf('?') >= 0 ? '&' : '?') + '_=' + new Date().getTime();
        }

        downloadLink.addEventListener('click', function (e) {
            e.preventDefault();
            triggerDownload();
        });

        downloadFrame.addEventListener('load', function () {
            if (!downloadStarted) {
                return;
            }
            setTimeout(function () {
                if (!downloadFinished) {
                    updateProgressView({
                        status: 'done',
                        percent: 100,
                        current_page: totalPage,
                        total_page: totalPage,
                        message: 'Your report is ready. Download complete.'
                    });
                }
            }, 1500);
        });

        updateProgressView({
            status: 'pending',
            percent: 0,
            current_page: 0,
            total_page: totalPage,
            message: 'Preparing order report, please wait...'
        });
        startPolling();
        triggerDownload();
    })();
</script>
</body>
</html>
