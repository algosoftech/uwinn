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
                     <?php /* ?>
                     <h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5>
                     <?php */ ?>
                  </div>
                  <ul class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);">Campaign sales</a></li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      <!-- [ breadcrumb ] end -->
      <!-- [ Main Content ] start -->
      <div class="row">
         <div class="col-lg-12 col-md-12">
            <div class="card">
               <div class="card-body">
                  <div class="card-block">
                     <div class="row ">
                        <div class="container-fluid">
                           <div class="panel panel-headline">
                              <div class="panel-body">
                                 <div class="row box_guard">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-success">
                                             <h4>Campaign sales</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <div class="table-responsive">
                                                <table class="table">
                                                   <thead>
                                                      <tr>
                                                         <th>Product ID</th>
                                                         <th>Product Name</th>
                                                         <th>Target Quantity</th>
                                                         <th>Sold Quantity</th>
                                                         <th>Expiry Date & Time</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <?php if($orderTargetData): foreach($orderTargetData as $orderTarget):?>
                                                      <tr>
                                                         <td><?php echo stripslashes($orderTarget['product_seq_id']);?></td>
                                                         <td><?php echo stripslashes($orderTarget['title']);?></td>
                                                         <td><?php echo stripslashes($orderTarget['target_stock']);?></td>
                                                         <td><?php echo stripslashes($orderTarget['total_sale']);?></td>
                                                         <td><?php echo date('d-M-Y H:i A', strtotime($orderTarget['validuptodate'].' '.$orderTarget['validuptotime'])); ?></td>
                                                      </tr>
                                                      <?php endforeach; endif;?>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>