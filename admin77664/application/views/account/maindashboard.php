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
<!-- Loding animation CSS -->
<style>
   .loader {
      border: 16px solid #f3f3f3; /* Light grey */
      border-top: 16px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 100px;
      height: 100px;
      animation: spin 2s linear infinite;
      margin: 0 auto;
   }
   .loader {
      border-top: 16px solid blue;
      border-right: 16px solid green;
      border-bottom: 16px solid red;
      }

   @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
   }
   .hide{
      display: none;
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
                     <li class="breadcrumb-item">
                        <a href="<?=base_url('/maindashboard');?>">
                           <i class="feather icon-home"></i>
                        </a>
                     </li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
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
               <!-- <div class="card-header">
                  <h5>Dashboard</h5>
               </div> -->
               <div class="card-body">
                  <div class="card-block">
                     <div class="row ">
                        <div class="container-fluid">
                           <div class="panel panel-headline">
                              <div class="panel-body">
                                 <div class="row box_guard">
                                    <!-- <h1 style="margin-left: 20%;">Welcome DealzArabia</h1>
                                    <img style="width: 93%;" src="http://localhost/dealzarabia/assets/1648737920295860.png"> -->
                                    
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-success">
                                             <h4>Ticket Soldout</h4>
                                          </div>
                                          <div class="d-card-body">
                                          <!-- loder start -->
                                          <div class="loader ticket-loader"></div>
                                             <div class="table-responsive ticket-table hide">
                                                <table class="table">
                                                   <thead>
                                                      <tr>
                                                         <th>Product ID</th>
                                                         <th>Product Name</th>
                                                         <th>Soldout</th>
                                                         <th>Total Quantity</th>
                                                         <th>Created Date</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="ticket-table-body">
                                                      
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}tickets/alltickets/index"><button>view more</button></a></center>
                                       </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-success">
                                             <h4>Campaign sales</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <!-- loder start -->
                                             <div class="loader campaign-sales-loader"></div>
                                             <div class="table-responsive">
                                                <table class="table campaign-sales-table hide">
                                                   <thead>
                                                      <tr>
                                                         <th>Product ID</th>
                                                         <th>Product Name</th>
                                                         <th>Target Quantity</th>
                                                         <th>Sold Quantity</th>
                                                         <th>Expiry Date & Time</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="campaign-sales-body">
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}campaignsales/sales/index"><button>view more</button></a></center>
                                       </div>
                                    </div>

                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-c-yellow">
                                             <h4>Sponsored</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <!-- loder start -->
                                             <div class="loader sponsored-loader"></div>
                                             <div class="table-responsive">
                                                <table class="table sponsored-table hide">
                                                   <thead>
                                                      <tr>
                                                         <tr>
                                                         <th>Product ID</th>
                                                         <th>Product Name</th>
                                                         <!-- <th>Sponsored</th> -->
                                                         <th>Not Sponsored</th>
                                                      </tr>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="sponsored-body">
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}campaignsales/sponsored/index"><button>view more</button></a></center>
                                       </div>
                                    </div>
                                    <!-- Referral Report -->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-c-yellow">
                                             <h4>Referral Report</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <!-- loder start -->
                                             <div class="loader referral-loader"></div>
                                             <div class="table-responsive">
                                                <table class="table referral-table hide">
                                                   <thead>
                                                      <tr>
                                                         <tr>
                                                         <th>Receiver Name</th>
                                                         <th>Campaign Name</th>
                                                         <th>Amount</th>
                                                         <th>Sender</th>
                                                         <th>Date & Time</th>
                                                      </tr>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="referral-body">
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}campaignsales/referralreport/index"><button>view more</button></a></center>
                                       </div>
                                    </div>
                                    <!-- END -->
                                    <!-- Signup Bonus Report -->
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-c-yellow">
                                             <h4>Signup Bonus Report</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <!-- loder start -->
                                             <div class="loader signup-bonus-loader"></div>
                                             <div class="table-responsive">
                                                <table class="table signup-bonus-table" >
                                                   <thead>
                                                      <tr>
                                                         <tr>
                                                         <th>Signup Date</th>
                                                         <th>User Name</th>
                                                         <th>Bonus Amount</th>
                                                      </tr>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="signup-bonus-body">
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}campaignsales/signupbonus/index"><button>view more</button></a></center>
                                       </div>
                                    </div>
                                    <!-- END -->

                                     <!-- Membership Cashback Report -->
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-c-yellow">
                                             <h4>Membership Cashback Report</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <!-- loder start -->
                                             <div class="loader membership-loader"></div>
                                             <div class="table-responsive">
                                                <table class="table membership-table hide">
                                                   <thead>
                                                      <tr>
                                                         <th>Created Date</th>
                                                         <th>User TYPE</th>
                                                         <th>User Name</th>
                                                         <th>Cashback Amount</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="membership-body">
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}cashback/membershipcashback/index"><button>view more</button></a></center>
                                       </div>
                                    </div>
                                    <!-- END -->

                                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                                       <div class="dashboard-card">
                                          <div class="d-card-header bg-c-blue">
                                             <h4>Recharge History</h4>
                                          </div>
                                          <div class="d-card-body">
                                             <!-- loder start -->
                                             <div class="loader recharge-loader"></div>
                                             <div class="table-responsive">
                                                <table class="table recharge-table hide">
                                                   <thead>
                                                      <tr>
                                                         <th width="20%">Arabian Points</th>
                                                        <th width="20%">Record Type</th>
                                                        <th width="20%">User Type</th>
                                                        <th width="20%">Email ID </th>
                                                        <th width="20%">Recharge At</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody id="recharge-body">
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <center><a href="{BASE_URL}recharge/allrecharge/index"><button>view more</button></a></center>
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
<script type="text/javascript">
$(document).ready(function(){
   var ur      = '<?=base_url().'/account/getTicketData'?>';
   $.ajax({
      url : ur,
      method: "POST", 
      success: function(data){
         $('#ticket-table-body').empty().append(data);
         $('.ticket-loader').addClass('hide');
         $('.ticket-loader').removeClass('loader');
         $('.ticket-table').removeClass('hide');
         getCampaignSales();
      }
   });
   //get campain sales data
   function getCampaignSales(){
      var path      = '<?=base_url().'/account/getCampaignSalesData'?>';
      $.ajax({
         url : path,
         method: "POST", 
         success: function(data){
            $('#campaign-sales-body').empty().append(data);
            $('.campaign-sales-loader').addClass('hide');
            $('.campaign-sales-loader').removeClass('loader');
            $('.campaign-sales-table').removeClass('hide');
            getSponsoredData();
         }
      });
   }

   //get sponsored data
   function getSponsoredData(){
      var path      = '<?=base_url().'/account/getSponsoredData'?>';
      $.ajax({
         url : path,
         method: "POST", 
         success: function(data){
            $('#sponsored-body').empty().append(data);
            $('.sponsored-loader').addClass('hide');
            $('.sponsored-loader').removeClass('loader');
            $('.sponsored-table').removeClass('hide')
            getReferralData();
         }
      });
   }

   //get referral data
   function getReferralData(){
      var path      = '<?=base_url().'/account/getRefferalData'?>';
      $.ajax({
         url : path,
         method: "POST", 
         success: function(data){
            $('#referral-body').empty().append(data);
            $('.referral-loader').addClass('hide');
            $('.referral-loader').removeClass('loader');
            $('.referral-table').removeClass('hide');
            getSignupBonusData();
         }
      });
   }

   //get Signup Bonus data
   function getSignupBonusData(){
      var path      = '<?=base_url().'/account/getSignupBonusData'?>';
      $.ajax({
         url : path,
         method: "POST", 
         success: function(data){
            $('#signup-bonus-body').empty().append(data);
            $('.signup-bonus-loader').addClass('hide');
            $('.signup-bonus-loader').removeClass('loader');
            $('.signup-bonus-table').removeClass('hide');
            getMembershipData();
         }
      });
   }

   //get Membership data
   function getMembershipData(){
      var path      = '<?=base_url().'/account/getMembershipData'?>';
      $.ajax({
         url : path,
         method: "POST", 
         success: function(data){
            $('#membership-body').empty().append(data);
            $('.membership-loader').addClass('hide');
            $('.membership-loader').removeClass('loader');
            $('.membership-table').removeClass('hide');
            getRechargeData();
         }
      });
   }

   //get Recharge data
   function getRechargeData(){
      var path      = '<?=base_url().'/account/getRechargeData'?>';
      $.ajax({
         url : path,
         method: "POST", 
         success: function(data){
            $('#recharge-body').empty().append(data);
            $('.recharge-loader').addClass('hide');
            $('.recharge-loader').removeClass('loader');
            $('.recharge-table').removeClass('hide')
         }
      });
   }
   

});
</script>