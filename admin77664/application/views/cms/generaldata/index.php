<style type="text/css">
  .scrollabletextbox1 {
    height: 50%;
    width: 618px;
    overflow: scroll;
    line-height: 3em;
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
                        <li class="breadcrumb-item"><a href="{FULL_SITE_URL}dashboard"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Website</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">General Data</a></li>
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
                <h5>General Data</h5>
                <!-- <a href="<?php echo base_url()?>website/websitecms/websitehomeslider/index" class="btn btn-sm btn-primary pull-right section"> Home Page Slider</a> -->
                <!-- <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Add General Data</a> -->
              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                     
                      <div class="row">
                        <div class="col-sm-12">
							<div class="table-responsive">
								<table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                            <thead style="text-align: center">
                              <tr role="row">
                                <th width="5%">S.No.</th>
                                <th width="20%">Email </th>
                                <th width="15%">Contact No. </th>
                                 <th width="50%">Address</th>
                                <th width="10%">Action</th>
                              </tr>
                            </thead>
                            <tbody style="text-align: center">
                              <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                              ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td><?=$i++?></td>
                                  <td><?=stripslashes(ucfirst($ALLDATAINFO['email_id']))?></td>
                                  <td><?=stripslashes(ucfirst($ALLDATAINFO['contact_no']))?></td>
                                   <td><textarea rows="3" cols="50" readonly><?=((strip_tags($ALLDATAINFO['address'])))?></textarea></td>
                                 
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['general_data_id'])?>"><i class="fas fa-edit"></i> Edit Details</a></li>
                                        
                                      </ul>
                                    </div>
                                  </td>
                                </tr>
                              <?php $j++; endforeach; else: ?>
                                <tr>
                                  <td colspan="4" style="text-align:center;">No Data Available In Table</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
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