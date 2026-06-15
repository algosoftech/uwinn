<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#fromDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
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
                        <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=base_url('/'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Result</a></li>
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
                <h5>Manage All Result</h5>
               <!-- Edit Product settings -->

               <!-- Add U Product start -->
                <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger pull-right mr-2">Bulk Delete</button>
                <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right mr-2">Add Daily Result</a>
                <a href="<?php echo getCurrentControllerPath('settings'); ?>" class="btn btn-sm btn-primary pull-right mr-2">Settings</a>
               <!-- Add U Product end -->

              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                     <div class="row">
                        <div class="col-sm-12 col-md-6">
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
                        <div class="col-sm-12 col-md-6 ">
                          <div class="row pull-right " >
                            <div class="col-sm-12 col-md-8 ">
                              <input type="text" name="result_date" id="fromDate" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                            </div>
                             
                            <div class="col-sm-12 col-md-4">
                              <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
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
                                <th width="5%" style="text-align: center;">
                                  <input type="checkbox" id="select-all" title="Select All">
                                </th>
                                <th width="5%" style="text-align: center;">S.No.</th>
                                <th width="25%">Result Date</th>
                                <th width="25%">total Result Count</th>
                                <th width="10%">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                    <?php $resultDateTs = (int)$ALLDATAINFO['_id']; ?>
                                    <td style="text-align: center;">
                                      <input type="checkbox" name="delete[]" class="delete-row" value="<?=$resultDateTs;?>">
                                    </td>
                                    <td><?=$i++;?></td>
                                    <td><?=$resultDateTs > 0 ? date("Y-m-d", $resultDateTs) : '';?></td>
                                    <td><?=$ALLDATAINFO['count'];?></td>
                                    <td>
                                      <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                        <ul class="dropdown-menu" role="menu">
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('viewdata/'.$resultDateTs)?>"><i class="fas fa-edit"></i> View Details</a>
                                          </li>
                                        </ul>
                                      </div>
                                    </td>
                                  
                                </tr>
                                <?php $j++; endforeach; else: ?>
                                <tr>
                                  <td colspan="5" style="text-align:center;">No Data Available In Table</td>
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

<script>
$(function() {
  $('#select-all').on('change', function() {
    $('.delete-row').prop('checked', $(this).is(':checked'));
  });

  $(document).on('change', '.delete-row', function() {
    var totalRows = $('.delete-row').length;
    var checkedRows = $('.delete-row:checked').length;
    $('#select-all').prop('checked', totalRows > 0 && totalRows === checkedRows);
  });

  $('#bulk-delete-btn').on('click', function() {
    var selectedIds = [];
    $('.delete-row:checked').each(function() {
      selectedIds.push($(this).val());
    });

    if(selectedIds.length === 0) {
      alert('Please select at least one row to delete.');
      return;
    }

    if(!confirm('Delete all results for ' + selectedIds.length + ' selected date(s)?')) {
      return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true);

    $.ajax({
      type: 'POST',
      url: '<?php echo getCurrentControllerPath("bulkdeletedata"); ?>',
      data: { ids: selectedIds },
      dataType: 'json',
      success: function(response) {
        if(response.status === true) {
          window.location.reload();
        } else {
          alert(response.message || 'Failed to delete selected items.');
          $btn.prop('disabled', false);
        }
      },
      error: function() {
        alert('Failed to delete selected items. Please try again.');
        $btn.prop('disabled', false);
      }
    });
  });
});
</script>