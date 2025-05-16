<style>
.coupon-container{
    display: inline-flex;
}
.coupon-code-circle {
    border: 1px solid #40ABA8;
    color: #0E4391;
    border-radius: 50%;
    padding: 12px;
    font-weight: 900;
}

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
$(function(){
   $("#date").datepicker({dateFormat:'dd-mm-yy',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#fromTime").datetimepicker({datepicker: false, format: 'H:i',step: 15});
   $("#toTime").datetimepicker({datepicker: false, format: 'H:i',step: 15});
});
</script>

<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Logs</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
     <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header">
                <h5>All Logs</h5>
                <!-- <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a> -->
              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                    <div class="dt-responsive table-responsive">
                      <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                       <div class="row">
                          <div class="col-sm-12 col-md-12">
                            <div class="dataTables_length" id="simpletable_length">
                              <label>Show 
                                <select name="showLength" id="showLength" class="custom-select custom-select-sm form-control form-control-sm">
                                  <option value="2" <?php if($perpage == '2')echo 'selected="selected"'; ?>>2</option>
                                  <option value="10" <?php if($perpage == '10')echo 'selected="selected"'; ?>>10</option>
                                  <option value="25" <?php if($perpage == '25')echo 'selected="selected"'; ?>>25</option>
                                  <option value="50" <?php if($perpage == '50')echo 'selected="selected"'; ?>>50</option>
                                  <option value="100" <?php if($perpage == '100')echo 'selected="selected"'; ?>>100</option>
                                  <option value="All" <?php if($perpage == 'All')echo 'selected="selected"'; ?>>All</option>
                                </select>
                                entries
                              </label>
                            </div>
                          </div>
                          <div class="col-sm-3 col-md-3">
                              <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="">Select Field</option>
                                <option value="requested_url"  <?php if($searchField == 'requested_url')echo 'selected="selected"';?>> Request URL </option>
                                <!-- <option value="timestamp"      <?php if($searchField == 'timestamp')echo 'selected="selected"'; ?>>Requested At (Date) </option> -->
                                <option value="requested_data" <?php if($searchField == 'requested_data')echo 'selected="selected"'; ?>>Requested By</option>

                              </select>
                          </div>
                          <div class="col-sm-3 col-md-3">
                            <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                          </div>
                          <div class="col-sm-6 col-md-6">
                              <div class="row" >
                                <div class="col-sm-12 col-md-4">
                                  <input type="text" name="date" id="date" autocomplete="off" value="<?php echo $date; ?>" class="form-control form-control-sm" placeholder="Date">
                                </div>
                                <div class="col-sm-12 col-md-2">
                                  <input type="text" name="fromTime" id="fromTime" autocomplete="off" value="<?php echo $fromTime; ?>" class="form-control form-control-sm" placeholder="From Time">
                                </div>
                                <div class="col-sm-12 col-md-2">
                                  <input type="text" name="toTime" id="toTime" autocomplete="off" value="<?php echo $toTime; ?>" class="form-control form-control-sm" placeholder="To Time">
                                </div>
                                <div class="col-sm-12 col-md-4">
                                  <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                                </div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                      
                    <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                <th width="5%">S.No.</th>
                                <th width="20%">URL & Method</th>
                                <th width="20%">User Details</th>
                                <th width="20%">IP Address</th>
                                <th width="10%">Created at</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if (!empty($ALLDATA)): $i = $first; $j = 0; ?>
                                    <?php foreach ($ALLDATA as $index => $ALLDATAINFO): 
                                        $rowClass = ($j % 2 == 0) ? 'odd' : 'even'; ?>
                                        <tr role="row" class="<?= $rowClass; ?>">
                                          <td><?= $i++; ?></td>
                                          <td>
                                              <?php  $lastThree = array_slice(explode('/', $ALLDATAINFO['requested_url']), -3); ?>
                                              Url   : <?=strtoupper('<b>'.$lastThree[2].'</b> >> <b>'.$lastThree[1].'</b> >> <b>'.$lastThree[0].'</b>');?><br>
                                              Method: <?= htmlspecialchars($ALLDATAINFO['access_method']); ?><br>
                                          </td>
                                          <td>
                                            <?php foreach ($ALLDATAINFO['requested_data'] as $field => $value): ?>
                                              <?php if (!empty($value)): ?>
                                                <?php 
                                                  if ($field === 'userPassword' || $field === 'userOtp' ) {
                                                    $value = str_repeat('*', strlen($value));
                                                  }
                                                ?>
                                                 <?= htmlspecialchars($field).' : <b>'.htmlspecialchars($value).'</b><br>';?> 
                                              <?php endif; ?>
                                            <?php endforeach; ?>
                                             <?php if($ALLDATAINFO['loggedIn_userID']): ?>
                                               Logged In ID     :  <?= htmlspecialchars($ALLDATAINFO['loggedIn_userID']); ?><br>
                                            <?php endif; ?>
                                            <?php if($ALLDATAINFO['loggedIn_Email']): ?>
                                              Logged In Email  :  <?= htmlspecialchars($ALLDATAINFO['loggedIn_Email']); ?><br>
                                            <?php endif; ?>
                                            <?php if($ALLDATAINFO['loggedIn_MOBILE']): ?> 
                                              Logged In Mobile :  <?= htmlspecialchars($ALLDATAINFO['loggedIn_MOBILE']); ?><br>
                                            <?php endif; ?>
                                            
                                          </td>

                                          <td>
                                          <?php if (!empty($ALLDATAINFO['ip_address'])): ?>
                                            <?php foreach ($ALLDATAINFO['ip_address'] as $field => $value): ?>
                                              <?php if (!empty($value)): ?>
                                                <?php 
                                                  if ($field === 'country'):
                                                    echo "Country: ".htmlspecialchars($value).'<br>';
                                                  endif; 

                                                  if ($field === 'regionName'):
                                                    echo "regionName: ".htmlspecialchars($value).'<br>';
                                                  endif; 

                                                  if ($field === 'query'):
                                                    echo "IP: ".htmlspecialchars($value).'<br>';
                                                  endif; 
                                                ?>
                                               
                                              <?php endif; ?>
                                            <?php endforeach; ?>
                                          <?php endif; ?>
                                          </td>
                                          <td><?=date('Y-m-d H:i:s', strtotime($ALLDATAINFO['timestamp'])); ?></td>
                                        </tr>
                                      <?php if($ALLDATAINFO['loggedIn_userID'] == $this->session->userdata('UW_ADMIN_ID')): ?>
                                      <?php endif; ?>
                                    <?php $j++; endforeach; ?>
                                <?php else: ?>
                                  <tr>
                                      <td colspan="6" style="text-align:center;">No Data Available In Table</td>
                                  </tr>
                                <?php endif; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                    
                    <div class="row">
                      <div class="col-sm-12 col-md-5">
                        <div class="dataTables_info" role="status" aria-live="polite"><?php echo $noOfContent; ?></div>
                      </div>
                      <div class="col-sm-12 col-md-7">
                        <div class="dataTables_paginate paging_simple_numbers">
                          <?php echo $PAGINATION; ?>
                        </div>
                      </div>
                    </div>

                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
 