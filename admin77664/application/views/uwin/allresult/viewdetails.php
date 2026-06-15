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
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">All Result</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">View Details</a></li>
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
                        <h5>Result Details</h5>
                        
                        <a href="<?php echo getCurrentControllerPath('index'); ?>" class="btn btn-sm btn-primary pull-right mr-2">Back</a>
                        <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right mr-2">Add Daily Result</a>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($EDITDATA)): 
                            $resultDate = isset($EDITDATA[0]['result_date']) ? date("Y-m-d", $EDITDATA[0]['result_date']) : '';
                            $totalCount = count($EDITDATA);
                        ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6><strong>Result Date:</strong> <?php echo $resultDate; ?></h6>
                                </div>
                                <div class="col-md-6 text-right">
                                  
                                    <h6><strong>Total Results:</strong> <?php echo $totalCount; ?></h6>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="resultDetailsTable" class="table table-striped table-bordered nowrap" style="width:100%">
                                    <thead style="text-align: center; background-color: #f8f9fa;">
                                        <tr>
                                            <th width="5%" style="text-align: center;">S.No.</th>
                                         
                                            <th width="25%">Product Name</th>
                                            <th width="25%">Draw Time</th>
                                            <th width="20%">Draw Result</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align: center;">
                                        <?php 
                                        $i = 1;
                                        foreach($EDITDATA as $resultInfo): 
                                            $rowClass = ($i % 2 == 0) ? 'even' : 'odd';
                                            $statusClass = (isset($resultInfo['status']) && $resultInfo['status'] == 'A') ? 'badge-success' : 'badge-danger';
                                            $statusText = (isset($resultInfo['status']) && $resultInfo['status'] == 'A') ? 'Active' : 'Inactive';
                                            
                                            // Get _id properly - handle both object and string formats
                                            $resultId = '';
                                            if(isset($resultInfo['_id'])):
                                                if(is_object($resultInfo['_id']) && isset($resultInfo['_id']->{'$id'})):
                                                    $resultId = $resultInfo['_id']->{'$id'};
                                                elseif(is_array($resultInfo['_id']) && isset($resultInfo['_id']['$id'])):
                                                    $resultId = $resultInfo['_id']['$id'];
                                                else:
                                                    $resultId = $resultInfo['_id'];
                                                endif;
                                            endif;
                                            
                                            // Format draw_result - split comma-separated values
                                            $drawResult = isset($resultInfo['draw_result']) ? trim($resultInfo['draw_result']) : '';
                                            $drawResultArray = !empty($drawResult) ? explode(',', $drawResult) : array();
                                        ?>
                                            <tr class="<?php echo $rowClass; ?>">
                                                <td><?php echo $i; ?></td>
                                                <td style="text-align: center;">
                                                    <?php echo isset($resultInfo['product_name']) ? htmlspecialchars($resultInfo['product_name']) : 'N/A'; ?>
                                                </td>
                                                <td style="text-align: center;">
                                                    <?php echo isset($resultInfo['draw_result_time']) ? htmlspecialchars($resultInfo['draw_result_time']) : 'N/A'; ?>
                                                </td>
                                                <td>
                                                    <?php if(!empty($drawResultArray)): ?>
                                                        <div class="draw-result-display">
                                                            <?php foreach($drawResultArray as $index => $number): ?>
                                                                <span class="badge badge-primary mr-1" style="font-size: 0.9em; padding: 4px 8px;"><?php echo trim($number); ?></span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <strong>N/A</strong>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                                </td>
                                                <td>
                                                    <?php if(!empty($resultId)): ?>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Action
                                                            </button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li>
                                                                    <a href="<?php echo getCurrentControllerPath('addeditdata/'.$resultId); ?>" class="dropdown-item">
                                                                        <i class="fas fa-edit"></i> Edit
                                                                    </a>
                                                                </li>
                                                                <?php if(isset($resultInfo['status']) && $resultInfo['status'] == 'A'): ?>
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$resultId.'/I'); ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to deactivate this result?');">
                                                                            <i class="fas fa-ban"></i> Deactivate
                                                                        </a>
                                                                    </li>
                                                                <?php else: ?>
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$resultId.'/A'); ?>" class="dropdown-item" onclick="return confirm('Are you sure you want to activate this result?');">
                                                                            <i class="fas fa-check"></i> Activate
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <a href="<?php echo getCurrentControllerPath('deletedata/'.$resultId); ?>" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this result? This action cannot be undone.');">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php 
                                        $i++;
                                        endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                <i class="feather icon-info"></i> No result details found for this date.
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?php echo getCurrentControllerPath('index'); ?>" class="btn btn-secondary">
                                    <i class="feather icon-arrow-left"></i> Back to List
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>