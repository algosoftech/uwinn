<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<style type="text/css">
   /*.d-card-body {
   overflow-y: auto;
   height: 300px;
   }*/
   #container {
   height: 400px;
   }
   .highcharts-figure, .highcharts-data-table table {
   min-width: 310px;
   max-width: 800px;
   margin: 1em auto;
   }
   #datatable {
   font-family: Verdana, sans-serif;
   border-collapse: collapse;
   border: 1px solid #EBEBEB;
   margin: 10px auto;
   text-align: center;
   width: 100%;
   max-width: 500px;
   }
   #datatable caption {
   padding: 1em 0;
   font-size: 1.2em;
   color: #555;
   }
   #datatable th {
   font-weight: 600;
   padding: 0.5em;
   }
   #datatable td, #datatable th, #datatable caption {
   padding: 0.5em;
   }
   #datatable thead tr, #datatable tr:nth-child(even) {
   background: #f8f8f8;
   }
   #datatable tr:hover {
   background: #f1f7ff;
   }
</style>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                          <li class="breadcrumb-item"><a href="javascript:void(0);">Orders</a></li>
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
                <h5>Order Statistics Reports</h5>
                <!-- <a href="<?php echo getCurrentControllerPath('getStatisticsByUserID') ?>" class="btn btn-sm btn-primary pull-right" style="margin-left:10px;">Get Statistics by email</a> -->
              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                      <div class="dt-responsive table-responsive">
                        <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                          <div class="row">
                            <div class="col-sm-3 col-md-3">
                              <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="">Select Field</option>
                                <option value="user_phone" <?php if($searchField == 'user_phone')echo 'selected="selected"'; ?>>Mobile</option>
                                <option value="user_email" <?php if($searchField == 'user_email')echo 'selected="selected"'; ?>>Email</option>
                                <option value="user_type" <?php if($searchField == 'user_type')echo 'selected="selected"'; ?>>User Type</option>
                                <option value="user_type" <?php if($searchField == 'user_type')echo 'selected="selected"'; ?>>User Type</option>
                                <option value="app_version" <?php if($searchField == 'app_version')echo 'selected="selected"'; ?>>App Version</option>
                                <option value="bind_with" <?php if($searchField == 'bind_with')echo 'selected="selected"'; ?>>Bind With (Mobile/Email)</option>
                              <!--   <option value="straight_add_on_amount" <?php if($searchField == 'straight_add_on_amount')echo 'selected="selected"'; ?>> straight Orders</option>
                                <option value="rumble_add_on_amount" <?php if($searchField == 'rumble_add_on_amount')echo 'selected="selected"'; ?>> Rumble Orders</option>
                                <option value="reverse_add_on_amount" <?php if($searchField == 'reverse_add_on_amount')echo 'selected="selected"'; ?>> Chance Orders</option> -->
                              </select>
                            </div>
                            <div class="col-sm-3 col-md-3">
                              <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                            </div>
                            <div class="col-sm-6 col-md-6">
                                <div class="row" >
                                  <div class="col-sm-12 col-md-4">
                                    <input type="datetime-local" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                                  </div>
                                  <div class="col-sm-12 col-md-4">
                                    <input type="datetime-local" name="toDate" id="toDate" autocomplete="off" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="To Date">
                                  </div>
                                  <div class="col-sm-12 col-md-4">
                                    <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                                  </div>
                                </div>
                            </div>
                          </div>
                        </div>
                      </div>


                    <?php  
                     $sales =0;
                     $ReportRecord = array();
                     foreach ($HourReport as $key1 => $Report):
                      foreach ($Report as $key2 => $ReportItems):
                            $ReportRecord[] = $ReportItems;
                            $sales          += $ReportItems['sales'];
                            $total_order    += $ReportItems['total_order'];
                      endforeach;
                     endforeach;
                     
                     foreach ($HourReport as $key1 => $Report):
                      foreach ($Report as $key2 => $ArrayHeading):
                            $ArrayHeading = array_keys($ArrayHeading);    
                      endforeach;
                     endforeach;
                     $ArrayHeading = str_replace('_', ' ',  $ArrayHeading);
                    ?>

                      <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <figure class="highcharts-figure">
                              <div id="container"></div>
                              <table id="datatable" style="display:none;">
                                  <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Order Statistics</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                        foreach ($ReportRecord as $key => $item): ?>
                                          <tr>
                                              <th><?php echo date('H:i', strtotime($item['start_time'])) .' - '.date('H:i', strtotime($item['end_time'])) ;?></th>
                                              <td><?php echo $item['sales'];?></td>
                                          </tr>
                                          <?php endforeach; ?>
                                  </tbody>
                              </table>
                            </figure>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                          <table id="simpletable" class="table table-striped table-bordered nowrap dataTable w-25" role="grid" aria-describedby="simpletable_info">
                            <thead style="text-align: center;">
                              <tr role="row">
                                <th width="5%">Total Orders</th>
                                <th width="5%">Total Sales</th>
                              </tr>
                            </thead>
                            <tbody style="text-align: center;">
                              <tr>
                                <td><?=$total_order; ?></td>
                                <td><?=$sales; ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <?php if(!empty($ArrayHeading)): ?>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                                <thead style="text-align: center;">
                                  <tr role="row">
                                    <?php foreach ($ArrayHeading as $key => $item): ?>
                                      <?php if($key >0): ?>
                                        <th width="5%"><?=ucwords($item);?></th>
                                      <?php endif; ?>
                                    <?php endforeach; ?>
                                  </tr>
                                </thead>
                                <tbody style="text-align: center;">
                                  <?php if($ReportRecord <> ""): foreach($ReportRecord as $listKey=>  $ALLDATAINFO): ?>
                                    <tr>
                                      <?php if($j%2==0): $rowClass = 'odd';   else: $rowClass = 'even'; endif; ?>
                                      <?php foreach($ALLDATAINFO as $listKey => $item): ?>
                                        <?php if($listKey != '_id'): ?>
                                          <td>
                                            <?=$item;?>
                                          </td>
                                        <?php endif; ?>
                                      <?php endforeach; ?>
                                    </tr>
                                  <?php endforeach; else: ?>
                                    <tr>
                                      <td colspan="6" style="text-align:center;">No Data Available In Table</td>
                                    </tr>
                                  <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
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

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
   Highcharts.chart('container', {
    data: {
      table: 'datatable'
    },
    chart: {
      type: 'column'
    },
    title: {
      text: 'Statistics chart'
    },
    yAxis: {
      allowDecimals: false,
      title: {
        text: 'Units'
      }
    },
    tooltip: {
      formatter: function () {
        return '<b>' + this.series.name + '</b><br/>' +
          this.point.y + ' ' + this.point.name.toLowerCase();
      }
    }
   });
</script>