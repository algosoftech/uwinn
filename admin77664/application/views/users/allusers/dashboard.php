<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    .dashboard {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .grid-card {
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.4);
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .grid-card:hover {
        transform: translateY(-5px);
    }

    .grid-card h3 {
        margin: 0 0 10px;
        font-size: 16px;
        color: #37474f;
    }

    .grid-card .value {
        font-size: 28px;
        font-weight: bold;
        color: #37474f;
    }

    .small-text {
        font-size: 12px;
        color: #37474f;
        margin-top: 5px;
    }

    .unread {
        background-color: #b53030;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 12px;
        margin-left: 5px;
    }

    .modal-dialog {
        max-width: 100%;
        margin: 0% 0.5% 0% 17%;
    }

    .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    .dataTables-wrapper {
        overflow-x: auto;
    }

    #userTable {
        width: 100% !important;
    }

    .dataTables_scrollBody {
        max-height: 300px !important;
        overflow-y: auto !important;
    }

    .modal-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .modal-header label {
        font-size: 14px;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0;
            max-width: 100%;
        }
        
        .dashboard {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- DataTables Buttons Extension -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script> 
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- Required dependencies for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('allbtcusers/index');?>">Users</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>User Activity Dashboard</h5>
                        <p><?= htmlspecialchars($userdata['users_name'] . ' ( ' . $userdata['users_email'] . ' )') ?></p>
                    </div>
                    <div class="card-body">
                        <div class="dashboard">
                            <div class="grid-card" id="ticketCard" data-toggle="modal" data-target="#ticketModal">
                                <h3>Total Tickets</h3>
                                <div class="value"><?= $total_ticket ?></div>
                                <div class="small-text">Tickets purchased • Click to view history</div>
                            </div>

                            <div class="grid-card">
                                <h3>Total Spent</h3>
                                <div class="value"><?= $totalspent ?></div>
                                <div class="small-text">All time spending</div>
                            </div>

                            <div class="grid-card" id="winnerCard" data-toggle="modal" data-target="#winnerModal">
                                <h3>Total Winnings</h3>
                                <div class="value"><?= $total_winn == '' ? 0 : $total_winn ?></div>
                                <div class="small-text">Prize winnings • Click to view history</div>
                            </div>

                            <div class="grid-card">
                                <h3>Balance</h3>
                                <div class="value"><?= $userdata['availableArabianPoints'] == '' ? 0 : $userdata['availableArabianPoints'] ?></div>
                                <div class="small-text">Current balance</div>
                            </div>

                             <div class="grid-card" id="activeCard" data-toggle="modal" data-target="#activeModal">
                                <h3>Active Draws</h3>
                                <div class="value"><?= $activeticketcount == '' ? 0 : $activeticketcount ?></div>
                                <div class="small-text">Participating in • Click to view details</div>
                            </div>

                            <div class="grid-card" id="notificationCard" data-toggle="modal" data-target="#notificationModal">
                                <h3>Notifications</h3>
                                <div class="value"><?= $totalread + $totalunread ?> <span class="unread"><?= $totalunread ?> unread</span></div>
                                <div class="small-text">Click to view details</div>
                            </div>

                            <div class="grid-card" id="topupCard" data-toggle="modal" data-target="#topupModal">
                                <h3>Voucher Topups</h3>
                                <div class="value"><?= $voucher_topups == '' ? 0 : $voucher_topups ?></div>
                                <div class="small-text">Total vouchers redeemed • Click to view history</div>
                            </div>

                            <div class="grid-card" id="transferwalletCard" data-toggle="modal" data-target="#transferwalletModal">
                                <h3>Transfer Wallet</h3>
                                <div class="value"><?= $transfer_wallet == '' ? 0 : $transfer_wallet ?></div>
                                <div class="small-text">Prize money transfers • Click to view history</div>
                            </div>

                            <div class="grid-card" id="onlinepurchaseCard" data-toggle="modal" data-target="#onlinepurchaseModal">
                                <h3>Online Purchases</h3>
                                <div class="value"><?= $online_purchase == '' ? 0 : $online_purchase ?></div>
                                <div class="small-text">Total store purchases • Click to view history</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Modal -->
<div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ticketModalLabel">Ticket Purchase History</h5>
                <label><?= htmlspecialchars($userdata['users_name'] . ' ( ' . $userdata['users_email'] . ' )') ?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="ticketFilterDate" class="form-label fw-semibold">Filter by Date:</label>
                        <input type="date" id="ticketFilterDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button id="ticketExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="ticketTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>TICKET</th>
                            <th>DRAW NAME</th>
                            <th>AMOUNT</th>
                            <th>PURCHASE DATE</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Winner Modal -->
<div class="modal fade" id="winnerModal" tabindex="-1" role="dialog" aria-labelledby="winnerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="winnerModalLabel">Winning History - <?= htmlspecialchars($userdata['users_name']) ?></h5>
                <label><?= htmlspecialchars($userdata['users_email']) ?></label>
                <label>Total Winning : <?= htmlspecialchars($total_winn) ?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="winnerFilterDate" class="form-label fw-semibold">Filter by Date:</label>
                        <input type="date" id="winnerFilterDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button id="winnerExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="winnerTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>SELLER NAME</th>
                            <th>ORDER ID</th>
                            <th>TICKET</th>
                            <th>PRIZE AMOUNT</th>
                            <th>WIN DATE</th>
                            <th>STATUS</th>
                            <th>CLAIM DATE</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Active Modal -->
<div class="modal fade" id="activeModal" tabindex="-1" role="dialog" aria-labelledby="activeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="winnerModalLabel">Active Draw History - <?= htmlspecialchars($userdata['users_name']) ?></h5>
                <label><?= htmlspecialchars($userdata['users_email']) ?></label>
                <label>Total Active Ticket : <?= htmlspecialchars($activeticketcount) ?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    
                    <div class="col-md-4">
                        <button id="activeExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="activeTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>CAMPAIGN NAME</th>
                            <th>DRAW DATE</th>
                            <th>ORDER ID</th>
                            <th>SPENT AMOUNT</th>
                            <!-- <th>PRIZE AMOUNT</th> -->
                            <th>STATUS</th>
                            <th>PURCHASE DATE</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="winnerModalLabel">Active Draw History - <?= htmlspecialchars($userdata['users_name']) ?></h5>
                <label><?= htmlspecialchars($userdata['users_email']) ?></label>
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; gap: 20px; align-items: center;">
                    <li><label>Total Notification: <?= htmlspecialchars($totalread + $totalunread) ?></label></li>
                    <li><label>Total Read Notification: <?= htmlspecialchars($totalread) ?></label></li>
                    <li><label>Total Unread Notification: <?= htmlspecialchars($totalunread) ?></label></li>
                </ul>
                
                
                
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="view_status" class="form-label fw-semibold">Filter By View Status:</label>
                        <select name="view_status" id="view_status" class="form-control">
                            <option value="all">All (<?=$totalread + $totalunread?>)</option>
                            <option value="Y">Read (<?=$totalread?>)</option>
                            <option value="N">Unread (<?=$totalunread?>)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button id="notificationExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="notificationTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>TITLE</th>
                            <th>MESSAGE</th>
                            <th>RECIVED</th>
                            <th>STATUS</th>
                         </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Voucher Topups Modal -->
<div class="modal fade" id="topupModal" tabindex="-1" role="dialog" aria-labelledby="topupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="topupModalLabel">Voucher Topups History - <?= htmlspecialchars($userdata['users_name']) ?></h5>
                <label><?= htmlspecialchars($userdata['users_email']) ?></label>
                <label>Total Amount : <?= htmlspecialchars($voucher_topups) ?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="topupFilterDate" class="form-label fw-semibold">Filter by Date:</label>
                        <input type="date" id="topupFilterDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button id="topupExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="topupTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>AMOUNT</th>
                            <th>DATE</th>
                            <th>TIME</th>
                            <th>DESCRIPTION</th>
                         </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Transfer wallet Modal -->
<div class="modal fade" id="transferwalletModal" tabindex="-1" role="dialog" aria-labelledby="transferwalletModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferwalletModalLabel">Transfer Wallet History - <?= htmlspecialchars($userdata['users_name']) ?></h5>
                <label><?= htmlspecialchars($userdata['users_email']) ?></label>
                <label>Total Amount : <?= htmlspecialchars($transfer_wallet) ?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="transferwalletFilterDate" class="form-label fw-semibold">Filter by Date:</label>
                        <input type="date" id="transferwalletFilterDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button id="transferwalletExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="transferwalletTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>AMOUNT</th>
                            <th>DATE</th>
                            <th>TIME</th>
                            <th>Type</th>
                            <th>DESCRIPTION</th>
                         </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Online Recharge Modal -->
<div class="modal fade" id="onlinepurchaseModal" tabindex="-1" role="dialog" aria-labelledby="onlinepurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="onlinepurchaseModalLabel">Online Purchase History - <?= htmlspecialchars($userdata['users_name']) ?></h5>
                <label><?= htmlspecialchars($userdata['users_email']) ?></label>
                <label>Total Amount : <?= htmlspecialchars($online_purchase) ?></label>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="onlinepurchaseFilterDate" class="form-label fw-semibold">Filter by Date:</label>
                        <input type="date" id="onlinepurchaseFilterDate" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <button id="onlinepurchaseExportBtn" style="margin-top:30px" class="btn btn-sm btn-primary">Export to Excel</button>
                    </div>
                </div>

                <table id="onlinepurchaseTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>AMOUNT</th>
                            <th>DATE</th>
                            <th>TIME</th>
                            <th>Type</th>
                            <th>DESCRIPTION</th>
                         </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>



  function initDataTable({ tableId, ajaxUrl, userId, columns, dateInputId,optional='' }) {
    return $('#' + tableId).DataTable({
      scrollX: true,
      scrollY: "300px",
      scrollCollapse: true,
      fixedHeader: true,
      processing: true,
      serverSide: true,
      destroy: true,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      ajax: {
        url: ajaxUrl,
        type: "POST",
        data: function (d) {
          d.filterDate = $('#' + dateInputId).val();
          d.user_id = userId;
          d.type=optional
        }
      },
      columns: columns
    });
  }

  let ticketTable, winnerTable,activeTable,notificationTable,transferwalletTable,onlinepurchaseTable;
  const userId = '<?= $userdata['users_id'] ?>';

  $('#ticketCard').on('click', function () {
    setTimeout(() => {
      ticketTable = initDataTable({
        tableId: "ticketTable",
        ajaxUrl: "<?= base_url('users/get-user-ticket-list') ?>",
        userId,
        columns: [
          
          { data: "ticket" },
          { data: "draw_name" },
          { data: "amount" },
          { data: "purchase_date" },
          { data: "status" }
        ],
        dateInputId: "ticketFilterDate"
      });
    }, 500); // delay to ensure modal has rendered
  });

  $('#winnerCard').on('click', function () {
    setTimeout(() => {
      winnerTable = initDataTable({
        tableId: "winnerTable",
        ajaxUrl: "<?= base_url('users/get-user-winner-list') ?>",
        userId,
        columns: [
          { data: "seller_name" },
          { data: "order_id" },
          { data: "ticket" },
          { data: "amount" },
          { data: "win_date" },
          { data: "status" },
          { data: "claim_date" }
        ],
        dateInputId: "winnerFilterDate"
      });
    }, 500);
  });
$('#activeCard').on('click', function () {
    setTimeout(() => {
      activeTable = initDataTable({
        tableId: "activeTable",
        ajaxUrl: "<?= base_url('users/get-user-active-ticket-list') ?>",
        userId,
        columns: [
          { data: "campaign_name" },
          { data: "draw_date" },
          { data: "order_id" },
          { data: "spent_amount" },
        //   { data: "prize_amount" },
          { data: "status" },
          { data: "purchase_date" }
        ],
        dateInputId: "activeFilterDate"
      });
    }, 500);
  });
  $('#notificationCard').on('click', function () {
    setTimeout(() => {
      notificationTable = initDataTable({
        tableId: "notificationTable",
        ajaxUrl: "<?= base_url('users/get-user-notification-list') ?>",
        userId,
        columns: [
          { data: "title" },
          { data: "notific_message" },
          { data: "created_at" },
          { data: "status" },
       
        ],
        dateInputId: "view_status"
      });
    }, 500);
  });

  $('#topupCard').on('click', function () {
    setTimeout(() => {
      topupTable = initDataTable({
        tableId: "topupTable",
        ajaxUrl: "<?= base_url('users/get-user-voucher-topups-list') ?>",
        userId,
        columns: [
          { data: "amount" },
          { data: "created_at" },
          { data: "time" },
          { data: "description" },
          
        ],
        dateInputId: "topupFilterDate",
        optional:'Recharge Coupon'
      });
    }, 500);
  });

  $('#transferwalletCard').on('click', function () {
    setTimeout(() => {
      transferwalletTable = initDataTable({
        tableId: "transferwalletTable",
        ajaxUrl: "<?= base_url('users/get-user-voucher-topups-list') ?>",
        userId,
        columns: [
          { data: "amount" },
          { data: "created_at" },
          { data: "time" },
          {data:'narration'},
          { data: "description" },
          
        ],
        dateInputId: "transferwalletFilterDate",
        optional:'Moved winning Prize'
      });
    }, 500);
  });

  
  $('#onlinepurchaseCard').on('click', function () {
    setTimeout(() => {
      onlinepurchaseTable = initDataTable({
        tableId: "onlinepurchaseTable",
        ajaxUrl: "<?= base_url('users/get-user-voucher-topups-list') ?>",
        userId,
        columns: [
          { data: "amount" },
          { data: "created_at" },
          { data: "time" },
          {data:'narration'},
          { data: "description" },
          
        ],
        dateInputId: "onlinepurchaseFilterDate",
        optional:'Online recharge'
      });
    }, 500);
  });
  $(document).on('change', '#ticketFilterDate', function () {
    if (ticketTable) ticketTable.ajax.reload();
  });

  $(document).on('change', '#winnerFilterDate', function () {
    if (winnerTable) winnerTable.ajax.reload();
  });
$(document).on('change', '#activeFilterDate', function () {
    if (activeTable) activeTable.ajax.reload();
  });
$(document).on('change', '#view_status', function () {
    if (notificationTable) notificationTable.ajax.reload();
});

$(document).on('change', '#topupFilterDate', function () {
    if (topupTable) topupTable.ajax.reload();
});
$(document).on('change', '#transferwalletFilterDate', function () {
    if (transferwalletTable) transferwalletTable.ajax.reload();
});
$(document).on('change', '#onlinepurchaseFilterDate', function () {
    if (onlinepurchaseTable) onlinepurchaseTable.ajax.reload();
});
  function exportTableToCSV(tableId, filename) {
    const rows = document.querySelectorAll(`#${tableId} tr`);
    let csv = [];

    rows.forEach(row => {
      let rowData = [];
      row.querySelectorAll("th, td").forEach(cell => {
        let text = cell.innerText.replace(/"/g, '""');
        rowData.push('"' + text + '"');
      });
      csv.push(rowData.join(","));
    });

    const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
    const downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = URL.createObjectURL(csvFile);
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
  }

  $('#ticketExportBtn').on('click', function () {
    exportTableToCSV('ticketTable', 'ticket_data.csv');
  });

  $('#winnerExportBtn').on('click', function () {
    exportTableToCSV('winnerTable', 'winner_data.csv');
  });
 $('#activeExportBtn').on('click', function () {
    exportTableToCSV('activeTable', 'active_order_data.csv');
  });
$('#notificationExportBtn').on('click', function () {
    exportTableToCSV('notificationTable', 'notification_data.csv');
  });
$('#topupExportBtn').on('click', function () {
    exportTableToCSV('topupTable', 'voucher_topups_data.csv');
  });
  $('#transferwalletExportBtn').on('click', function () {
    exportTableToCSV('transferwalletTable', 'transfer_wallet_data.csv');
  });
  $('#onlinepurchaseExportBtn').on('click', function () {
    exportTableToCSV('onlinepurchaseTable', 'online_purchase_data.csv');
  });
</script>