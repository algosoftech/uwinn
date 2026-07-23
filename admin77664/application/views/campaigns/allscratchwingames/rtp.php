<?php
$_rtp_cfg = array();
if (!empty($EDITDATA)) {
    if (is_object($EDITDATA)) {
        if ($EDITDATA instanceof MongoDB\Model\BSONDocument || $EDITDATA instanceof ArrayObject) {
            $EDITDATA = $EDITDATA->getArrayCopy();
        } else {
            $EDITDATA = (array) $EDITDATA;
        }
    }
}
if (!is_array($EDITDATA)) {
    $EDITDATA = array();
}
if (!empty($EDITDATA['rtp_config'])) {
    $_rtp_cfg = is_object($EDITDATA['rtp_config']) ? (array) $EDITDATA['rtp_config'] : $EDITDATA['rtp_config'];
    if (!is_array($_rtp_cfg)) {
        $_rtp_cfg = array();
    }
}

if (!function_exists('_scratchwin_rtp_cfg')) {
    function _scratchwin_rtp_cfg($cfg, $key, $default = '')
    {
        return (isset($cfg[$key]) && $cfg[$key] !== '' && $cfg[$key] !== null) ? $cfg[$key] : $default;
    }
}

$_rtp_default_slabs = array(
    'regular' => array(
        array('prize_amount' => 3, 'distribution_percentage' => 28, 'is_active' => 1),
        array('prize_amount' => 5, 'distribution_percentage' => 25, 'is_active' => 1),
        array('prize_amount' => 10, 'distribution_percentage' => 10, 'is_active' => 1),
        array('prize_amount' => 15, 'distribution_percentage' => 15, 'is_active' => 1),
        array('prize_amount' => 20, 'distribution_percentage' => 10, 'is_active' => 1),
        array('prize_amount' => 25, 'distribution_percentage' => 8, 'is_active' => 1),
        array('prize_amount' => 30, 'distribution_percentage' => 4, 'is_active' => 1),
    ),
    'reserve' => array(
        array('prize_amount' => 50, 'distribution_percentage' => 45, 'is_active' => 1),
        array('prize_amount' => 60, 'distribution_percentage' => 20, 'is_active' => 1),
        array('prize_amount' => 70, 'distribution_percentage' => 15, 'is_active' => 1),
        array('prize_amount' => 75, 'distribution_percentage' => 10, 'is_active' => 1),
        array('prize_amount' => 80, 'distribution_percentage' => 10, 'is_active' => 1),
    ),
    'big' => array(
        array('prize_amount' => 100, 'distribution_percentage' => 15, 'is_active' => 1),
        array('prize_amount' => 150, 'distribution_percentage' => 15, 'is_active' => 1),
        array('prize_amount' => 200, 'distribution_percentage' => 20, 'is_active' => 1),
        array('prize_amount' => 250, 'distribution_percentage' => 20, 'is_active' => 1),
        array('prize_amount' => 500, 'distribution_percentage' => 20, 'is_active' => 1),
        array('prize_amount' => 1000, 'distribution_percentage' => 10, 'is_active' => 1, 'is_mandatory' => 1),
    ),
);

$_rtp_pool_slabs = array('regular' => array(), 'reserve' => array(), 'big' => array());
$_saved_slabs = !empty($EDITDATA['rtp_prize_slabs']) ? $EDITDATA['rtp_prize_slabs'] : array();
if (is_object($_saved_slabs)) {
    $_saved_slabs = (array) $_saved_slabs;
}
if (!empty($_saved_slabs) && is_array($_saved_slabs)) {
    foreach ($_saved_slabs as $_slab) {
        if (is_object($_slab)) {
            $_slab = (array) $_slab;
        }
        if (!is_array($_slab)) {
            continue;
        }
        $_pool = isset($_slab['pool_type']) ? (string) $_slab['pool_type'] : 'regular';
        if (!isset($_rtp_pool_slabs[$_pool])) {
            $_pool = 'regular';
        }
        $_rtp_pool_slabs[$_pool][] = $_slab;
    }
}
foreach (array('regular', 'reserve', 'big') as $_pool_key) {
    if (empty($_rtp_pool_slabs[$_pool_key])) {
        $_rtp_pool_slabs[$_pool_key] = $_rtp_default_slabs[$_pool_key];
    }
}

$_pool_release_defaults = array('regular' => 100, 'reserve' => 100, 'big' => 100);
$_pool_labels = array(
    'regular' => 'Regular Prize Pool (AED 3 - AED 30)',
    'reserve' => 'Medium Prize Pool (AED 50 - AED 80)',
    'big' => 'Big Prize Pool (AED 100 and Above)',
);
$_ticket_price = _scratchwin_rtp_cfg($_rtp_cfg, 'ticket_price', 3);
$_live_stats = !empty($live_stats) && is_array($live_stats) ? $live_stats : array();
$_live_daily_sales = isset($_live_stats['daily_sales']) ? (float) $_live_stats['daily_sales'] : 0;
$_live_rtp_budget = isset($_live_stats['total_rtp_budget']) ? (float) $_live_stats['total_rtp_budget'] : 0;
$_live_effective_rtp = isset($_live_stats['effective_rtp']) ? (float) $_live_stats['effective_rtp'] : 0;
$_live_avg_prize = isset($_live_stats['average_prize_amount']) ? (float) $_live_stats['average_prize_amount'] : 0;
$_live_manual_big_prize_pct = isset($_live_stats['manual_big_prize_percent']) ? (float) $_live_stats['manual_big_prize_percent'] : 0;
$_live_game_wise_prize_pct = isset($_live_stats['game_wise_prize_percent']) ? (float) $_live_stats['game_wise_prize_percent'] : 0;
$_live_all_games_prize_pct = isset($_live_stats['all_games_prize_percent'])
    ? (float) $_live_stats['all_games_prize_percent']
    : round($_live_manual_big_prize_pct + $_live_game_wise_prize_pct, 2);
$_rtp_filter_date = !empty($rtp_filter_date) ? (string) $rtp_filter_date : date('Y-m-d');
$_rtp_scope = !empty($rtp_scope) ? (string) $rtp_scope : 'global';
$_saved_rtp_scope = !empty($saved_rtp_scope) ? (string) $saved_rtp_scope : '';
if ($_saved_rtp_scope === '' && isset($EDITDATA['rtp_scope'])) {
    $_saved_rtp_scope = (string) $EDITDATA['rtp_scope'];
}
// Show Enabled only for the currently saved active RTP mode.
$_rtp_enabled = ($_saved_rtp_scope !== '' && $_saved_rtp_scope === $_rtp_scope);
$_rtp_all_games_id = 'all';
$_selected_game_id = !empty($selected_game_id) ? (string) $selected_game_id : '';
$_rtp_all_games_view = ($_rtp_scope === 'game' && $_selected_game_id === '');
$_rtp_game_options = !empty($rtp_game_options) && is_array($rtp_game_options) ? $rtp_game_options : array();
$_rtp_display_title = !empty($rtp_display_title) ? (string) $rtp_display_title : 'Global RTP Prize Pool';
$_rtp_scope_label = !empty($rtp_scope_label) ? (string) $rtp_scope_label : 'Global RTP';
$_rtp_readonly_mode = !empty($rtp_readonly_mode);
$_rtp_scope_message = !empty($rtp_scope_message) ? (string) $rtp_scope_message : '';
$_list_total_sales = 0;
$_list_total_orders = 0;
$_list_total_distributed_prize = 0;
$_list_game_count = count($_rtp_game_options);
$_list_rtp_sum = 0.0;
$_list_rtp_count = 0;
foreach ($_rtp_game_options as $_list_row) {
    $_list_total_sales += (float) (isset($_list_row['total_sales']) ? $_list_row['total_sales'] : 0);
    $_list_total_orders += (int) (isset($_list_row['order_count']) ? $_list_row['order_count'] : 0);
    $_list_total_distributed_prize += (float) (isset($_list_row['distributed_prize_amount']) ? $_list_row['distributed_prize_amount'] : 0);
    $_row_rtp = isset($_list_row['target_rtp_percent']) ? (float) $_list_row['target_rtp_percent'] : 0;
    if ($_row_rtp > 0) {
        $_list_rtp_sum += $_row_rtp;
        $_list_rtp_count++;
    }
}
$_list_avg_rtp = $_list_rtp_count > 0 ? round($_list_rtp_sum / $_list_rtp_count, 2) : 0;
$_rtp_field_value = _scratchwin_rtp_cfg($_rtp_cfg, 'target_rtp_percent', 70);
if ($_rtp_scope === 'game' && $_rtp_all_games_view && $_list_avg_rtp > 0) {
    $_rtp_field_value = $_list_avg_rtp;
}
// Mute RTP (%) when Game Wise is enabled / a single game is selected.
// Global RTP (%) stays editable whenever the Global section is shown.
$_rtp_field_muted = $_rtp_readonly_mode
    || ($_rtp_scope === 'game' && ($_rtp_enabled || !$_rtp_all_games_view));
// All Games Sales is combined view-only — pool/slab config is not editable here.
$_rtp_pools_locked = $_rtp_readonly_mode || !empty($_rtp_all_games_view);
$_current_data_id = '';
if (!empty($EDITDATA['_id'])) {
    $_mongo_id = $EDITDATA['_id'];
    if ($_mongo_id instanceof MongoDB\BSON\ObjectId) {
        $_current_data_id = (string) $_mongo_id;
    } elseif (is_object($_mongo_id) && isset($_mongo_id->{'$id'})) {
        $_current_data_id = (string) $_mongo_id->{'$id'};
    } else {
        $_current_data_id = (string) $_mongo_id;
    }
}
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLSCRATCHWINGAMESDATA', getCurrentControllerPath('index')); ?>">Scratch Win Games</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">RTP Configuration</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card rtp-page-card" style="overflow: visible;">
                    <div class="card-header rtp-page-header d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h5 class="mb-0 rtp-page-title"><?= htmlspecialchars($_rtp_display_title) ?></h5>
                            <small class="rtp-page-subtitle">Manage RTP pools, campaign sales, and prize distribution</small>
                        </div>
                        <a href="<?php echo correctLink('ALLSCRATCHWINGAMESDATA', getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-light rtp-back-btn">Back</a>
                    </div>
                    <div class="card-body rtp-page-body">
                        <form id="rtpPageForm" method="post" action="<?= htmlspecialchars(base_url('scratchwin/allscratchwingames/rtp?scope=' . urlencode($_rtp_scope) . '&filterDate=' . urlencode($_rtp_filter_date) . ($_selected_game_id !== '' ? '&game_id=' . urlencode($_selected_game_id) : ''))) ?>">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="rtp_scope" id="rtp_scope_hidden" value="<?= htmlspecialchars($_rtp_scope) ?>">
                            <input type="hidden" name="rtp_filter_date" id="rtp_filter_date_hidden" value="<?= htmlspecialchars($_rtp_filter_date) ?>">
                            <input type="hidden" name="CurrentDataID" value="<?= htmlspecialchars($_current_data_id) ?>">
                            <input type="hidden" name="selected_game_id" id="rtp_selected_game_id" value="<?= htmlspecialchars($_selected_game_id) ?>">

                            <?php if (validation_errors()): ?>
                                <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                            <?php endif; ?>

                            <div class="rtp-fieldset rtp-scope-panel mb-4">
                                <legend class="rtp-scope-legend"><i class="feather icon-layers"></i> RTP Scope</legend>
                                <div class="row align-items-end">
                                    <div class="col-md-12">
                                        <div class="rtp-scope-switch btn-group btn-group-sm" role="group">
                                            <a href="<?= htmlspecialchars(base_url('scratchwin/allscratchwingames/rtp?scope=global&filterDate=' . urlencode($_rtp_filter_date))) ?>" id="rtp_scope_btn_global" class="btn <?= $_rtp_scope === 'global' ? 'rtp-scope-btn-active' : 'rtp-scope-btn' ?>" aria-pressed="<?= $_rtp_scope === 'global' ? 'true' : 'false' ?>">Global RTP</a>
                                            <a href="<?= htmlspecialchars(base_url('scratchwin/allscratchwingames/rtp?scope=game&filterDate=' . urlencode($_rtp_filter_date) . ($_selected_game_id !== '' ? '&game_id=' . urlencode($_selected_game_id) : ''))) ?>" id="rtp_scope_btn_game" class="btn <?= $_rtp_scope === 'game' ? 'rtp-scope-btn-active' : 'rtp-scope-btn' ?>" aria-pressed="<?= $_rtp_scope === 'game' ? 'true' : 'false' ?>">Game Wise RTP</a>
                                        </div>
                                        <div class="rtp-scope-hint">Global RTP shows pool configuration. Game Wise RTP shows the campaign list — open a game to edit its RTP and prize distribution.</div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($_rtp_scope_message !== ''): ?>
                                <div class="alert alert-info" id="rtp_scope_message_alert"><?= htmlspecialchars($_rtp_scope_message) ?></div>
                            <?php else: ?>
                                <div class="alert alert-info" id="rtp_scope_message_alert" style="display:none;"></div>
                            <?php endif; ?>

                            <?php if ($_rtp_scope === 'global' || $_rtp_scope === 'game'): ?>
                            <?php $_rtp_show_game_config = ($_rtp_scope === 'global') || ($_rtp_scope === 'game' && ($_rtp_all_games_view || $_selected_game_id !== '')); ?>

                            <div id="rtp_live_top_section" class="<?= $_rtp_show_game_config ? '' : 'rtp-game-config-hidden' ?>">
                            <div class="rtp-enable-bar d-flex align-items-center justify-content-between flex-wrap mb-3">
                                <div class="rtp-enable-toggle-wrap">
                                    <label class="rtp-enable-toggle mb-0" for="rtp_is_enabled">
                                        <input type="checkbox" id="rtp_is_enabled" name="is_rtp_enabled" value="1" <?= $_rtp_enabled ? 'checked' : '' ?> <?= $_rtp_readonly_mode ? 'disabled' : '' ?>>
                                        <span class="rtp-enable-toggle-slider"></span>
                                        <span class="rtp-enable-toggle-text"><?= ($_rtp_scope === 'game')
                                            ? ($_rtp_enabled ? 'Game Wise RTP Enabled' : 'Enable Game Wise RTP')
                                            : ($_rtp_enabled ? 'Global RTP Enabled' : 'Enable Global RTP') ?></span>
                                    </label>
                                    <span class="rtp-enable-status-badge ml-2 <?= $_rtp_enabled ? 'rtp-enable-status-on' : 'rtp-enable-status-off' ?>" id="rtp_enable_status_badge"><?= $_rtp_enabled ? 'Enabled' : 'Disabled' ?></span>
                                </div>
                                <small class="text-muted"><?= ($_rtp_scope === 'game')
                                    ? ($_rtp_enabled
                                        ? 'Game Wise RTP is active. Turn off and save to disable it, or switch to Global RTP and enable that instead.'
                                        : 'Turn on and save to activate Game Wise RTP. This will disable Global RTP.')
                                    : ($_rtp_enabled
                                        ? 'Global RTP is active. Turn off and save to disable it, or switch to Game Wise RTP and enable that instead.'
                                        : 'Turn on and save to activate Global RTP. This will disable Game Wise RTP.') ?></small>
                            </div>

                            <fieldset class="rtp-fieldset mb-4" id="rtp_live_business_section">
                                <legend>Live Business Data <small class="text-muted font-weight-normal">(from API - not stored)</small></legend>
                                <div class="row rtp-live-date-filter">
                                    <div class="form-group col-md-3">
                                        <label for="rtp_filter_date">Select Date</label>
                                        <input type="text" id="rtp_filter_date" class="form-control rtp-filter-date-input" value="<?= htmlspecialchars($_rtp_filter_date) ?>" autocomplete="off" placeholder="YYYY-MM-DD" readonly>
                                    </div>
                                </div>
                                <div class="row rtp-live-data-fields">
                                    <div class="form-group col-md-3">
                                        <label id="rtp_live_sales_label">Today's Sales (AED)</label>
                                        <input type="text" class="form-control" id="rtp_live_daily_sales" readonly value="<?= htmlspecialchars(number_format($_live_daily_sales, 0, '.', ',')) ?>">
                                        <small class="text-muted" id="rtp_live_sales_hint">Live Scratch Win sales for today</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Ticket Price (AED)</label>
                                        <input type="text" class="form-control" id="rtp_ticket_price_display" readonly value="<?= htmlspecialchars($_ticket_price) ?>">
                                        <small class="text-muted">Not updated from this page</small>
                                    </div>
                                    <?php if ($_rtp_scope === 'global' ? !$_rtp_readonly_mode : true): ?>
                                    <div class="form-group col-md-3 rtp-live-rtp-field">
                                        <label for="rtp_target_rtp_percent"><?= $_rtp_scope === 'game' ? 'RTP (%)' : 'Global RTP (%)' ?></label>
                                        <input type="number" min="0" step="0.01" class="form-control rtp-calc-trigger<?= $_rtp_field_muted ? ' rtp-field-muted' : '' ?>" id="rtp_target_rtp_percent" name="rtp_config[target_rtp_percent]" value="<?= htmlspecialchars(rtrim(rtrim(number_format((float) $_rtp_field_value, 2, '.', ''), '0'), '.') ?: '0') ?>" <?= $_rtp_field_muted ? 'readonly' : '' ?>>
                                        <small class="text-muted"><?php
                                            if ($_rtp_scope === 'game' && $_rtp_all_games_view) {
                                                echo 'Average RTP % of all games';
                                            } elseif ($_rtp_scope === 'game') {
                                                echo 'From selected game RTP config';
                                            } else {
                                                echo 'Used to calculate total RTP budget';
                                            }
                                        ?></small>
                                    </div>
                                    <?php endif; ?>
                                    <div class="form-group col-md-3">
                                        <label>Total RTP Budget (AED)</label>
                                        <input type="text" class="form-control" id="rtp_total_budget_display" readonly value="<?= htmlspecialchars(number_format($_live_rtp_budget, 0, '.', ',')) ?>">
                                        <small class="text-muted" id="rtp_budget_hint">Today's sales × Global RTP %</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Avg Prize Amount (AED)</label>
                                        <input type="text" class="form-control" id="rtp_live_avg_prize" readonly value="<?= htmlspecialchars(number_format($_live_avg_prize, 2, '.', ',')) ?>">
                                        <small class="text-muted">All games payout ÷ winners</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Total Prize % (All Games)</label>
                                        <input type="text" class="form-control" id="rtp_live_all_games_prize_pct" readonly value="<?= htmlspecialchars(number_format($_live_all_games_prize_pct, 2, '.', ',')) ?>">
                                        <small class="text-muted">Manual prize % + Game wise prize %</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Manual Prizes %</label>
                                        <input type="text" class="form-control" id="rtp_live_manual_big_prize_pct" readonly value="<?= htmlspecialchars(number_format($_live_manual_big_prize_pct, 2, '.', ',')) ?>">
                                        <small class="text-muted">Manual prize payout ÷ sales</small>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Game Wise Prize %</label>
                                        <input type="text" class="form-control" id="rtp_live_game_wise_prize_pct" readonly value="<?= htmlspecialchars(number_format($_live_game_wise_prize_pct, 2, '.', ',')) ?>">
                                        <small class="text-muted">Game-wise prize payout ÷ sales</small>
                                    </div>
                                </div>
                                <div class="rtp-live-stats-footer">
                                    <div class="rtp-live-stats-metrics">
                                        <span class="rtp-live-stat-item"><span id="rtp_live_orders_label">Orders today:</span> <strong id="rtp_live_order_count"><?= (int) (isset($_live_stats['order_count']) ? $_live_stats['order_count'] : 0) ?></strong></span>
                                        <span class="rtp-live-stat-item">Prizes distributed: <strong id="rtp_live_total_winners"><?= (int) (isset($_live_stats['total_winners']) ? $_live_stats['total_winners'] : 0) ?></strong> winners</span>
                                        <span class="rtp-live-stat-item">Payout: <strong id="rtp_live_total_payout"><?= htmlspecialchars(number_format((float) (isset($_live_stats['total_winning_amount']) ? $_live_stats['total_winning_amount'] : 0), 0, '.', ',')) ?></strong> AED</span>
                                        <span class="rtp-live-stat-item">Effective RTP: <strong id="rtp_live_effective_rtp"><?= htmlspecialchars(number_format($_live_effective_rtp, 2)) ?></strong>%</span>
                                    </div>
                                    <div class="rtp-live-stats-action">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="rtp_refresh_live_stats" onclick="checkGetRtpLiveStats();"><i class="feather icon-refresh-cw"></i> Refresh Live Data</button>
                                    </div>
                                </div>
                            </fieldset>
                            </div>

                            <?php if ($_rtp_scope === 'global'): ?>
                            <div class="rtp-fieldset rtp-campaign-list-panel mb-4">
                                <div class="rtp-game-list-toolbar mt-1 mb-2">
                                    <div class="rtp-game-list-toolbar-title">
                                        <i class="feather icon-grid"></i> Scratch &amp; Win Games (<?= (int) $_list_game_count ?>)
                                    </div>
                                    <a href="<?= htmlspecialchars(base_url('scratchwin/allscratchwingames/rtp?scope=game&filterDate=' . urlencode($_rtp_filter_date))) ?>" class="btn btn-sm rtp-save-list-btn">
                                        <i class="feather icon-sliders"></i> Manage Game Wise RTP
                                    </a>
                                </div>
                                <?php if (!empty($_rtp_game_options)): ?>
                                <div class="table-responsive rtp-game-list-wrap mt-2">
                                    <table class="table table-sm mb-0 rtp-game-list-table">
                                        <thead>
                                            <tr>
                                                <th width="60">#</th>
                                                <th>Game</th>
                                                <th width="110">Product ID</th>
                                                <th width="90">Ticket</th>
                                                <th width="130">Total Sales</th>
                                                <th width="80">Orders</th>
                                                <th width="150">Distributed Prize</th>
                                                <th width="100">RTP %</th>
                                                <th width="100">Mode</th>
                                                <th width="90">Open</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $_g_sl = 1; foreach ($_rtp_game_options as $_game_opt): ?>
                                            <?php
                                            $_row_sales = (float) (isset($_game_opt['total_sales']) ? $_game_opt['total_sales'] : 0);
                                            $_row_orders = (int) (isset($_game_opt['order_count']) ? $_game_opt['order_count'] : 0);
                                            $_row_prize = (float) (isset($_game_opt['distributed_prize_amount']) ? $_game_opt['distributed_prize_amount'] : 0);
                                            $_row_rtp = (float) (isset($_game_opt['target_rtp_percent']) ? $_game_opt['target_rtp_percent'] : 0);
                                            $_row_open_url = base_url('scratchwin/allscratchwingames/rtp?scope=game&filterDate=' . urlencode($_rtp_filter_date) . '&game_id=' . urlencode($_game_opt['_id']));
                                            ?>
                                            <tr class="rtp-game-list-row" data-game-id="<?= htmlspecialchars($_game_opt['_id']) ?>">
                                                <td class="text-center"><?= (int) $_g_sl++ ?></td>
                                                <td>
                                                    <a href="<?= htmlspecialchars($_row_open_url) ?>" class="rtp-game-name-link">
                                                        <span class="rtp-game-name-dot"></span>
                                                        <?= htmlspecialchars($_game_opt['title']) ?>
                                                    </a>
                                                </td>
                                                <td class="text-center"><?= (int) (isset($_game_opt['products_id']) ? $_game_opt['products_id'] : 0) ?></td>
                                                <td class="text-center"><?= htmlspecialchars(number_format((float) (isset($_game_opt['ticket_price']) ? $_game_opt['ticket_price'] : 0), 0, '.', ',')) ?></td>
                                                <td class="text-right">
                                                    <span class="rtp-sales-value <?= $_row_sales > 0 ? 'rtp-sales-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_row_sales, 0, '.', ',')) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="rtp-orders-pill"><?= $_row_orders ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="rtp-prize-value <?= $_row_prize > 0 ? 'rtp-prize-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_row_prize, 0, '.', ',')) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?= $_row_rtp > 0 ? htmlspecialchars(rtrim(rtrim(number_format($_row_rtp, 2, '.', ''), '0'), '.')) . '%' : '—' ?>
                                                </td>
                                                <td>
                                                    <span class="rtp-mode-badge <?= !empty($_game_opt['is_rtp_enabled']) ? 'rtp-mode-badge-on' : 'rtp-mode-badge-off' ?>">
                                                        <?= !empty($_game_opt['is_rtp_enabled']) ? 'RTP' : 'Normal' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?= htmlspecialchars($_row_open_url) ?>" class="btn btn-sm rtp-open-game-btn">Open</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="rtp-game-list-footnote mt-2">All Scratch &amp; Win game products from <code>uw_scratch_win_games</code>. Sales, orders, and distributed prizes are generated from <code>uw_scratch_win_orders</code> for <?= htmlspecialchars($_rtp_filter_date) ?>. Click a game to configure its Game Wise RTP.</div>
                                <?php else: ?>
                                <div class="alert alert-info mb-0">No active Scratch &amp; Win games found.</div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($_rtp_scope === 'game' && !empty($_rtp_game_options)): ?>
                            <div class="rtp-fieldset rtp-campaign-list-panel mb-4">
                                <div class="row rtp-game-list-summary">
                                    <div class="col-md-3 col-6">
                                        <div class="rtp-list-stat-card rtp-list-stat-games">
                                            <span class="rtp-list-stat-label">Games</span>
                                            <strong><?= (int) $_list_game_count ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="rtp-list-stat-card rtp-list-stat-sales">
                                            <span class="rtp-list-stat-label">Total Sales</span>
                                            <strong><?= htmlspecialchars(number_format($_list_total_sales, 0, '.', ',')) ?></strong>
                                            <small>AED</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="rtp-list-stat-card rtp-list-stat-orders">
                                            <span class="rtp-list-stat-label">Total Orders</span>
                                            <strong><?= htmlspecialchars(number_format($_list_total_orders, 0, '.', ',')) ?></strong>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="rtp-list-stat-card rtp-list-stat-date">
                                            <span class="rtp-list-stat-label">Sales Date</span>
                                            <strong><?= htmlspecialchars($_rtp_filter_date) ?></strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="rtp-game-list-toolbar mt-3 mb-2">
                                    <div class="rtp-game-list-toolbar-title">
                                        <i class="feather icon-grid"></i> Campaign RTP List
                                    </div>
                                    <button type="submit" name="SaveGameListRtp" value="1" class="btn btn-sm rtp-save-list-btn">
                                        <i class="feather icon-save"></i> Save List RTP
                                    </button>
                                </div>
                                <div class="table-responsive rtp-game-list-wrap mt-2">
                                    <table class="table table-sm mb-0 rtp-game-list-table">
                                        <thead>
                                            <tr>
                                                <th>Game</th>
                                                <th width="90">Ticket</th>
                                                <th width="130">Total Sales</th>
                                                <th width="80">Orders</th>
                                                <th width="150">Distributed Prize</th>
                                                <th width="130">Distributed Prize %</th>
                                                <th width="100">RTP %</th>
                                                <th width="100">Mode</th>
                                                <th width="90">Open</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($_rtp_game_options as $_game_opt): ?>
                                            <?php
                                            $_row_sales = (float) (isset($_game_opt['total_sales']) ? $_game_opt['total_sales'] : 0);
                                            $_row_orders = (int) (isset($_game_opt['order_count']) ? $_game_opt['order_count'] : 0);
                                            $_row_prize = (float) (isset($_game_opt['distributed_prize_amount']) ? $_game_opt['distributed_prize_amount'] : 0);
                                            $_row_prize_pct = $_row_sales > 0 ? round(($_row_prize / $_row_sales) * 100, 2) : 0;
                                            ?>
                                            <tr class="rtp-game-list-row <?= $_selected_game_id === (string) $_game_opt['_id'] ? 'rtp-game-list-row-active' : '' ?>" data-game-id="<?= htmlspecialchars($_game_opt['_id']) ?>">
                                                <td>
                                                    <a href="javascript:void(0)" class="rtp-game-name-link rtp-game-open-trigger" data-game-id="<?= htmlspecialchars($_game_opt['_id']) ?>" data-game-title="<?= htmlspecialchars($_game_opt['title']) ?>">
                                                        <span class="rtp-game-name-dot"></span>
                                                        <?= htmlspecialchars($_game_opt['title']) ?>
                                                    </a>
                                                </td>
                                                <td class="text-center"><?= htmlspecialchars(number_format((float) (isset($_game_opt['ticket_price']) ? $_game_opt['ticket_price'] : 0), 0, '.', ',')) ?></td>
                                                <td class="text-right">
                                                    <span class="rtp-sales-value <?= $_row_sales > 0 ? 'rtp-sales-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_row_sales, 0, '.', ',')) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="rtp-orders-pill"><?= $_row_orders ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="rtp-prize-value <?= $_row_prize > 0 ? 'rtp-prize-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_row_prize, 0, '.', ',')) ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="rtp-prize-pct-value <?= $_row_prize_pct > 0 ? 'rtp-prize-pct-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_row_prize_pct, 2, '.', ',')) ?>%
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        class="form-control form-control-sm rtp-list-rtp-input"
                                                        name="game_list_rtp[<?= htmlspecialchars($_game_opt['_id']) ?>]"
                                                        value="<?= htmlspecialchars(rtrim(rtrim(number_format((float) (isset($_game_opt['target_rtp_percent']) ? $_game_opt['target_rtp_percent'] : 0), 2, '.', ''), '0'), '.')) ?>"
                                                    >
                                                </td>
                                                <td>
                                                    <span class="rtp-mode-badge <?= !empty($_game_opt['is_rtp_enabled']) ? 'rtp-mode-badge-on' : 'rtp-mode-badge-off' ?>">
                                                        <?= !empty($_game_opt['is_rtp_enabled']) ? 'RTP' : 'Normal' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm rtp-open-game-btn rtp-game-open-trigger" data-game-id="<?= htmlspecialchars($_game_opt['_id']) ?>" data-game-title="<?= htmlspecialchars($_game_opt['title']) ?>">Open</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php
                                            $_list_total_prize_pct = $_list_total_sales > 0
                                                ? round(($_list_total_distributed_prize / $_list_total_sales) * 100, 2)
                                                : 0;
                                            ?>
                                            <tr class="rtp-game-list-row rtp-game-list-row-total <?= $_rtp_all_games_view ? 'rtp-game-list-row-active' : '' ?>" data-game-id="<?= htmlspecialchars($_rtp_all_games_id) ?>">
                                                <td>
                                                    <a href="javascript:void(0)" class="rtp-game-name-link rtp-game-open-trigger" data-game-id="<?= htmlspecialchars($_rtp_all_games_id) ?>" data-game-title="All Games Sales">
                                                        <span class="rtp-game-name-dot rtp-game-name-dot-total"></span>
                                                        All Games Sales
                                                    </a>
                                                </td>
                                                <td class="text-center">—</td>
                                                <td class="text-right">
                                                    <span class="rtp-sales-value rtp-sales-value-total <?= $_list_total_sales > 0 ? 'rtp-sales-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_list_total_sales, 0, '.', ',')) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="rtp-orders-pill rtp-orders-pill-total"><?= (int) $_list_total_orders ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="rtp-prize-value rtp-prize-value-total <?= $_list_total_distributed_prize > 0 ? 'rtp-prize-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_list_total_distributed_prize, 0, '.', ',')) ?>
                                                    </span>
                                                </td>
                                                <td class="text-right">
                                                    <span class="rtp-prize-pct-value rtp-prize-pct-value-total <?= $_list_total_prize_pct > 0 ? 'rtp-prize-pct-value-active' : '' ?>">
                                                        <?= htmlspecialchars(number_format($_list_total_prize_pct, 2, '.', ',')) ?>%
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="rtp-avg-rtp-display" id="rtp_list_avg_rtp_display"><?= $_list_avg_rtp > 0 ? htmlspecialchars(rtrim(rtrim(number_format($_list_avg_rtp, 2, '.', ''), '0'), '.')) : '—' ?></span>
                                                </td>
                                                <td class="text-center">—</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm rtp-open-game-btn rtp-open-all-games-btn rtp-game-open-trigger" data-game-id="<?= htmlspecialchars($_rtp_all_games_id) ?>" data-game-title="All Games Sales">Open</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="rtp-game-list-footnote mt-2">Total sales, orders, and distributed prize amounts are campaign-wise from <code>uw_scratch_win_orders</code> for <?= htmlspecialchars($_rtp_filter_date) ?>. Click any game name once to open its RTP and prize distribution.</div>
                            </div>
                            <?php endif; ?>

                            <div id="rtp_game_config_wrapper" class="<?= $_rtp_show_game_config ? '' : 'rtp-game-config-hidden' ?>">
                            <div class="rtp-based-prize-slab<?= $_rtp_enabled ? '' : ' rtp-config-disabled' ?><?= $_rtp_all_games_view ? ' rtp-all-games-mode' : '' ?>" id="rtp_config_content">
                                <div class="alert alert-warning rtp-config-disabled-notice<?= $_rtp_enabled ? '' : ' is-visible' ?>" id="rtp_disabled_notice">
                                    <?= $_rtp_scope === 'game'
                                        ? 'Game Wise RTP is currently <strong>disabled</strong>. Turn on <strong>Enable Game Wise RTP</strong> and save to activate this mode.'
                                        : 'Global RTP is currently <strong>disabled</strong>. Orders will not use this RTP pool until you enable it and save.' ?>
                                </div>
                                <div class="alert alert-info rtp-all-games-readonly-notice<?= $_rtp_all_games_view ? ' is-visible' : '' ?>" id="rtp_all_games_readonly_notice">
                                    <strong>All Games Sales</strong> is read-only. Open an individual game to edit RTP Pool Allocation and prize pools.
                                </div>

                                <fieldset class="rtp-fieldset mb-4 rtp-editable-pools-section">
                                    <legend>RTP Pool Allocation</legend>
                                    <div class="table-responsive">
                                        <table class="table table-bordered rtp-pool-summary-table">
                                            <thead>
                                                <tr>
                                                    <th>Prize Pool</th>
                                                    <th>% of RTP</th>
                                                    <th>Pool Budget (AED)</th>
                                                    <th>Prize Releasing (%)</th>
                                                    <th>Released Budget (AED)</th>
                                                    <th>Runtime Winners</th>
                                                    <th>Runtime Payout (AED)</th>
                                                    <th>Unallocated (AED)</th>
                                                    <th>Prize Range</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Regular Prize Pool</td>
                                                    <td><input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-calc-trigger rtp-pool-percent" data-pool="regular" name="rtp_config[regular_pool_percent]" value="<?= htmlspecialchars(_scratchwin_rtp_cfg($_rtp_cfg, 'regular_pool_percent', 70)) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-pool-amount" data-pool="regular">0</td>
                                                    <td><input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm rtp-calc-trigger rtp-pool-release-percent" data-pool="regular" name="rtp_config[regular_pool_release_percent]" value="<?= htmlspecialchars(_scratchwin_rtp_cfg($_rtp_cfg, 'regular_pool_release_percent', $_pool_release_defaults['regular'])) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-pool-released-amount" data-pool="regular">0</td>
                                                    <td class="rtp-pool-winners-total" data-pool="regular">0</td>
                                                    <td class="rtp-pool-payout-total" data-pool="regular">0</td>
                                                    <td class="rtp-pool-unallocated" data-pool="regular">0</td>
                                                    <td>AED 3 - AED 30</td>
                                                </tr>
                                                <tr>
                                                    <td>Medium Prize Pool</td>
                                                    <td><input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-calc-trigger rtp-pool-percent" data-pool="reserve" name="rtp_config[reserve_pool_percent]" value="<?= htmlspecialchars(_scratchwin_rtp_cfg($_rtp_cfg, 'reserve_pool_percent', 20)) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-pool-amount" data-pool="reserve">0</td>
                                                    <td><input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm rtp-calc-trigger rtp-pool-release-percent" data-pool="reserve" name="rtp_config[reserve_pool_release_percent]" value="<?= htmlspecialchars(_scratchwin_rtp_cfg($_rtp_cfg, 'reserve_pool_release_percent', $_pool_release_defaults['reserve'])) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-pool-released-amount" data-pool="reserve">0</td>
                                                    <td class="rtp-pool-winners-total" data-pool="reserve">0</td>
                                                    <td class="rtp-pool-payout-total" data-pool="reserve">0</td>
                                                    <td class="rtp-pool-unallocated" data-pool="reserve">0</td>
                                                    <td>AED 50 - AED 80</td>
                                                </tr>
                                                <tr>
                                                    <td>Big Prize Pool</td>
                                                    <td><input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-calc-trigger rtp-pool-percent" data-pool="big" name="rtp_config[big_pool_percent]" value="<?= htmlspecialchars(_scratchwin_rtp_cfg($_rtp_cfg, 'big_pool_percent', 10)) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-pool-amount" data-pool="big">0</td>
                                                    <td><input type="number" min="0" max="100" step="0.01" class="form-control form-control-sm rtp-calc-trigger rtp-pool-release-percent" data-pool="big" name="rtp_config[big_pool_release_percent]" value="<?= htmlspecialchars(_scratchwin_rtp_cfg($_rtp_cfg, 'big_pool_release_percent', $_pool_release_defaults['big'])) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-pool-released-amount" data-pool="big">0</td>
                                                    <td class="rtp-pool-winners-total" data-pool="big">0</td>
                                                    <td class="rtp-pool-payout-total" data-pool="big">0</td>
                                                    <td class="rtp-pool-unallocated" data-pool="big">0</td>
                                                    <td>AED 100 and Above</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Total RTP</td>
                                                    <td><span id="rtp_pool_percent_total">0</span>%</td>
                                                    <td><span id="rtp_pool_amount_total">0</span></td>
                                                    <td></td>
                                                    <td><span id="rtp_pool_released_amount_total">0</span></td>
                                                    <td><span id="rtp_total_winners_summary">0</span></td>
                                                    <td><span id="rtp_total_payout_summary">0</span></td>
                                                    <td><span id="rtp_total_unallocated_summary">0</span></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </fieldset>

                                <?php $_slab_idx = 0; foreach ($_rtp_pool_slabs as $_pool_key => $_pool_rows): ?>
                                <fieldset class="rtp-fieldset rtp-pool-slabs-fieldset rtp-editable-pools-section mb-4">
                                    <legend><?= htmlspecialchars($_pool_labels[$_pool_key]) ?></legend>
                                    <p class="text-muted small mb-2 rtp-pool-runtime-hint">
                                        <?= ($_rtp_scope === 'game' && !$_rtp_all_games_view)
                                            ? 'Prize rows are from the selected game. Runtime Winners / Payout show live distribution for this game only.'
                                            : 'When All Games Sales is selected, Runtime Winners / Payout show combined distribution across all games.' ?>
                                    </p>
                                    <div class="mb-2 rtp-slab-actions">
                                        <?php if (!$_rtp_pools_locked): ?>
                                        <button type="button" class="btn btn-sm btn-success rtp-slab-add-btn" data-pool="<?= htmlspecialchars($_pool_key) ?>"><i class="feather icon-plus"></i> Add Prize</button>
                                        <button type="button" class="btn btn-sm btn-info rtp-slab-normalize-btn" data-pool="<?= htmlspecialchars($_pool_key) ?>">Normalize %</button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm rtp-slab-table">
                                            <thead>
                                                <tr>
                                                    <th>Active</th>
                                                    <th>Prize (AED)</th>
                                                    <th>Distribution %</th>
                                                    <th>Runtime Payout (AED)</th>
                                                    <th>Runtime Winners</th>
                                                    <th>Mandatory</th>
                                                    <th width="50">Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rtp-slab-tbody-<?= htmlspecialchars($_pool_key) ?>">
                                                <?php foreach ($_pool_rows as $_slab): ?>
                                                <?php if (is_object($_slab)) { $_slab = (array) $_slab; } ?>
                                                <tr class="rtp-slab-row" data-index="<?= (int) $_slab_idx ?>">
                                                    <td class="text-center"><input type="checkbox" class="rtp-slab-is-active" disabled <?= !empty($_slab['is_active']) || !isset($_slab['is_active']) ? 'checked' : '' ?>></td>
                                                    <td>
                                                        <input type="hidden" name="rtp_prize_slabs[<?= (int) $_slab_idx ?>][pool_type]" value="<?= htmlspecialchars($_pool_key) ?>">
                                                        <input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-slab-prize rtp-calc-trigger" name="rtp_prize_slabs[<?= (int) $_slab_idx ?>][prize_amount]" value="<?= htmlspecialchars(isset($_slab['prize_amount']) ? $_slab['prize_amount'] : 0) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>>
                                                    </td>
                                                    <td><input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-slab-dist rtp-calc-trigger" name="rtp_prize_slabs[<?= (int) $_slab_idx ?>][distribution_percentage]" value="<?= htmlspecialchars(isset($_slab['distribution_percentage']) ? $_slab['distribution_percentage'] : 0) ?>" <?= $_rtp_pools_locked ? 'readonly' : '' ?>></td>
                                                    <td class="rtp-slab-allocation text-right">0</td>
                                                    <td><span class="rtp-slab-winners-display">0</span></td>
                                                    <td class="text-center"><input type="checkbox" class="rtp-slab-is-mandatory" disabled <?= !empty($_slab['is_mandatory']) ? 'checked' : '' ?>></td>
                                                    <td class="text-center"><?php if (!$_rtp_pools_locked): ?><button type="button" class="btn btn-sm btn-danger rtp-slab-remove-btn" title="Remove"><i class="feather icon-trash-2"></i></button><?php endif; ?></td>
                                                </tr>
                                                <?php $_slab_idx++; endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td colspan="2" class="text-right">Pool Total</td>
                                                    <td class="rtp-slab-dist-total" data-pool="<?= htmlspecialchars($_pool_key) ?>">0</td>
                                                    <td class="rtp-slab-alloc-total text-right" data-pool="<?= htmlspecialchars($_pool_key) ?>">0</td>
                                                    <td class="rtp-slab-winners-total" data-pool="<?= htmlspecialchars($_pool_key) ?>">0</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </fieldset>
                                <?php endforeach; ?>

                                <fieldset class="rtp-fieldset mb-4" id="rtp-allocation-preview-section">
                                    <legend>Live Prize Distribution</legend>
                                    <p class="text-muted mb-3 rtp-preview-intro" id="rtp_preview_intro"><?= ($_rtp_scope === 'game' && !$_rtp_all_games_view) ? 'Runtime prizes distributed for the selected game.' : 'Runtime prizes distributed across all games combined.' ?> Pool projections below use the selected day's sales for budget estimates.</p>
                                    <div class="row rtp-preview-summary-cards mb-3">
                                        <div class="col-md-2 col-6"><div class="rtp-preview-stat-card"><span class="rtp-preview-stat-label">Daily Sales</span><strong id="rtp_preview_daily_sales">0</strong><small>AED</small></div></div>
                                        <div class="col-md-2 col-6"><div class="rtp-preview-stat-card"><span class="rtp-preview-stat-label">RTP Budget</span><strong id="rtp_preview_rtp_budget">0</strong><small>AED</small></div></div>
                                        <div class="col-md-2 col-6"><div class="rtp-preview-stat-card"><span class="rtp-preview-stat-label">Total Winners</span><strong id="rtp_preview_total_winners">0</strong></div></div>
                                        <div class="col-md-2 col-6"><div class="rtp-preview-stat-card"><span class="rtp-preview-stat-label">Total Payout</span><strong id="rtp_preview_total_payout">0</strong><small>AED</small></div></div>
                                        <div class="col-md-2 col-6"><div class="rtp-preview-stat-card"><span class="rtp-preview-stat-label">Active Slabs</span><strong id="rtp_preview_active_slabs">0</strong></div></div>
                                        <div class="col-md-2 col-6"><div class="rtp-preview-stat-card"><span class="rtp-preview-stat-label">Effective RTP</span><strong id="rtp_preview_effective_rtp">0</strong><small>%</small></div></div>
                                    </div>

                                    <h6 class="mb-2" id="rtp_preview_section_title"><?= ($_rtp_scope === 'game' && !$_rtp_all_games_view) ? 'Runtime Prize Distribution — Selected Game' : 'Runtime Prize Distribution — All Games Combined' ?></h6>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-sm rtp-preview-slab-table">
                                            <thead>
                                                <tr>
                                                    <th>Pool</th>
                                                    <th>Prize (AED)</th>
                                                    <th>Distribution %</th>
                                                    <th>Winners</th>
                                                    <th>Payout (AED)</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rtp_preview_slab_tbody"></tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold">
                                                    <td colspan="3" class="text-right">Grand Total</td>
                                                    <td id="rtp_preview_slab_winners_total">0</td>
                                                    <td id="rtp_preview_slab_payout_total">0</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <h6 class="mb-2">Sales Scenario Comparison — Pool Breakdown</h6>
                                    <p class="text-muted small mb-2">See how prize allocation scales when daily sales change using the current RTP, pool, release, and slab configuration.</p>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm rtp-preview-scenario-table">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Daily Sales (AED)</th>
                                                    <th rowspan="2">RTP Budget</th>
                                                    <th colspan="3" class="text-center">Regular Pool</th>
                                                    <th colspan="3" class="text-center">Medium Pool</th>
                                                    <th colspan="3" class="text-center">Big Pool</th>
                                                    <th rowspan="2">Total Winners</th>
                                                    <th rowspan="2">Total Payout</th>
                                                    <th rowspan="2">Effective RTP</th>
                                                </tr>
                                                <tr>
                                                    <th>Budget</th><th>Winners</th><th>Payout</th>
                                                    <th>Budget</th><th>Winners</th><th>Payout</th>
                                                    <th>Budget</th><th>Winners</th><th>Payout</th>
                                                </tr>
                                            </thead>
                                            <tbody id="rtp_preview_scenario_tbody"></tbody>
                                        </table>
                                    </div>
                                </fieldset>
                            </div>

                            <?php if (!$_rtp_readonly_mode || $_rtp_scope === 'game'): ?>
                            <div class="form-group text-right rtp-save-section <?= ($_rtp_scope === 'game' && $_rtp_readonly_mode) ? 'rtp-game-config-hidden' : '' ?>" id="rtp_save_section">
                                <p class="text-muted small text-left mb-2">
                                    Saving updates:
                                    <strong><?= ($_rtp_scope === 'game' && !$_rtp_all_games_view) ? 'Use Game Wise RTP' : 'Enable Global RTP' ?></strong>,
                                    <strong>RTP Pool Allocation</strong>, and each pool's <strong>Prize (AED)</strong> / <strong>Distribution %</strong> only.
                                </p>
                                <button type="submit" name="SaveChanges" value="1" class="btn btn-primary"><?= ($_rtp_scope === 'game' && !$_rtp_all_games_view) ? 'Save Game RTP Configuration' : 'Save RTP Configuration' ?></button>
                            </div>
                            <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/template" id="rtp-slab-row-tpl">
<tr class="rtp-slab-row" data-index="__INDEX__">
    <td class="text-center"><input type="checkbox" class="rtp-slab-is-active" disabled checked></td>
    <td>
        <input type="hidden" name="rtp_prize_slabs[__INDEX__][pool_type]" value="__POOL__">
        <input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-slab-prize rtp-calc-trigger" name="rtp_prize_slabs[__INDEX__][prize_amount]" value="0">
    </td>
    <td><input type="number" min="0" step="0.01" class="form-control form-control-sm rtp-slab-dist rtp-calc-trigger" name="rtp_prize_slabs[__INDEX__][distribution_percentage]" value="0"></td>
    <td class="rtp-slab-allocation text-right">0</td>
    <td><span class="rtp-slab-winners-display">0</span></td>
    <td class="text-center"><input type="checkbox" class="rtp-slab-is-mandatory" disabled></td>
    <td class="text-center"><button type="button" class="btn btn-sm btn-danger rtp-slab-remove-btn" title="Remove"><i class="feather icon-trash-2"></i></button></td>
</tr>
</script>

<link href="{ASSET_INCLUDE_URL}dist/css/allscratchwingames-settings.css?v=8" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css?v=111">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js?v=111"></script>
<script>
window.RTP_LIVE_CFG = {
  liveStatsUrl: '<?= base_url('scratchwin/allscratchwingames/getRtpLiveStats') ?>',
  gameConfigUrl: '<?= base_url('scratchwin/allscratchwingames/getRtpGameConfig') ?>',
  gameListStatsUrl: '<?= base_url('scratchwin/allscratchwingames/getRtpGameListStats') ?>',
  pageUrl: '<?= base_url('scratchwin/allscratchwingames/rtp') ?>',
  filterDate: '<?= htmlspecialchars($_rtp_filter_date) ?>',
  todayDate: '<?= htmlspecialchars($this->timezone->current_date('Y-m-d', 'Asia/Dubai')) ?>',
  scope: '<?= htmlspecialchars($_rtp_scope) ?>',
  savedScope: '<?= htmlspecialchars($_saved_rtp_scope) ?>',
  readonly: <?= !empty($_rtp_readonly_mode) ? 'true' : 'false' ?>,
  gameId: '<?= htmlspecialchars($_selected_game_id !== '' ? $_selected_game_id : $_rtp_all_games_id) ?>',
  allGamesId: '<?= htmlspecialchars($_rtp_all_games_id) ?>',
  liveStats: <?= json_encode($_live_stats, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
};
</script>
<script src="{ASSET_INCLUDE_URL}dist/js/allscratchwingames-rtp.js?v=20260722b"></script>
