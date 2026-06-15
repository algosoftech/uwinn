<?php
$shopFromDate = date('Y-m-d\T00:00:00');
$shopToDate = date('Y-m-d\T23:59:59');
?>
<div class="modal fade" id="shopExportModal" tabindex="-1" role="dialog" aria-labelledby="shopExportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shopExportModalLabel">Download Hourly Shop Excel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportshopexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <label for="shopFromDate" class="col-form-label">From:</label>
            <input type="datetime-local" name="fromDate" id="shopFromDate" step="1" value="<?php echo $shopFromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
          </div>
          <div class="col-sm-12 col-md-6">
            <label for="shopToDate" class="col-form-label">To:</label>
            <input type="datetime-local" name="toDate" id="shopToDate" step="1" value="<?php echo $shopToDate; ?>" class="form-control form-control-sm" placeholder="To Date">
          </div>
        </div>
        <div class="row mt-2">
          <?php if(!empty($ALLHOURLYGAMES)): foreach($ALLHOURLYGAMES as $hourlyGame): ?>
            <div class="col-sm-12 col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="gameIds[]" value="<?=$hourlyGame['products_id']?>" id="shop_hourly_game_<?=$hourlyGame['products_id']?>">
                <label class="form-check-label" for="shop_hourly_game_<?=$hourlyGame['products_id']?>"><?=htmlspecialchars($hourlyGame['title'] ?? '')?></label>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Download Shop Excel</button>
      </div>
      </form>
    </div>
  </div>
</div>
