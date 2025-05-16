<style type="text/css">
  .card {
    background-color: #fff;
    border-radius: 10px;
    border: none;
    position: relative;
    margin-bottom: 30px;
    box-shadow: 0 0.46875rem 2.1875rem rgba(90,97,105,0.1), 0 0.9375rem 1.40625rem rgba(90,97,105,0.1), 0 0.25rem 0.53125rem rgba(90,97,105,0.12), 0 0.125rem 0.1875rem rgba(90,97,105,0.1);
}
.card .card-statistic-4 {
    position: relative;
    color: #000000;
    padding: 15px;
    border-radius: 3px;
    overflow: hidden;
}
.font-15 {
    font-size: 15px !important;
}
.font-18 {
    font-size: 18px !important;
}
.card {
    position: relative;
    display: -ms-flexbox;
    display: -webkit-box;
    display: flex;
    -ms-flex-direction: column;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
}
.pcoded-main-container {
  margin-left: 0px;
}
.pcoded-micon {
    font-size: 16px;
    padding: 0;
    margin-right: 5px;
    border-radius: 4px;
    width: 30px;
    display: inline-flex;
    align-items: center;
    height: 30px;
    text-align: center;
    justify-content: center;
    background: #fff;
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="feather icon-home"></i> Home</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card table-card review-card">
                    <div class="col-md-12 col-sm-12 col-xs-12 p-space">
                        <div class="card">
                            <div class="card-header">
                                <h5>Main Dashboard</h5>
                            </div>
                            <div class="card-body">
                                <div class="card-block">
                                    <div class="row ">
                                        <div class="container-fluid">
                                          <div class="panel panel-headline">
                                            <div class="panel-body">
                                              <div class="row box_guard">
                                                <?php if($moduleData <> ""): foreach($moduleData as $moduleInfo): ?>
                                                   <div class="col-md-4 col-sm-4 col-xs-6">
                                                    <div class="card">
                                                      <div class="card-content bg-1"> 
                                                        <a href="{BASE_URL}<?php echo $moduleInfo['module_name'].'/'.$moduleInfo['first_data'].'/index'?>" title="Readers">
                                                          <div class="media align-items-stretch">
                                                            <div class="p-2 text-center bg-primary bg-darken-1 absobox"><span class="pcoded-micon"><?php echo stripslashes($moduleInfo['module_icone']); ?></span></div>
                                                            <div class="p-2 bg-gradient-x-primary white media-body">
                                                              <h5><?php echo stripslashes($moduleInfo['module_display_name']); ?></h5>
                                                            </div>
                                                          </div>
                                                        </a> 
                                                      </div>
                                                    </div>
                                                  </div>
                                                <?php endforeach; endif; ?>
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
        <!-- [ Main Content ] end -->
    </div>
</div>