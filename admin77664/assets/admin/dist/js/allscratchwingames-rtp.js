(function($) {
  'use strict';

  if (!document.getElementById('rtpPageForm')) {
    return;
  }

  var $rtpSlabTpl = document.getElementById('rtp-slab-row-tpl');
  var rtpPoolMeta = {
    regular: { isHigh: '0', isBig: '0' },
    reserve: { isHigh: '1', isBig: '0' },
    big: { isHigh: '0', isBig: '1' }
  };
  var rtpPoolLabels = {
    regular: 'Regular',
    reserve: 'Medium',
    big: 'Big'
  };

  function rtpFormatPoolLabel(poolType) {
    var key = String(poolType || '').toLowerCase();
    if (rtpPoolLabels[key]) {
      return rtpPoolLabels[key];
    }
    if (key === 'reserve' || key === 'medium') {
      return 'Medium';
    }
    return poolType || '-';
  }
  var rtpScenarioSalesSteps = [1500, 3000, 5000, 7500, 10000, 15000, 20000, 30000];
  var rtpLiveStats = (window.RTP_LIVE_CFG && window.RTP_LIVE_CFG.liveStats) ? window.RTP_LIVE_CFG.liveStats : {};

  function rtpRound2(n) { return Math.round((Number(n) || 0) * 100) / 100; }
  function rtpFmtInt(n) { return Math.round(Number(n) || 0).toLocaleString('en-US'); }

  function rtpGetFilterDate() {
    var $date = $('#rtp_filter_date');
    if ($date.length && $date.val()) return $date.val();
    var cfg = window.RTP_LIVE_CFG || {};
    return cfg.filterDate || cfg.todayDate || '';
  }

  function rtpIsTodaySelected() {
    var cfg = window.RTP_LIVE_CFG || {};
    return rtpGetFilterDate() === (cfg.todayDate || '');
  }

  function rtpFormatDisplayDate(dateStr) {
    if (!dateStr) return 'selected day';
    var parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return parts[2] + ' ' + (months[(parseInt(parts[1], 10) - 1)] || parts[1]) + ' ' + parts[0];
  }

  function rtpUpdateDateContextLabels() {
    var cfg = window.RTP_LIVE_CFG || {};
    var displayDate = rtpFormatDisplayDate(rtpGetFilterDate());
    var isToday = rtpIsTodaySelected();
    var isAllGames = rtpIsAllGamesId(rtpGetActiveGameId());
    var isGameScope = (cfg.scope === 'game');
    var rtpHintLabel = (isGameScope && !isAllGames) ? 'Game RTP %' : 'Global RTP %';
    $('#rtp_live_sales_label').text(isToday ? "Today's Sales (AED)" : 'Sales (AED)');
    $('#rtp_live_sales_hint').text(isToday ? 'Live Tambola sales for today' : ('Tambola sales for ' + displayDate));
    $('#rtp_live_orders_label').text(isToday ? 'Orders today:' : 'Orders:');
    $('#rtp_budget_hint').text(isToday ? ("Today's sales × " + rtpHintLabel) : ('Selected day sales × ' + rtpHintLabel));
    if (isGameScope && !isAllGames) {
      $('#rtp_preview_section_title').text(isToday ? 'Runtime Prize Distribution — Selected Game' : ('Runtime Prize Distribution — Selected Game (' + displayDate + ')'));
      $('#rtp_preview_intro').text(
        (isToday ? 'Runtime prizes distributed today for the selected game.' : ('Runtime prizes distributed on ' + displayDate + ' for the selected game.')) +
        ' Pool projections below use that day\'s sales for budget estimates.'
      );
      $('.rtp-pool-runtime-hint').text('Prize rows are from the selected game. Runtime Winners / Payout show live distribution for this game only.');
    } else {
      $('#rtp_preview_section_title').text(isToday ? 'Runtime Prize Distribution — All Games Combined' : ('Runtime Prize Distribution — All Games Combined (' + displayDate + ')'));
      $('#rtp_preview_intro').text(
        (isToday ? 'Runtime prizes distributed today across all games combined.' : ('Runtime prizes distributed on ' + displayDate + ' across all games combined.')) +
        ' Pool projections below use that day\'s sales for budget estimates.'
      );
      $('.rtp-pool-runtime-hint').text('When All Games Sales is selected, Runtime Winners / Payout show combined distribution across all games.');
    }
    $('.rtp-list-stat-date strong').text(rtpGetFilterDate());
    $('.rtp-game-list-footnote').text('Total sales, orders, and distributed prize amounts are campaign-wise for ' + rtpGetFilterDate() + '. Click any game name once to open its RTP and prize distribution.');
  }

  function rtpSetLiveSectionLoading(isLoading) {
    var $section = $('#rtp_live_business_section');
    if (!$section.length) return;
    $section.toggleClass('rtp-live-data-loading', !!isLoading);
    $('#rtp_refresh_live_stats').prop('disabled', !!isLoading);
  }

  function rtpSyncFormContextFields() {
    var cfg = window.RTP_LIVE_CFG || {};
    var filterDate = rtpGetFilterDate();
    var gameId = cfg.gameId || '';
    var isAllGames = rtpIsAllGamesId(gameId);
    $('#rtp_filter_date_hidden').val(filterDate);
    $('#rtp_scope_hidden').val(cfg.scope || 'global');
    $('#rtp_selected_game_id').val(isAllGames ? '' : (gameId || ''));
  }

  function rtpPrepareFormForSubmit() {
    rtpSyncFormContextFields();
    var $enabled = $('#rtp_is_enabled');
    if ($enabled.length && $enabled.prop('disabled')) {
      $enabled.prop('disabled', false);
    }
    var cfg = window.RTP_LIVE_CFG || {};
    if (cfg.scope === 'game' && !rtpIsAllGamesId(cfg.gameId)) {
      var gameId = cfg.gameId || '';
      var listRtp = $('.rtp-game-list-row[data-game-id="' + gameId + '"] .rtp-list-rtp-input').val();
      if (listRtp !== undefined && listRtp !== '' && $('#rtp_target_rtp_percent').length) {
        $('#rtp_target_rtp_percent').val(listRtp);
      }
    }
  }

  function rtpSyncFilterDateConfig(selectedDate) {
    var cfg = window.RTP_LIVE_CFG || {};
    if (selectedDate) {
      cfg.filterDate = selectedDate;
      $('#rtp_filter_date').val(selectedDate);
      $('#rtp_filter_date_hidden').val(selectedDate);
    }
  }

  function rtpUpdateFilterDateUrl() {
    var cfg = window.RTP_LIVE_CFG || {};
    if (!cfg.pageUrl) return;
    var parts = ['scope=' + encodeURIComponent(cfg.scope || 'global'), 'filterDate=' + encodeURIComponent(rtpGetFilterDate())];
    if (cfg.scope === 'game' && cfg.gameId && !rtpIsAllGamesId(cfg.gameId)) {
      parts.push('game_id=' + encodeURIComponent(cfg.gameId));
    }
    var nextUrl = cfg.pageUrl + '?' + parts.join('&');
    if (window.history && typeof window.history.replaceState === 'function') {
      window.history.replaceState({ gameId: cfg.gameId || '', scope: cfg.scope || 'global', filterDate: rtpGetFilterDate() }, '', nextUrl);
    }
  }

  function rtpApplyEnableScopeUi(scope, options) {
    options = options || {};
    var cfg = window.RTP_LIVE_CFG || {};
    var activeScope = String(scope || cfg.scope || 'global');
    var savedScope = String(cfg.savedScope || '');
    var isGame = activeScope === 'game';
    var $toggle = $('#rtp_is_enabled');
    var $badge = $('#rtp_enable_status_badge');
    // Match PHP: enabled when saved rtp_scope equals the current tab scope.
    var scopesMatch = savedScope !== '' && savedScope === activeScope;

    // Initial / tab sync: show checked only for the saved active mode.
    // On user toggle (preserveToggle), keep whatever the operator just selected.
    if (!options.preserveToggle) {
      $toggle.prop('checked', scopesMatch);
    }

    $toggle.prop('disabled', !!cfg.readonly);

    var enabled = $toggle.is(':checked');
    if (isGame) {
      $('.rtp-enable-toggle-text').text(enabled ? 'Game Wise RTP Enabled' : 'Enable Game Wise RTP');
      $('.rtp-enable-bar .text-muted').first().text(
        enabled
          ? (scopesMatch
            ? 'Game Wise RTP is active. Turn off and save to disable it, or switch to Global RTP and enable that instead.'
            : 'Game Wise RTP will become active after save. Global RTP will be disabled.')
          : 'Turn on and save to activate Game Wise RTP. This will disable Global RTP.'
      );
    } else {
      $('.rtp-enable-toggle-text').text(enabled ? 'Global RTP Enabled' : 'Enable Global RTP');
      $('.rtp-enable-bar .text-muted').first().text(
        enabled
          ? (scopesMatch
            ? 'Global RTP is active. Turn off and save to disable it, or switch to Game Wise RTP and enable that instead.'
            : 'Global RTP will become active after save. Game Wise RTP will be disabled.')
          : 'Turn on and save to activate Global RTP. This will disable Game Wise RTP.'
      );
    }
    $badge.text(enabled ? 'Enabled' : 'Disabled')
      .toggleClass('rtp-enable-status-on', enabled)
      .toggleClass('rtp-enable-status-off', !enabled);

    var $notice = $('#rtp_disabled_notice');
    if ($notice.length) {
      if (isGame) {
        $notice.html('Game Wise RTP is currently <strong>disabled</strong>. Turn on <strong>Enable Game Wise RTP</strong> and save to activate this mode.');
      } else {
        $notice.html('Global RTP is currently <strong>disabled</strong>. Orders will not use this RTP pool until you enable it and save.');
      }
    }
  }

  function rtpSyncPoolSectionsEditable() {
    var cfg = window.RTP_LIVE_CFG || {};
    var isAllGames = rtpIsAllGamesId(rtpGetActiveGameId());
    var locked = !!cfg.readonly || isAllGames;
    var $content = $('#rtp_config_content');
    $content.toggleClass('rtp-all-games-mode', isAllGames);
    $('#rtp_all_games_readonly_notice').toggleClass('is-visible', isAllGames);

    $content.find('.rtp-pool-percent, .rtp-pool-release-percent, .rtp-slab-prize, .rtp-slab-dist')
      .prop('readonly', locked)
      .toggleClass('rtp-field-muted', locked);

    $content.find('.rtp-slab-actions').toggle(!locked);
    $content.find('.rtp-slab-add-btn, .rtp-slab-normalize-btn, .rtp-slab-remove-btn').toggle(!locked);
  }

  function updateRtpEnabledState(options) {
    var cfg = window.RTP_LIVE_CFG || {};
    var isAllGames = rtpIsAllGamesId(rtpGetActiveGameId());
    rtpApplyEnableScopeUi(cfg.scope, options);
    var enabled = $('#rtp_is_enabled').is(':checked');
    $('#rtp_config_content')
      .toggleClass('rtp-all-games-mode', isAllGames)
      .toggleClass('rtp-config-disabled', !enabled && !isAllGames);
    $('#rtp_disabled_notice').toggleClass('is-visible', !enabled && !isAllGames);
    rtpSyncTargetRtpFieldState();
    rtpSyncPoolSectionsEditable();
  }

  function rtpSyncScopeTabs(scope) {
    var activeScope = String(scope || $('#rtp_scope_hidden').val() || (window.RTP_LIVE_CFG || {}).scope || 'global');
    var isGame = activeScope === 'game';
    var $globalBtn = $('#rtp_scope_btn_global');
    var $gameBtn = $('#rtp_scope_btn_game');
    $globalBtn.toggleClass('rtp-scope-btn-active', !isGame).toggleClass('rtp-scope-btn', isGame).attr('aria-pressed', !isGame ? 'true' : 'false');
    $gameBtn.toggleClass('rtp-scope-btn-active', isGame).toggleClass('rtp-scope-btn', !isGame).attr('aria-pressed', isGame ? 'true' : 'false');
    $('#rtp_scope_hidden').val(activeScope);
    var cfg = window.RTP_LIVE_CFG || {};
    cfg.scope = activeScope;
    updateRtpEnabledState();
  }

  function rtpIsAllGamesId(gameId) {
    var cfg = window.RTP_LIVE_CFG || {};
    return String(gameId || '') === String(cfg.allGamesId || 'all');
  }

  function rtpGetActiveGameId() {
    var cfg = window.RTP_LIVE_CFG || {};
    return cfg.gameId || cfg.allGamesId || 'all';
  }

  function rtpGetLiveDailySales() {
    var fromStats = parseFloat(rtpLiveStats.daily_sales);
    if (fromStats > 0) return fromStats;
    var fromDom = parseFloat(($('#rtp_live_daily_sales').val() || '').toString().replace(/,/g, ''));
    return fromDom > 0 ? fromDom : 0;
  }

  function rtpApplyLiveStatsToDom(stats) {
    if (!stats || typeof stats !== 'object') return;
    var cfg = window.RTP_LIVE_CFG || {};
    rtpLiveStats = stats;
    $('#rtp_live_daily_sales').val(rtpFmtInt(stats.daily_sales || 0));
    $('#rtp_total_budget_display').val(rtpFmtInt(stats.total_rtp_budget || 0));
    $('#rtp_live_order_count').text(rtpFmtInt(stats.order_count || 0));
    $('#rtp_live_total_winners').text(rtpFmtInt(stats.total_winners || 0));
    $('#rtp_live_total_payout').text(rtpFmtInt(stats.total_winning_amount || 0));
    $('#rtp_live_effective_rtp').text((Number(stats.effective_rtp) || 0).toFixed(2));
    var avgPrize = Number(stats.average_prize_amount);
    if (!isFinite(avgPrize) || avgPrize < 0) avgPrize = 0;
    var manualBigPrizePct = Number(stats.manual_big_prize_percent);
    if (!isFinite(manualBigPrizePct) || manualBigPrizePct < 0) manualBigPrizePct = 0;
    var gameWisePrizePct = Number(stats.game_wise_prize_percent);
    if (!isFinite(gameWisePrizePct) || gameWisePrizePct < 0) gameWisePrizePct = 0;
    var allGamesPrizePct = manualBigPrizePct + gameWisePrizePct;
    $('#rtp_live_avg_prize').val(avgPrize.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
    $('#rtp_live_all_games_prize_pct').val(allGamesPrizePct.toFixed(2));
    $('#rtp_live_manual_big_prize_pct').val(manualBigPrizePct.toFixed(2));
    $('#rtp_live_game_wise_prize_pct').val(gameWisePrizePct.toFixed(2));
    if (stats.ticket_price != null && stats.ticket_price !== '') {
      $('#rtp_ticket_price_display').val(stats.ticket_price);
    }
    // Don't overwrite All Games average RTP from live-stats target_rtp_percent.
    if (cfg.scope === 'game' && !rtpIsAllGamesId(rtpGetActiveGameId()) && stats.target_rtp_percent != null && $('#rtp_target_rtp_percent').length) {
      var nextRtp = Number(stats.target_rtp_percent);
      if (!isFinite(nextRtp) || nextRtp <= 0) {
        nextRtp = parseFloat($('#rtp_target_rtp_percent').val()) || 70;
      }
      $('#rtp_target_rtp_percent').val(nextRtp);
    }
    rtpSyncTargetRtpFieldState();
  }

  function rtpApplyGameListStats(data) {
    if (!data || typeof data !== 'object') return;
    var summary = data.summary || {};
    $('.rtp-list-stat-games strong').text(rtpFmtInt(summary.game_count || 0));
    $('.rtp-list-stat-sales strong').text(rtpFmtInt(summary.total_sales || 0));
    $('.rtp-list-stat-orders strong').text(rtpFmtInt(summary.total_orders || 0));
    var totalSales = Number(summary.total_sales) || 0;
    var totalOrders = Number(summary.total_orders) || 0;
    var totalPrize = Number(summary.total_distributed_prize) || 0;
    var totalPrizePct = totalSales > 0 ? Math.round((totalPrize / totalSales) * 10000) / 100 : 0;
    $('.rtp-sales-value-total').text(rtpFmtInt(totalSales)).toggleClass('rtp-sales-value-active', totalSales > 0);
    $('.rtp-orders-pill-total').text(rtpFmtInt(totalOrders));
    $('.rtp-prize-value-total').text(rtpFmtInt(totalPrize)).toggleClass('rtp-prize-value-active', totalPrize > 0);
    $('.rtp-prize-pct-value-total').text(totalPrizePct.toFixed(2) + '%').toggleClass('rtp-prize-pct-value-active', totalPrizePct > 0);
    if (data.filter_date) {
      $('.rtp-list-stat-date strong').text(data.filter_date);
    }
    (data.games || []).forEach(function(game) {
      var gameId = game._id || '';
      if (!gameId) return;
      var $row = $('.rtp-game-list-row[data-game-id="' + gameId + '"]');
      if (!$row.length) return;
      var sales = Number(game.total_sales) || 0;
      var orders = Number(game.order_count) || 0;
      var prize = Number(game.distributed_prize_amount) || 0;
      var prizePct = sales > 0 ? Math.round((prize / sales) * 10000) / 100 : 0;
      var $sales = $row.find('.rtp-sales-value');
      $sales.text(rtpFmtInt(sales)).toggleClass('rtp-sales-value-active', sales > 0);
      $row.find('.rtp-orders-pill').text(rtpFmtInt(orders));
      var $prize = $row.find('.rtp-prize-value');
      $prize.text(rtpFmtInt(prize)).toggleClass('rtp-prize-value-active', prize > 0);
      $row.find('.rtp-prize-pct-value').text(prizePct.toFixed(2) + '%').toggleClass('rtp-prize-pct-value-active', prizePct > 0);
    });
    rtpSyncTargetRtpFieldState();
  }

  function rtpRefreshGameListStats(callback) {
    var cfg = window.RTP_LIVE_CFG || {};
    if (cfg.scope !== 'game' || !cfg.gameListStatsUrl) {
      if (typeof callback === 'function') callback();
      return;
    }
    $.ajax({
      url: cfg.gameListStatsUrl,
      type: 'POST',
      dataType: 'json',
      data: { filterDate: rtpGetFilterDate() },
      success: function(response) {
        if (response && response.success && response.data) {
          rtpApplyGameListStats(response.data);
        }
        if (typeof callback === 'function') callback(response);
      },
      error: function() {
        if (typeof callback === 'function') callback();
      }
    });
  }

  function rtpFetchLiveStats(callback) {
    var cfg = window.RTP_LIVE_CFG || {};
    if (!cfg.liveStatsUrl) {
      if (typeof callback === 'function') callback(rtpLiveStats);
      return;
    }
    $.ajax({
      url: cfg.liveStatsUrl,
      type: 'POST',
      dataType: 'json',
      data: {
        filterDate: rtpGetFilterDate(),
        scope: cfg.scope || 'global',
        game_id: rtpIsAllGamesId(cfg.gameId) ? 'all' : (cfg.gameId || '')
      },
      success: function(response) {
        if (response && response.success && response.data) {
          rtpApplyLiveStatsToDom(response.data);
        }
        if (typeof callback === 'function') callback(rtpLiveStats);
      },
      error: function() {
        if (typeof callback === 'function') callback(rtpLiveStats);
      }
    });
  }

  function rtpGetSlabConfigFromDom() {
    var slabsByPool = { regular: [], reserve: [], big: [] };
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      $('#rtp-slab-tbody-' + pool + ' .rtp-slab-row').each(function() {
        var $row = $(this);
        slabsByPool[pool].push({
          prize: parseFloat($row.find('.rtp-slab-prize').val()) || 0,
          dist: parseFloat($row.find('.rtp-slab-dist').val()) || 0,
          isActive: $row.find('.rtp-slab-is-active').is(':checked'),
          isMandatory: $row.find('.rtp-slab-is-mandatory').is(':checked')
        });
      });
    });
    return slabsByPool;
  }

  function rtpGetPoolConfigFromDom() {
    var rtpPct = parseFloat($('#rtp_target_rtp_percent').val());
    if (!isFinite(rtpPct) || rtpPct < 0) rtpPct = 0;
    if (rtpPct === 0 && $('#rtp_target_rtp_percent').length) {
      // Keep scenario usable if field was wiped by a bad live-stats payload.
      rtpPct = 70;
      $('#rtp_target_rtp_percent').val(70);
    }
    var pools = {};
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      var pct = parseFloat($('.rtp-pool-percent[data-pool="' + pool + '"]').val()) || 0;
      var releasePct = parseFloat($('.rtp-pool-release-percent[data-pool="' + pool + '"]').val()) || 0;
      if (releasePct < 0) releasePct = 0;
      if (releasePct > 100) releasePct = 100;
      pools[pool] = { percent: pct, releasePercent: releasePct };
    });
    return { rtpPct: rtpPct, pools: pools };
  }

  function rtpAllocateGroupWinnerCounts(activeRows, poolBudget) {
    if (!activeRows.length || !(poolBudget > 0)) {
      activeRows.forEach(function(item) { item.winners = 0; });
      return;
    }
    var pctSum = activeRows.reduce(function(sum, item) { return sum + (Number(item.dist) || 0); }, 0);
    if (pctSum <= 0) {
      activeRows.forEach(function(item) { item.winners = 0; });
      return;
    }
    var infos = [];
    activeRows.forEach(function(item) {
      var prize = Number(item.prize) || 0;
      if (prize <= 0) {
        item.winners = 0;
        return;
      }
      var ideal = (((Number(item.dist) || 0) / pctSum) * poolBudget) / prize;
      item.winners = Math.floor(ideal);
      infos.push({ item: item, remainder: ideal - item.winners });
    });
    var spent = infos.reduce(function(sum, info) { return sum + info.item.winners * info.item.prize; }, 0);
    var leftover = rtpRound2(poolBudget - spent);
    var guard = 0;
    while (leftover > 0 && guard < 1000) {
      guard++;
      var candidates = infos.filter(function(info) {
        return info.item.prize <= leftover + 0.0001;
      }).sort(function(a, b) {
        return b.remainder - a.remainder || a.item.prize - b.item.prize;
      });
      if (!candidates.length) break;
      candidates[0].item.winners += 1;
      candidates[0].remainder -= 1;
      leftover = rtpRound2(leftover - candidates[0].item.prize);
    }
  }

  function rtpComputePoolAllocation(releasedBudget, slabs) {
    var activeRows = slabs.filter(function(slab) {
      return slab.isActive && slab.dist > 0;
    }).map(function(slab) {
      return { prize: slab.prize, dist: slab.dist, isMandatory: slab.isMandatory, winners: 0 };
    });
    rtpAllocateGroupWinnerCounts(activeRows, releasedBudget);

    var allocTotal = 0;
    var winnersTotal = 0;
    var slabResults = activeRows.map(function(item) {
      var payout = item.winners * item.prize;
      allocTotal += payout;
      winnersTotal += item.winners;
      return {
        prize: item.prize,
        dist: item.dist,
        winners: item.winners,
        payout: payout,
        isMandatory: item.isMandatory,
        isFunded: item.winners > 0
      };
    });

    slabs.filter(function(slab) {
      return !slab.isActive || slab.dist <= 0;
    }).forEach(function(slab) {
      slabResults.push({
        prize: slab.prize,
        dist: slab.dist,
        winners: 0,
        payout: 0,
        isMandatory: slab.isMandatory,
        isFunded: false,
        isInactive: !slab.isActive
      });
    });

    return {
      releasedBudget: releasedBudget,
      allocTotal: rtpRound2(allocTotal),
      winnersTotal: winnersTotal,
      unallocated: rtpRound2(Math.max(0, releasedBudget - allocTotal)),
      slabs: slabResults
    };
  }

  function rtpComputeScenario(salesAmount, poolConfig, slabsByPool) {
    var totalBudget = rtpRound2(salesAmount * (poolConfig.rtpPct / 100));
    var pools = {};
    var totalWinners = 0;
    var totalPayout = 0;
    var totalUnallocated = 0;
    var allSlabs = [];
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      var meta = poolConfig.pools[pool];
      var poolBudget = rtpRound2(totalBudget * (meta.percent / 100));
      var releasedBudget = rtpRound2(poolBudget * (meta.releasePercent / 100));
      var poolResult = rtpComputePoolAllocation(releasedBudget, slabsByPool[pool] || []);
      pools[pool] = {
        percent: meta.percent,
        releasePercent: meta.releasePercent,
        poolBudget: poolBudget,
        releasedBudget: releasedBudget,
        winnersTotal: poolResult.winnersTotal,
        allocTotal: poolResult.allocTotal,
        unallocated: poolResult.unallocated,
        slabs: poolResult.slabs
      };
      totalWinners += poolResult.winnersTotal;
      totalPayout += poolResult.allocTotal;
      totalUnallocated += poolResult.unallocated;
      poolResult.slabs.forEach(function(slab) {
        allSlabs.push({
          pool: pool,
          poolLabel: rtpFormatPoolLabel(pool),
          prize: slab.prize,
          dist: slab.dist,
          winners: slab.winners,
          payout: slab.payout,
          isMandatory: slab.isMandatory,
          isFunded: slab.isFunded,
          isInactive: slab.isInactive
        });
      });
    });
    return {
      salesAmount: salesAmount,
      totalBudget: totalBudget,
      totalWinners: totalWinners,
      totalPayout: rtpRound2(totalPayout),
      totalUnallocated: rtpRound2(totalUnallocated),
      effectiveRtp: salesAmount > 0 ? rtpRound2((totalPayout / salesAmount) * 100) : 0,
      pools: pools,
      slabs: allSlabs
    };
  }

  function rtpBuildDistLookup(poolConfig, slabsByPool, dailySales) {
    var scenario = rtpComputeScenario(dailySales, poolConfig, slabsByPool);
    var lookup = {};
    scenario.slabs.forEach(function(slab) {
      if (!slab.isInactive) {
        lookup[slab.pool + '|' + rtpRound2(slab.prize)] = Number(slab.dist) || 0;
      }
    });
    return lookup;
  }

  function rtpFormatDistPercent(dist) {
    if (dist === null || dist === undefined || dist === '') return '—';
    var value = Number(dist);
    if (!isFinite(value)) return '—';
    return value.toFixed(2) + '%';
  }

  function rtpRenderDistributedSlabs(stats, poolConfig, slabsByPool) {
    var $slabBody = $('#rtp_preview_slab_tbody');
    if (!$slabBody.length) return;
    var slabs = (stats && stats.distributed_slabs) ? stats.distributed_slabs : [];
    var totalWinners = Number(stats && stats.total_winners) || 0;
    var totalPayout = Number(stats && stats.total_winning_amount) || 0;
    var dailySales = rtpGetLiveDailySales();
    var distLookup = rtpBuildDistLookup(poolConfig || rtpGetPoolConfigFromDom(), slabsByPool || rtpGetSlabConfigFromDom(), dailySales);
    $('#rtp_preview_daily_sales').text(rtpFmtInt(dailySales));
    $('#rtp_preview_rtp_budget').text(rtpFmtInt(Number(stats && stats.total_rtp_budget) || 0));
    $('#rtp_preview_total_winners').text(rtpFmtInt(totalWinners));
    $('#rtp_preview_total_payout').text(rtpFmtInt(totalPayout));
    $('#rtp_preview_active_slabs').text(slabs.length);
    $('#rtp_preview_effective_rtp').text((Number(stats && stats.effective_rtp) || 0).toFixed(2));
    $slabBody.empty();
    slabs.forEach(function(slab) {
      var prizeAmount = rtpRound2(slab.prize_amount);
      var key = (slab.pool_type || 'regular') + '|' + prizeAmount;
      var dist = (slab.distribution_percentage !== undefined && slab.distribution_percentage !== null) ? slab.distribution_percentage : distLookup[key];
      $slabBody.append(
        '<tr class="rtp-preview-slab-active">' +
        '<td>' + rtpFormatPoolLabel(slab.pool_type) + '</td>' +
        '<td>' + rtpFmtInt(slab.prize_amount) + '</td>' +
        '<td>' + rtpFormatDistPercent(dist) + '</td>' +
        '<td>' + rtpFmtInt(slab.winners) + '</td>' +
        '<td>' + rtpFmtInt(slab.payout) + '</td>' +
        '<td><span class="rtp-preview-badge-funded">Distributed</span></td>' +
        '</tr>'
      );
    });
    if (!slabs.length) {
      $slabBody.append('<tr><td colspan="6" class="text-center text-muted">' + (rtpIsTodaySelected() ? 'No prizes distributed yet today.' : ('No prizes distributed on ' + rtpFormatDisplayDate(rtpGetFilterDate()) + '.')) + '</td></tr>');
    }
    $('#rtp_preview_slab_winners_total').text(rtpFmtInt(totalWinners));
    $('#rtp_preview_slab_payout_total').text(rtpFmtInt(totalPayout));
  }

  function rtpGetScenarioSalesAmounts(currentSales) {
    var amounts = rtpScenarioSalesSteps.slice();
    if (currentSales > 0) amounts.push(currentSales);
    amounts = amounts.filter(function(v, i, arr) { return v > 0 && arr.indexOf(v) === i; });
    amounts.sort(function(a, b) { return a - b; });
    return amounts;
  }

  function rtpRenderScenarioPreview(currentScenario) {
    var $body = $('#rtp_preview_scenario_tbody');
    if (!$body.length) return;
    $body.empty();
    rtpGetScenarioSalesAmounts(currentScenario.salesAmount).forEach(function(sales) {
      var scenario = rtpComputeScenario(sales, rtpGetPoolConfigFromDom(), rtpGetSlabConfigFromDom());
      var isCurrent = Math.abs(sales - currentScenario.salesAmount) < 0.01;
      var label = rtpFmtInt(sales) + (isCurrent ? ' <span class="badge badge-primary">Current</span>' : '');
      $body.append(
        '<tr class="' + (isCurrent ? 'rtp-preview-current-row' : '') + '">' +
        '<td>' + label + '</td>' +
        '<td>' + rtpFmtInt(scenario.totalBudget) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.regular.releasedBudget) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.regular.winnersTotal) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.regular.allocTotal) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.reserve.releasedBudget) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.reserve.winnersTotal) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.reserve.allocTotal) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.big.releasedBudget) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.big.winnersTotal) + '</td>' +
        '<td>' + rtpFmtInt(scenario.pools.big.allocTotal) + '</td>' +
        '<td>' + rtpFmtInt(scenario.totalWinners) + '</td>' +
        '<td>' + rtpFmtInt(scenario.totalPayout) + '</td>' +
        '<td>' + scenario.effectiveRtp.toFixed(2) + '%</td>' +
        '</tr>'
      );
    });
  }

  function updateRtpPoolSummary() {
    var data = {
      dailySales: rtpGetLiveDailySales(),
      rtpPct: parseFloat($('#rtp_target_rtp_percent').val()),
      pools: {},
      poolTotalPct: 0,
      releasedTotal: 0
    };
    if (!isFinite(data.rtpPct) || data.rtpPct < 0) data.rtpPct = 0;
    if (data.rtpPct === 0) {
      data.rtpPct = 70;
      if ($('#rtp_target_rtp_percent').length) {
        $('#rtp_target_rtp_percent').val(70);
      }
    }
    data.totalBudget = rtpRound2(data.dailySales * (data.rtpPct / 100));
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      var pct = parseFloat($('.rtp-pool-percent[data-pool="' + pool + '"]').val()) || 0;
      var releasePct = parseFloat($('.rtp-pool-release-percent[data-pool="' + pool + '"]').val()) || 0;
      if (releasePct < 0) releasePct = 0;
      if (releasePct > 100) releasePct = 100;
      var amount = rtpRound2(data.totalBudget * (pct / 100));
      var releasedAmount = rtpRound2(amount * (releasePct / 100));
      data.poolTotalPct += pct;
      data.releasedTotal += releasedAmount;
      data.pools[pool] = { percent: pct, amount: amount, releasePercent: releasePct, releasedAmount: releasedAmount };
      $('.rtp-pool-amount[data-pool="' + pool + '"]').text(rtpFmtInt(amount));
      $('.rtp-pool-released-amount[data-pool="' + pool + '"]').text(rtpFmtInt(releasedAmount));
    });
    $('#rtp_total_budget_display').val(rtpFmtInt(data.totalBudget));
    $('#rtp_pool_percent_total').text((Math.round(data.poolTotalPct * 100) / 100).toFixed(2));
    $('#rtp_pool_amount_total').text(rtpFmtInt(data.totalBudget));
    $('#rtp_pool_released_amount_total').text(rtpFmtInt(data.releasedTotal));
    return data;
  }

  function reindexRtpSlabRows() {
    $('.rtp-based-prize-slab .rtp-slab-row').each(function(i) {
      $(this).attr('data-index', i).find('input').each(function() {
        if (this.name && this.name.indexOf('rtp_prize_slabs[') === 0) {
          this.name = this.name.replace(/^rtp_prize_slabs\[\d+\]/, 'rtp_prize_slabs[' + i + ']');
        }
      });
    });
  }

  function rtpBuildRuntimeLookup(stats) {
    var lookup = {};
    var pools = {
      regular: { winners: 0, payout: 0 },
      reserve: { winners: 0, payout: 0 },
      big: { winners: 0, payout: 0 }
    };
    var slabs = (stats && stats.distributed_slabs) ? stats.distributed_slabs : [];
    slabs.forEach(function(slab) {
      var pool = slab.pool_type || 'regular';
      if (pool === 'medium') pool = 'reserve';
      if (!pools[pool]) pool = 'regular';
      var prize = rtpRound2(slab.prize_amount);
      var key = pool + '|' + prize;
      var winners = Number(slab.winners) || 0;
      var payout = Number(slab.payout) || 0;
      if (!lookup[key]) {
        lookup[key] = { winners: 0, payout: 0, dist: null };
      }
      lookup[key].winners += winners;
      lookup[key].payout = rtpRound2(lookup[key].payout + payout);
      if (slab.distribution_percentage !== undefined && slab.distribution_percentage !== null) {
        lookup[key].dist = Number(slab.distribution_percentage);
      }
      pools[pool].winners += winners;
      pools[pool].payout = rtpRound2(pools[pool].payout + payout);
    });
    if (stats && stats.pools) {
      ['regular', 'reserve', 'big'].forEach(function(pool) {
        var src = stats.pools[pool] || stats.pools[pool === 'reserve' ? 'medium' : pool];
        if (!src) return;
        if (src.winners != null) pools[pool].winners = Number(src.winners) || 0;
        if (src.payout != null) pools[pool].payout = Number(src.payout) || 0;
      });
    }
    return { byPrize: lookup, pools: pools };
  }

  function rtpSyncRuntimeOnlySlabRows(runtimeLookup) {
    if (!runtimeLookup || !runtimeLookup.byPrize) return;
    var isAllGames = rtpIsAllGamesId(rtpGetActiveGameId());
    $('.rtp-slab-row-runtime-only').remove();
    if (!isAllGames) return;
    if (!$rtpSlabTpl) return;

    var existing = {};
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      $('#rtp-slab-tbody-' + pool + ' .rtp-slab-row').each(function() {
        var prize = rtpRound2(parseFloat($(this).find('.rtp-slab-prize').val()) || 0);
        existing[pool + '|' + prize] = true;
      });
    });

    var globalIndex = $('.rtp-based-prize-slab .rtp-slab-row').length;
    Object.keys(runtimeLookup.byPrize).forEach(function(key) {
      if (existing[key]) return;
      var parts = key.split('|');
      var pool = parts[0] || 'regular';
      var prize = parseFloat(parts[1]) || 0;
      if (prize <= 0) return;
      var entry = runtimeLookup.byPrize[key];
      if (!entry || (!(entry.winners > 0) && !(entry.payout > 0))) return;
      var html = $rtpSlabTpl.innerHTML.replace(/__INDEX__/g, globalIndex).replace(/__POOL__/g, pool);
      var $row = $(html);
      $row.addClass('rtp-slab-row-runtime-only');
      $row.find('.rtp-slab-prize').val(prize).prop('readonly', true);
      $row.find('.rtp-slab-dist').val(entry.dist != null ? entry.dist : 0).prop('readonly', true);
      $row.find('.rtp-slab-is-active').prop('checked', true);
      $row.find('.rtp-slab-remove-btn').remove();
      $row.find('input[name]').each(function() {
        $(this).removeAttr('name');
      });
      $('#rtp-slab-tbody-' + pool).append($row);
      globalIndex++;
    });
    reindexRtpSlabRows();
  }

  function updateRtpSlabCalculations() {
    var data = updateRtpPoolSummary();
    var runtime = rtpBuildRuntimeLookup(rtpLiveStats);
    rtpSyncRuntimeOnlySlabRows(runtime);
    var totalWinners = 0;
    var totalPayout = 0;
    var totalUnallocated = 0;
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      var releasedBudget = data.pools[pool].releasedAmount || 0;
      var $rows = $('#rtp-slab-tbody-' + pool + ' .rtp-slab-row');
      var slabs = [];
      $rows.each(function() {
        var $row = $(this);
        slabs.push({
          prize: parseFloat($row.find('.rtp-slab-prize').val()) || 0,
          dist: parseFloat($row.find('.rtp-slab-dist').val()) || 0,
          isActive: $row.find('.rtp-slab-is-active').is(':checked'),
          isMandatory: $row.find('.rtp-slab-is-mandatory').is(':checked'),
          $row: $row
        });
      });
      var result = rtpComputePoolAllocation(releasedBudget, slabs);
      var distTotal = 0;
      var runtimeWinnersTotal = 0;
      var runtimePayoutTotal = 0;
      result.slabs.forEach(function(item, index) {
        var slab = slabs[index];
        if (slab && slab.$row) {
          var key = pool + '|' + rtpRound2(slab.prize);
          var runtimeEntry = runtime.byPrize[key];
          var winners = runtimeEntry ? (Number(runtimeEntry.winners) || 0) : 0;
          var payout = runtimeEntry ? (Number(runtimeEntry.payout) || 0) : 0;
          slab.$row.find('.rtp-slab-allocation').text(rtpFmtInt(payout));
          slab.$row.find('.rtp-slab-winners-display').text(rtpFmtInt(winners));
          runtimeWinnersTotal += winners;
          runtimePayoutTotal += payout;
        }
        if (!item.isInactive) distTotal += Number(item.dist) || 0;
      });
      var poolRuntime = runtime.pools[pool] || { winners: 0, payout: 0 };
      var hasPoolStats = !!(rtpLiveStats && rtpLiveStats.pools && rtpLiveStats.pools[pool]);
      var displayWinners = hasPoolStats ? poolRuntime.winners : runtimeWinnersTotal;
      var displayPayout = hasPoolStats ? poolRuntime.payout : runtimePayoutTotal;
      var unallocated = rtpRound2(Math.max(0, releasedBudget - displayPayout));
      totalWinners += displayWinners;
      totalPayout += displayPayout;
      totalUnallocated += unallocated;
      $('.rtp-slab-dist-total[data-pool="' + pool + '"]').text((Math.round(distTotal * 100) / 100).toFixed(2));
      $('.rtp-slab-alloc-total[data-pool="' + pool + '"]').text(rtpFmtInt(displayPayout));
      $('.rtp-slab-winners-total[data-pool="' + pool + '"]').text(rtpFmtInt(displayWinners));
      $('.rtp-pool-winners-total[data-pool="' + pool + '"]').text(rtpFmtInt(displayWinners));
      $('.rtp-pool-payout-total[data-pool="' + pool + '"]').text(rtpFmtInt(displayPayout));
      $('.rtp-pool-unallocated[data-pool="' + pool + '"]').text(rtpFmtInt(unallocated));
    });
    $('#rtp_total_winners_summary').text(rtpFmtInt(Number(rtpLiveStats.total_winners) || totalWinners));
    $('#rtp_total_payout_summary').text(rtpFmtInt(Number(rtpLiveStats.total_winning_amount) || totalPayout));
    $('#rtp_total_unallocated_summary').text(rtpFmtInt(totalUnallocated));
    var poolConfig = rtpGetPoolConfigFromDom();
    var slabsByPool = rtpGetSlabConfigFromDom();
    var currentScenario = rtpComputeScenario(data.dailySales, poolConfig, slabsByPool);
    rtpRenderDistributedSlabs(rtpLiveStats, poolConfig, slabsByPool);
    rtpRenderScenarioPreview(currentScenario);
  }

  function rtpLoadStatsForSelectedDate() {
    var cfg = window.RTP_LIVE_CFG || {};
    rtpSyncFilterDateConfig(rtpGetFilterDate());
    rtpUpdateDateContextLabels();
    rtpSetLiveSectionLoading(true);
    var $date = $('#rtp_filter_date');
    $date.prop('disabled', true);
    if ($date.hasClass('hasDatepicker')) $date.datepicker('disable');

    var pending = (cfg.scope === 'game' && cfg.gameListStatsUrl) ? 2 : 1;
    var done = function() {
      pending--;
      if (pending > 0) return;
      rtpSetLiveSectionLoading(false);
      $date.prop('disabled', false);
      if ($date.hasClass('hasDatepicker')) $date.datepicker('enable');
      rtpUpdateFilterDateUrl();
      updateRtpSlabCalculations();
    };

    rtpFetchLiveStats(done);
    if (cfg.scope === 'game' && cfg.gameListStatsUrl) {
      rtpRefreshGameListStats(done);
    }
  }

  window.checkGetRtpLiveStats = function() {
    var $btn = $('#rtp_refresh_live_stats');
    rtpSetLiveSectionLoading(true);
    if ($btn.length) {
      $btn.prop('disabled', true);
    }
    var cfg = window.RTP_LIVE_CFG || {};
    var pending = (cfg.scope === 'game' && cfg.gameListStatsUrl) ? 2 : 1;
    var done = function(responseStats) {
      pending--;
      if (pending > 0) return;
      rtpSetLiveSectionLoading(false);
      if ($btn.length) {
        $btn.prop('disabled', false);
      }
      updateRtpSlabCalculations();
      if (window.console && typeof window.console.log === 'function') {
        window.console.log('getRtpLiveStats response:', responseStats);
      }
    };
    rtpFetchLiveStats(done);
    if (cfg.scope === 'game' && cfg.gameListStatsUrl) {
      rtpRefreshGameListStats(done);
    }
  };

  function rtpInitFilterDatepicker() {
    var $date = $('#rtp_filter_date');
    if (!$date.length || typeof $.fn.datepicker !== 'function') return;
    if ($date.hasClass('hasDatepicker')) $date.datepicker('destroy');
    $date.datepicker({
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      changeYear: true,
      maxDate: 0,
      yearRange: '2020:' + (new Date().getFullYear()),
      beforeShow: function() {
        setTimeout(function() { $('.ui-datepicker').css('z-index', 1060); }, 0);
      },
      onSelect: function(selectedDate) {
        rtpSyncFilterDateConfig(selectedDate || rtpGetFilterDate());
        rtpLoadStatsForSelectedDate();
      }
    });
    $date.on('click focus', function() {
      if (!$date.prop('disabled')) $date.datepicker('show');
    });
  }

  function rtpInitGameListAjax() {
    var cfg = window.RTP_LIVE_CFG || {};
    if (cfg.scope !== 'game' || !cfg.gameConfigUrl) return;

    $(document).on('click', '.rtp-game-open-trigger', function(e) {
      e.preventDefault();
      var gameId = $(this).data('game-id') || '';
      if (!gameId) return;
      rtpLoadGameConfig(gameId);
    });
  }

  function rtpSetScopeMessage(message) {
    var $alert = $('#rtp_scope_message_alert');
    if (!$alert.length) return;
    if (message) {
      $alert.text(message).show();
    } else {
      $alert.hide().text('');
    }
  }

  function rtpUpdateGameListActiveRow(gameId) {
    $('.rtp-game-list-row').removeClass('rtp-game-list-row-active rtp-game-list-row-loading');
    var cfg = window.RTP_LIVE_CFG || {};
    var activeId = gameId || cfg.allGamesId || 'all';
    if (rtpIsAllGamesId(activeId)) {
      $('.rtp-game-list-row-total').addClass('rtp-game-list-row-active');
      return;
    }
    $('.rtp-game-list-row[data-game-id="' + activeId + '"]').addClass('rtp-game-list-row-active');
  }

  function rtpApplySlabsFromData(slabsByPool) {
    if (!$rtpSlabTpl) return;
    var globalIndex = 0;
    ['regular', 'reserve', 'big'].forEach(function(pool) {
      var $tbody = $('#rtp-slab-tbody-' + pool);
      $tbody.empty();
      var slabs = (slabsByPool && slabsByPool[pool]) ? slabsByPool[pool] : [];
      slabs.forEach(function(slab) {
        var html = $rtpSlabTpl.innerHTML.replace(/__INDEX__/g, globalIndex).replace(/__POOL__/g, pool);
        var $row = $(html);
        $row.find('.rtp-slab-prize').val(slab.prize_amount != null ? slab.prize_amount : 0);
        $row.find('.rtp-slab-dist').val(slab.distribution_percentage != null ? slab.distribution_percentage : 0);
        $row.find('.rtp-slab-is-active').prop('checked', slab.is_active === 0 || slab.is_active === false ? false : true);
        $row.find('.rtp-slab-is-mandatory').prop('checked', !!(slab.is_mandatory === 1 || slab.is_mandatory === true));
        $tbody.append($row);
        globalIndex++;
      });
    });
    reindexRtpSlabRows();
  }

  function rtpGetAverageListRtp() {
    var sum = 0;
    var count = 0;
    $('.rtp-game-list-row:not(.rtp-game-list-row-total) .rtp-list-rtp-input').each(function() {
      var val = parseFloat($(this).val());
      if (isFinite(val) && val > 0) {
        sum += val;
        count++;
      }
    });
    if (count <= 0) return 0;
    return Math.round((sum / count) * 100) / 100;
  }

  function rtpFormatRtpPercent(n) {
    var value = Number(n) || 0;
    if (!isFinite(value)) value = 0;
    return String(parseFloat(value.toFixed(2)));
  }

  function rtpSyncAvgRtpDisplay() {
    var avg = rtpGetAverageListRtp();
    var label = avg > 0 ? rtpFormatRtpPercent(avg) : '—';
    $('#rtp_list_avg_rtp_display').text(label);
    return avg;
  }

  function rtpSyncTargetRtpFieldState() {
    var cfg = window.RTP_LIVE_CFG || {};
    var isGame = String(cfg.scope || '') === 'game';
    var isAllGames = isGame && rtpIsAllGamesId(rtpGetActiveGameId());
    var $input = $('#rtp_target_rtp_percent');
    if (!$input.length) return;
    var modeEnabled = $('#rtp_is_enabled').is(':checked');

    if (isAllGames) {
      var avg = rtpSyncAvgRtpDisplay();
      if (avg > 0) {
        $input.val(rtpFormatRtpPercent(avg));
      }
      $('.rtp-live-rtp-field label').text('RTP (%)');
      $('.rtp-live-rtp-field small').text('Average RTP % of all games');
    } else if (isGame) {
      $('.rtp-live-rtp-field label').text('RTP (%)');
      $('.rtp-live-rtp-field small').text('From selected game RTP config');
    } else {
      $('.rtp-live-rtp-field label').text('Global RTP (%)');
      $('.rtp-live-rtp-field small').text('Used to calculate total RTP budget');
    }

    // Mute RTP (%) on Game Wise when enabled / selected game.
    // Global section: Global RTP (%) is never muted (unless page is readonly).
    var muted = !!cfg.readonly;
    if (!muted && isGame) {
      muted = modeEnabled || !isAllGames;
    }
    $input.prop('readonly', muted).toggleClass('rtp-field-muted', muted);
  }

  function rtpApplyGameConfigData(data, options) {
    if (!data || typeof data !== 'object') return;
    options = options || {};
    var cfg = window.RTP_LIVE_CFG || {};
    var isAllGames = !!(data.is_all_games || rtpIsAllGamesId(data.game_id));
    var rtpCfg = data.rtp_config || {};
    var targetRtp = rtpCfg.target_rtp_percent != null ? Number(rtpCfg.target_rtp_percent) : 70;
    if (!isFinite(targetRtp) || targetRtp <= 0) {
      targetRtp = 70;
    }

    if (!isAllGames) {
      var listRtp = $('.rtp-game-list-row[data-game-id="' + data.game_id + '"] .rtp-list-rtp-input').val();
      if (listRtp !== undefined && listRtp !== '') {
        targetRtp = listRtp;
      }
    }

    $('input[name="CurrentDataID"]').val(data.current_data_id || '');
    $('#rtp_selected_game_id').val(isAllGames ? '' : (data.game_id || ''));
    $('#rtp_scope_hidden').val('game');
    rtpSyncScopeTabs('game');
    if (!isAllGames) {
      $('#rtp_target_rtp_percent').val(targetRtp);
    }
    $('#rtp_ticket_price_display').val(rtpCfg.ticket_price != null ? rtpCfg.ticket_price : 3);
    $('.rtp-pool-percent[data-pool="regular"]').val(rtpCfg.regular_pool_percent != null ? rtpCfg.regular_pool_percent : 70);
    $('.rtp-pool-percent[data-pool="reserve"]').val(rtpCfg.reserve_pool_percent != null ? rtpCfg.reserve_pool_percent : 20);
    $('.rtp-pool-percent[data-pool="big"]').val(rtpCfg.big_pool_percent != null ? rtpCfg.big_pool_percent : 10);
    $('.rtp-pool-release-percent[data-pool="regular"]').val(rtpCfg.regular_pool_release_percent != null ? rtpCfg.regular_pool_release_percent : 100);
    $('.rtp-pool-release-percent[data-pool="reserve"]').val(rtpCfg.reserve_pool_release_percent != null ? rtpCfg.reserve_pool_release_percent : 100);
    $('.rtp-pool-release-percent[data-pool="big"]').val(rtpCfg.big_pool_release_percent != null ? rtpCfg.big_pool_release_percent : 100);
    cfg.gameId = isAllGames ? (cfg.allGamesId || 'all') : (data.game_id || '');
    if (data.saved_rtp_scope !== undefined && data.saved_rtp_scope !== null) {
      cfg.savedScope = String(data.saved_rtp_scope || '');
    }
    rtpApplySlabsFromData(data.rtp_prize_slabs || {});
    if (data.live_stats) {
      rtpApplyLiveStatsToDom(data.live_stats);
    }
    // Re-sync enable toggle from saved scope after AJAX game load.
    updateRtpEnabledState({ preserveToggle: !!options.preserveToggle });
    rtpSyncTargetRtpFieldState();
    rtpSyncPoolSectionsEditable();
    rtpSetScopeMessage(data.scope_message || '');
    $('.rtp-page-title').text(isAllGames ? 'Game Wise RTP - All Games Sales' : ('Game Wise RTP - ' + (data.title || 'Selected Game')));
    rtpUpdateDateContextLabels();
  }

  function rtpUpdateGameUrl(gameId) {
    var cfg = window.RTP_LIVE_CFG || {};
    if (!cfg.pageUrl) return;
    var parts = ['scope=game', 'filterDate=' + encodeURIComponent(rtpGetFilterDate())];
    if (gameId && !rtpIsAllGamesId(gameId)) {
      parts.push('game_id=' + encodeURIComponent(gameId));
    }
    var nextUrl = cfg.pageUrl + '?' + parts.join('&');
    if (window.history && typeof window.history.pushState === 'function') {
      window.history.pushState({ gameId: rtpIsAllGamesId(gameId) ? '' : gameId, scope: 'game' }, '', nextUrl);
    }
    cfg.gameId = rtpIsAllGamesId(gameId) ? (cfg.allGamesId || 'all') : gameId;
    cfg.scope = 'game';
  }

  function rtpLoadGameConfig(gameId) {
    var cfg = window.RTP_LIVE_CFG || {};
    if (!cfg.gameConfigUrl || !gameId) return;

    $('.rtp-game-list-row').removeClass('rtp-game-list-row-active rtp-game-list-row-loading');
    var $activeRow = rtpIsAllGamesId(gameId)
      ? $('.rtp-game-list-row-total')
      : $('.rtp-game-list-row[data-game-id="' + gameId + '"]');
    $activeRow.addClass('rtp-game-list-row-loading');

    $.ajax({
      url: cfg.gameConfigUrl,
      type: 'POST',
      dataType: 'json',
      data: {
        game_id: gameId,
        filterDate: rtpGetFilterDate()
      },
      success: function(response) {
        $('.rtp-game-list-row').removeClass('rtp-game-list-row-loading');
        if (!response || !response.success || !response.data) {
          alert((response && response.message) ? response.message : 'Unable to load game RTP configuration.');
          return;
        }
        rtpApplyGameConfigData(response.data);
        rtpUpdateGameListActiveRow(gameId);
        rtpUpdateGameUrl(gameId);
        $('#rtp_live_top_section').removeClass('rtp-game-config-hidden');
        $('#rtp_game_config_wrapper').removeClass('rtp-game-config-hidden');
        $('#rtp_save_section').removeClass('rtp-game-config-hidden');
        updateRtpSlabCalculations();
        $('html, body').animate({ scrollTop: $('#rtp_live_top_section').offset().top - 80 }, 300);
      },
      error: function() {
        $('.rtp-game-list-row').removeClass('rtp-game-list-row-loading');
        alert('Unable to load game RTP configuration. Please try again.');
      }
    });
  }

  $(document).on('input change', '.rtp-calc-trigger', updateRtpSlabCalculations);
  $('.rtp-slab-add-btn').on('click', function() {
    if (rtpIsAllGamesId(rtpGetActiveGameId()) || !!(window.RTP_LIVE_CFG || {}).readonly) return;
    if (!$rtpSlabTpl) return;
    var pool = $(this).data('pool');
    var meta = rtpPoolMeta[pool] || rtpPoolMeta.regular;
    var nextIndex = $('.rtp-based-prize-slab .rtp-slab-row').length;
    var html = $rtpSlabTpl.innerHTML.replace(/__INDEX__/g, nextIndex).replace(/__POOL__/g, pool).replace(/__IS_HIGH__/g, meta.isHigh).replace(/__IS_BIG__/g, meta.isBig);
    $('#rtp-slab-tbody-' + pool).append(html);
    reindexRtpSlabRows();
    updateRtpSlabCalculations();
  });
  $('.rtp-based-prize-slab').on('click', '.rtp-slab-remove-btn', function() {
    if (rtpIsAllGamesId(rtpGetActiveGameId()) || !!(window.RTP_LIVE_CFG || {}).readonly) return;
    var $tbody = $(this).closest('tbody');
    if ($tbody.find('.rtp-slab-row').length <= 1) {
      alert('At least one prize row is required per pool.');
      return;
    }
    $(this).closest('.rtp-slab-row').remove();
    reindexRtpSlabRows();
    updateRtpSlabCalculations();
  });
  $('.rtp-slab-normalize-btn').on('click', function() {
    if (rtpIsAllGamesId(rtpGetActiveGameId()) || !!(window.RTP_LIVE_CFG || {}).readonly) return;
    var $inputs = $('#rtp-slab-tbody-' + $(this).data('pool') + ' .rtp-slab-dist');
    var sum = 0;
    $inputs.each(function() { sum += parseFloat($(this).val()) || 0; });
    if (sum <= 0) return;
    $inputs.each(function() {
      var current = parseFloat($(this).val()) || 0;
      $(this).val(((current / sum) * 100).toFixed(2));
    });
    updateRtpSlabCalculations();
  });
  $('#rtp_is_enabled').on('change', function() {
    updateRtpEnabledState({ preserveToggle: true });
    updateRtpSlabCalculations();
  });
  $(document).on('input change', '.rtp-list-rtp-input', function() {
    rtpSyncAvgRtpDisplay();
    if (rtpIsAllGamesId(rtpGetActiveGameId())) {
      rtpSyncTargetRtpFieldState();
      updateRtpSlabCalculations();
    }
  });
  $('#rtp_refresh_live_stats').on('click', function(e) {
    if (e && typeof e.preventDefault === 'function') {
      e.preventDefault();
    }
    window.checkGetRtpLiveStats();
  });
  $('#rtpPageForm').on('submit', function() {
    rtpPrepareFormForSubmit();
  });

  rtpInitFilterDatepicker();
  rtpInitGameListAjax();
  rtpSyncScopeTabs();
  updateRtpEnabledState();
  rtpSyncTargetRtpFieldState();
  rtpUpdateDateContextLabels();
  if ((window.RTP_LIVE_CFG || {}).scope === 'game') {
    rtpUpdateGameListActiveRow(rtpGetActiveGameId());
    rtpSyncAvgRtpDisplay();
  }
  if (rtpLiveStats && Object.keys(rtpLiveStats).length) {
    rtpApplyLiveStatsToDom(rtpLiveStats);
    updateRtpSlabCalculations();
  } else {
    rtpLoadStatsForSelectedDate();
  }
  setInterval(function() {
    if (!rtpIsTodaySelected()) return;
    rtpFetchLiveStats(function() { updateRtpSlabCalculations(); });
  }, 60000);
})(jQuery);
