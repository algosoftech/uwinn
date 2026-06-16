

<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">International Recharge</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Voucher</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Add Voucher</a>
                    </div>
                    <div class="card-body">
                        <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                            <div class="dt-responsive table-responsive">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="dataTables_length">
                                            <label>Show
                                                <select name="showLength" id="showLength" class="custom-select custom-select-sm form-control form-control-sm">
                                                    <option value="10" <?php if ($perpage == '10') echo 'selected="selected"'; ?>>10</option>
                                                    <option value="25" <?php if ($perpage == '25') echo 'selected="selected"'; ?>>25</option>
                                                    <option value="50" <?php if ($perpage == '50') echo 'selected="selected"'; ?>>50</option>
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
                                            <option value="name" <?php if ($searchField == 'name') echo 'selected="selected"'; ?>>Voucher Name</option>
                                            <option value="provider_name" <?php if ($searchField == 'provider_name') echo 'selected="selected"'; ?>>Provider</option>
                                            <option value="message" <?php if ($searchField == 'message') echo 'selected="selected"'; ?>>Message</option>
                                            <option value="status" <?php if ($searchField == 'status') echo 'selected="selected"'; ?>>Status</option>
                                            <option value="sequence_order" <?php if ($searchField == 'sequence_order') echo 'selected="selected"'; ?>>Sequence Order</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-3 col-md-3">
                                        <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                                    </div>
                                    <div class="col-sm-6 col-md-6">
                                        <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered nowrap">
                                    <thead style="text-align:center;">
                                        <tr>
                                            <th width="5%">S.No.</th>
                                            <th width="15%">Image</th>
                                            <th>Voucher Name</th>
                                            <!-- <th>Provider</th> -->
                                            <!-- <th>Message</th> -->
                                            <th>Sequence</th>
                                            <th>Status</th>
                                            <th width="12%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align:center;">
                                        <?php if (!empty($ALLDATA)): $i = $first; foreach ($ALLDATA as $row):
                                            $row = is_object($row) ? json_decode(json_encode($row), true) : $row;
                                            $id = '';
                                            if (!empty($row['_id'])) {
                                                if (is_string($row['_id'])) {
                                                    $id = trim($row['_id']);
                                                } else {
                                                    $idEnc = is_array($row['_id']) ? $row['_id'] : json_decode(json_encode($row['_id']), true);
                                                    if (is_array($idEnc)) {
                                                        if (isset($idEnc['$id'])) {
                                                            $id = (string)$idEnc['$id'];
                                                        } elseif (isset($idEnc['$oid'])) {
                                                            $id = (string)$idEnc['$oid'];
                                                        }
                                                    } elseif (is_string($idEnc)) {
                                                        $id = trim($idEnc);
                                                    }
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td>
                                                    <?php
                                                    $imgSrc = '';
                                                    $imgFallback = '';
                                                    $imgVal = trim((string)($row['image'] ?? ($row['voucher_image'] ?? ($row['image_url'] ?? ''))));
                                                    $rootBase = rtrim(base_url(), '/') . '/';
                                                    $publicBase = str_replace('/admin77664/', '/', $rootBase);
                                                    if ($imgVal !== '') {
                                                        $imgVal = str_replace('\\', '/', $imgVal);
                                                        if (preg_match('/^https?:\/\//i', $imgVal)) {
                                                            $imgSrc = $imgVal;
                                                        } else {
                                                            if (strpos($imgVal, 'admin77664/assets/') !== false) {
                                                                $relativeImg = ltrim(substr($imgVal, strpos($imgVal, 'admin77664/assets/')), '/');
                                                                $imgSrc = $publicBase . $relativeImg;
                                                                $imgFallback = $rootBase . ltrim(substr($relativeImg, strlen('admin77664/')), '/');
                                                            } elseif (strpos($imgVal, 'assets/') === 0) {
                                                                $relativeImg = ltrim($imgVal, '/');
                                                                $imgSrc = $rootBase . $relativeImg;
                                                                $imgFallback = $publicBase . 'admin77664/' . $relativeImg;
                                                            } else {
                                                                $relativeImg = 'assets/admin/image/' . ltrim($imgVal, '/');
                                                                $imgSrc = $rootBase . $relativeImg;
                                                                $imgFallback = $publicBase . 'admin77664/' . $relativeImg;
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <?php if ($imgSrc !== ''): ?>
                                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($row['name'] ?? 'voucher'); ?>" width="100" height="100" onerror="this.onerror=null;<?php if ($imgFallback !== ''): ?>this.src='<?php echo htmlspecialchars($imgFallback); ?>';this.onerror=function(){this.outerHTML='N/A';};<?php else: ?>this.outerHTML='N/A';<?php endif; ?>">
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                                                <!-- <td>
                                                    <?php
                                                    if (!empty($row['provider_names']) && is_array($row['provider_names'])) {
                                                        echo htmlspecialchars(implode(', ', $row['provider_names']));
                                                    } else {
                                                        echo htmlspecialchars($row['provider_name'] ?? '');
                                                    }
                                                    ?>
                                                </td> -->
                                                <!-- <td><?php echo htmlspecialchars($row['message'] ?? ''); ?></td> -->
                                                <td><?php echo (int)($row['sequence_order'] ?? 0); ?></td>
                                                <td><?php echo showStatus($row['status'] ?? 'I'); ?></td>
                                                <td>
                                                    <?php if ($id !== ''): ?>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action</button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li>
                                                                    <a href="<?php echo getCurrentControllerPath('addeditdata/' . $id); ?>"><i class="fas fa-edit"></i> Edit</a>
                                                                </li>
                                                                <?php if (($row['status'] ?? 'I') == 'A'): ?>
                                                                    <li><a href="<?php echo getCurrentControllerPath('changestatus/' . $id . '/I'); ?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                                                <?php else: ?>
                                                                    <li><a href="<?php echo getCurrentControllerPath('changestatus/' . $id . '/A'); ?>"><i class="fas fa-thumbs-up"></i> Active</a></li>
                                                                <?php endif; ?>
                                                                <li><a href="<?php echo getCurrentControllerPath('deletedata/' . $id); ?>" onclick="return confirm('Are you sure you want to delete this voucher?');"><i class="fas fa-trash"></i> Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; else: ?>
                                            <tr>
                                                <td colspan="8" style="text-align:center;">No voucher found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info"><?php echo $noOfContent ?? ''; ?></div>
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
    </div>
</div>
