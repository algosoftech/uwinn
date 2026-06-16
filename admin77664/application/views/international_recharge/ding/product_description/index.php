<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title"></div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLDINGPRODUCTSDATA',getCurrentControllerPath('index')); ?>">Ding</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Product Description List</a></li>
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
                        <a href="javaScript:void(0)" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
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
                                                        <option value="2"   <?php if ($perpage == '2') echo 'selected="selected"'; ?>>2</option>
                                                        <option value="10"  <?php if ($perpage == '10') echo 'selected="selected"'; ?>>10</option>
                                                        <option value="25"  <?php if ($perpage == '25') echo 'selected="selected"'; ?>>25</option>
                                                        <option value="50"  <?php if ($perpage == '50') echo 'selected="selected"'; ?>>50</option>
                                                        <option value="100" <?php if ($perpage == '100') echo 'selected="selected"'; ?>>100</option>
                                                        <option value="All" <?php if ($perpage == 'All') echo 'selected="selected"'; ?>>All</option>
                                                    </select>
                                                    entries
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                                <option value="">Select Field</option>
                                                <option value="DisplayText" <?php if ($searchField == 'DisplayText') echo 'selected="selected"'; ?>>Display Text</option>
                                                <option value="LocalizationKey" <?php if ($searchField == 'LocalizationKey') echo 'selected="selected"'; ?>>Localization Key</option>
                                                <option value="LanguageCode" <?php if ($searchField == 'LanguageCode') echo 'selected="selected"'; ?>>Language Code</option>
                                                <option value="status" <?php if ($searchField == 'status') echo 'selected="selected"'; ?>>Status (A = Active, I = Inactive)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="datetime-local" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate ? date('Y-m-d\TH:i', strtotime($fromDate)) : ''; ?>" class="form-control form-control-sm" placeholder="From Date">
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="datetime-local" name="toDate" id="toDate" autocomplete="off" value="<?php echo $toDate ? date('Y-m-d\TH:i', strtotime($toDate)) : ''; ?>" class="form-control form-control-sm" placeholder="To Date">
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
                                                    <th width="20%">Display Text</th>
                                                    <th width="25%">Read More Markdown</th>
                                                    <th width="15%">Localization Key</th>
                                                    <th width="10%">Language</th>
                                                    <th width="15%">Status</th>
                                                    <th width="15%">ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody style="text-align: center;">
                                                <?php
                                                if (!empty($ALLDATA)):
                                                    $i = $first;
                                                    $j = 0;
                                                    foreach ($ALLDATA as $row):
                                                        $row = json_decode(json_encode($row), true);
                                                        $rowClass = ($j % 2 == 0) ? 'odd' : 'even';
                                                    ?>
                                                        <tr role="row" class="<?php echo $rowClass; ?>">
                                                           
                                                            <td><?php echo $i++; ?></td>
                                                            <td><?php echo htmlspecialchars($row['DisplayText'] ?? 'N/A'); ?></td>
                                                            <td style="text-align:left;">
                                                                <?php
                                                                    $readMoreText = trim((string)($row['ReadMoreMarkdown'] ?? ''));
                                                                    if($readMoreText == ''){
                                                                        echo 'N/A';
                                                                    }else{
                                                                        $shortText = (strlen($readMoreText) > 160) ? substr($readMoreText, 0, 160).'...' : $readMoreText;
                                                                        echo htmlspecialchars($shortText);
                                                                    }
                                                                ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($row['LocalizationKey'] ?? 'N/A'); ?></td>
                                                            <td><?php echo htmlspecialchars($row['LanguageCode'] ?? 'N/A'); ?></td>
                                                            <td><?=showStatus($row['status']) ?></td>
                                                           
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('addeditdata/'.$row['_id']['$id'])?>" onclick="return confirm('Are you sure you want to edit this description?')"> <i class="fas fa-edit"></i> Edit</a>
                                                                    </li>
                                                                    <?php if($row['status'] == 'A'): ?>
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$row['_id']['$id'].'/I')?>" onclick="return confirm('Are you sure you want to update the status of this description?')"> <i class="fas fa-thumbs-down"></i> Inactive</a>
                                                                        </a>
                                                                    </li>
                                                                    <?php elseif($row['status'] == 'I'): ?>
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$row['_id']['$id'].'/A')?>" onclick="return confirm('Are you sure you want to update the status of this description?')"> <i class="fas fa-thumbs-up"></i> Active</a>
                                                                    </li>
                                                                    <?php endif; ?>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php $j++; endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="7" style="text-align:center;">No Ding Product Description List found.</td>
                                                        </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" role="status" aria-live="polite"><?php echo $noOfContent ?? ''; ?></div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <?php echo $PAGINATION ?? ''; ?>
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


<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Download Ding Product Description Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
      <div class="row mt-2">
          <div class="col-sm-12 col-md-6">
            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                <option value="">Select Field</option>
                <option value="DisplayText" <?php if ($searchField == 'DisplayText') echo 'selected="selected"'; ?>>Display Text</option>
                <option value="LocalizationKey" <?php if ($searchField == 'LocalizationKey') echo 'selected="selected"'; ?>>Localization Key</option>
                <option value="LanguageCode" <?php if ($searchField == 'LanguageCode') echo 'selected="selected"'; ?>>Language Code</option>
                <option value="status" <?php if ($searchField == 'status') echo 'selected="selected"'; ?>>Status</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
          </div>
        </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <label for="recipient-name" class="col-form-label">Form:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <label for="recipient-name" class="col-form-label">To:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="toDate" id="toDate" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Download Report</button>
      </div>
      </form>
    </div>
  </div>
</div>