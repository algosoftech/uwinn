<!-- <link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/user.image.canvasCrop.css">
 --><link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/about.image.canvasCrop.css">
<script type="text/javascript" src="{ASSET_INCLUDE_URL}canvasCrop/jquery.canvasCrop.js"></script>
<style type="text/css">
  input#show_vat {
    margin-right: 30%;
    margin-left: 6px;
}
fieldset{
  border: 2px solid #49CCED;
  margin: unset;
  padding: unset;
  width: 80%;
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
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSTERMSCONDITIONS',getCurrentControllerPath('index')); ?>"> Contents</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Contents</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Contents</h5>
                <a href="<?php echo correctLink('CMSTERMSCONDITIONS',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                   <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="content_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['content_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['content_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class='row'>
                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('upload_type')): ?>error<?php endif; ?>">
                                <label>Upload Type <span class="required">*</span></label>
                                <select id="upload_type" name="upload_type" class="form-control required">
                                <option value="">select</option>
                                <option value="image_section" <?= isset($EDITDATA['upload_type']) && $EDITDATA['upload_type']== 'image_section'? 'selected':'';?>>Image</option>
                                <option value="video_section" <?= isset($EDITDATA['upload_type']) && $EDITDATA['upload_type']== 'video_section'? 'selected':'';?>>Video</option>
                                <option value="link_section"  <?= isset($EDITDATA['upload_type']) && $EDITDATA['upload_type']== 'link_section'? 'selected':'';?>>Link</option>
                                </select>
                                <?php if(form_error('upload_type')): ?>
                                <span for="upload_type" generated="true" class="help-inline"><?php echo form_error('upload_type'); ?></span>
                                <?php endif; ?>
                            </div>
                             
                        </div>

                        <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('position')): ?>error<?php endif; ?>">
                            <label>Position (Current )<span class="required">*</span></label>
                            <input type="number" name="position" id="position" class="form-control required" value="<?php if(set_value('position')): echo set_value('position'); else: echo stripslashes($EDITDATA['position']);endif; ?>" placeholder="Position">
                            <?php if(form_error('position')): ?>
                            <span for="position" generated="true" class="help-inline"><?php echo form_error('position'); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('live_date_time')): ?>error<?php endif; ?>">
                            <label> Lauching Date & Time<span class="required">*</span></label>
                            <input type="datetime-local" name="live_date_time" id="live_date_time" class="form-control required" value="<?php if(set_value('live_date_time')): echo set_value('live_date_time'); else: echo $EDITDATA['live_date_time'] ? date('Y-m-d H:i',$EDITDATA['live_date_time']) : date('Y-m-d H:i') ;endif; ?>" placeholder="live_date_time">
                            <?php if(form_error('live_date_time')): ?>
                            <span for="live_date_time" generated="true" class="help-inline"><?php echo form_error('live_date_time'); ?></span>
                            <?php endif; ?>
                        </div>

                         <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('new_position')): ?>error<?php endif; ?>">
                            <label>New Position ( Later )</label>
                            <input type="number" name="new_position" id="new_position" class="form-control" value="<?php if(set_value('new_position')): echo set_value('new_position'); else: echo stripslashes($EDITDATA['new_position']);endif; ?>" placeholder="Position Later">
                            <?php if(form_error('new_position')): ?>
                            <span for="new_position" generated="true" class="help-inline"><?php echo form_error('new_position'); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('live_date_time_later')): ?>error<?php endif; ?>">
                            <label> Lauching Date & Time ( Later)</label>
                            <input type="datetime-local" name="live_date_time_later" id="live_date_time_later" class="form-control" value="<?php if(set_value('live_date_time_later')): echo set_value('live_date_time_later'); else: echo $EDITDATA['live_date_time_later'] ? date('Y-m-d H:i', $EDITDATA['live_date_time_later']) : date('Y-m-d H:i') ;endif; ?>" placeholder="live_date_time_later">
                            <?php if(form_error('live_date_time_later')): ?>
                            <span for="live_date_time_later" generated="true" class="help-inline"><?php echo form_error('live_date_time_later'); ?></span>
                            <?php endif; ?>
                        </div>
                       
                    </div>


                        
                    </div>
                     
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12 image_section contetnt-section <?= $EDITDATA['upload_type'] != 'image_section'? 'd-none':'';?>">
                        <legend>Image Section</legend>
                        <div class="row">
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                              <label>Image</label><br>
                              <input type="file" name="image" id="image" class="" value="<?php if(set_value('image')): echo set_value('image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['image'])){ ?> required <?php } ?> >
                              <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                              <?php if($EDITDATA['image']): ?>
                              <img src="<?php echo fileBaseUrl.$EDITDATA['image']; ?>" width="250" border="0" alt="">
                              <?php endif; ?>
                              <?php if(form_error('image')): ?>
                              <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12 video_section contetnt-section <?= $EDITDATA['upload_type'] != 'video_section'? 'd-none':'';?>">
                        <legend>Video Section</legend>
                        <div class="row">
                          <!-- <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('video')): ?>error<?php endif; ?>">
                            <label>Video</label><br>
                            <input type="file" name="video" id="video" class="" value="<?php if(set_value('video')): echo set_value('video'); endif; ?>" accept="video/mp4, video/avi, video/webm" <?php if(empty($EDITDATA['video'])){ ?> required <?php } ?>>
                            <p style="font-family:italic; color:red;">[Recommended Video Size: 241 x 136 px in MP4/AVI/WebM]</p>
                            <?php if($EDITDATA['video']): ?>
                            <video width="250" controls>
                                <source src="<?php echo fileBaseUrl.$EDITDATA['video']; ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <?php endif; ?>
                            <?php if(form_error('video')): ?>
                            <span for="video" generated="true" class="help-inline"><?php echo form_error('video'); ?></span>
                            <?php endif; ?>
                          </div> -->
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('video')): ?>error<?php endif; ?>">
                              <label>video</label><br>
                              <input type="file" name="video" id="video" value="<?php if(set_value('video')): echo set_value('video'); endif; ?>"  accept="video/mp4, video/avi, video/webm" <?php if(empty($EDITDATA['video'])){ ?> required <?php } ?>>
                              <p style="font-family:italic; color:red;">[Recommended video Size: 241 x 136 px in MP4/AVI/WebM]</p>
                              <?php if($EDITDATA['video']): ?>
                              <video width="250" controls>
                                  <source src="<?php echo fileBaseUrl.$EDITDATA['video']; ?>" type="video/mp4">
                                  Your browser does not support the video tag.
                              </video>
                              <?php endif; ?>
                              <?php if(form_error('video')): ?>
                              <span for="video" generated="true" class="help-inline"><?php echo form_error('video'); ?></span>
                              <?php endif; ?>
                          </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12 link_section contetnt-section <?= $EDITDATA['upload_type'] != 'link_section'? 'd-none':'';?>">
                        <legend>Link section</legend>
                        <div class="row">
                          <!-- URL Link -->
                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('link_url')): ?>error<?php endif; ?>">
                              <label>URL Link</label><br>
                              <input type="url" name="link_url" id="link_url" value="<?=set_value('link_url') ?set_value('link_url') : $EDITDATA['link_url']; ?>" class="form-control" required>
                              <?php if(form_error('link_url')): ?>
                              <span for="link_url" generated="true" class="help-inline"><?php echo form_error('link_url'); ?></span>
                              <?php endif; ?>
                          </div>

                           <!-- Title -->
                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('link_title')): ?>error<?php endif; ?>">
                              <label>Link Title</label><br>
                              <input type="text" name="link_title" id="link_title" value="<?=set_value('link_title') ?set_value('link_title') : $EDITDATA['link_title']; ?>" class="form-control" required>
                              <?php if(form_error('link_title')): ?>
                              <span for="link_title" generated="true" class="help-inline"><?php echo form_error('link_title'); ?></span>
                              <?php endif; ?>
                          </div>

                          <!-- Game Type -->
                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('game_type')): ?>error<?php endif; ?>">
                              <label>Game Type</label><br>
                              <input type="text" name="game_type" id="game_type" value="<?=set_value('game_type') ?set_value('game_type') : $EDITDATA['game_type']; ?>" class="form-control" required>
                              <?php if(form_error('game_type')): ?>
                              <span for="game_type" generated="true" class="help-inline"><?php echo form_error('game_type'); ?></span>
                              <?php endif; ?>
                          </div>

                          <!-- link_thumbnail Image -->
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('link_thumbnail')): ?>error<?php endif; ?>">
                              <label>thumbnail</label><br>
                              <input type="file" name="link_thumbnail" id="link_thumbnail" accept="image/jpeg, image/png, image/webp" class="form-control" required>
                              <?php if($EDITDATA['link_thumbnail']): ?>
                              <img src="<?php echo fileBaseUrl.$EDITDATA['link_thumbnail']; ?>" width="250" border="0" alt="">
                              <?php endif; ?>
                              <?php if(form_error('link_thumbnail')): ?>
                              <span for="link_thumbnail" generated="true" class="help-inline"><?php echo form_error('link_thumbnail'); ?></span>
                              <?php endif; ?>
                          </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <legend>Top Banner</legend>
                        <div class="row">
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_top_banner[]" value="Website" id="Website" <?= isset($EDITDATA['added_for_top_banner']) && in_array('Website', $EDITDATA['added_for_top_banner']) ? 'checked' : ''; ?>>
                                    <label for="Website">Website</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_top_banner[]" value="App" id="App" <?= isset($EDITDATA['added_for_top_banner']) && in_array('App', $EDITDATA['added_for_top_banner']) ? 'checked' : ''; ?>>
                                    <label for="App">App</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_top_banner[]" value="POS" id="POS" <?= isset($EDITDATA['added_for_top_banner']) && in_array('POS', $EDITDATA['added_for_top_banner']) ? 'checked' : ''; ?>>
                                    <label for="POS">POS</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for[]" value="Top Banner" id="top_banner" <?= isset($EDITDATA['added_for']) && in_array('Top Banner', $EDITDATA['added_for']) ? 'checked' : ''; ?>>
                                    <label for="top_banner">Top Banner</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <legend>Recent Winners</legend>
                        <div class="row">
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_recent_winners[]" value="Website" id="Website2" <?= isset($EDITDATA['added_for_recent_winners']) && in_array('Website', $EDITDATA['added_for_recent_winners']) ? 'checked' : ''; ?>>
                                    <label for="Website2">Website</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_recent_winners[]" value="App" id="App2" <?= isset($EDITDATA['added_for_recent_winners']) && in_array('App', $EDITDATA['added_for_recent_winners']) ? 'checked' : ''; ?>>
                                    <label for="App2">App</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_recent_winners[]" value="POS" id="POS2" <?= isset($EDITDATA['added_for_recent_winners']) && in_array('POS', $EDITDATA['added_for_recent_winners']) ? 'checked' : ''; ?>>
                                    <label for="POS2">POS</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for[]" value="Recent Winners" id="recent_winners" <?= isset($EDITDATA['added_for']) && in_array('Recent Winners', $EDITDATA['added_for']) ? 'checked' : ''; ?>>
                                    <label for="recent_winners">Recent Winners</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <legend>Result Page</legend>
                        <div class="row">
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_result_page[]" value="Website" id="Website3" <?= isset($EDITDATA['added_for_result_page']) && in_array('Website', $EDITDATA['added_for_result_page']) ? 'checked' : ''; ?>>
                                    <label for="Website3">Website</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_result_page[]" value="App" id="App3" <?= isset($EDITDATA['added_for_result_page']) && in_array('App', $EDITDATA['added_for_result_page']) ? 'checked' : ''; ?>>
                                    <label for="App3">App</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_result_page[]" value="POS" id="POS3" <?= isset($EDITDATA['added_for_result_page']) && in_array('POS', $EDITDATA['added_for_result_page']) ? 'checked' : ''; ?>>
                                    <label for="POS3">POS</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for[]" value="Result Page" id="result_page" <?= isset($EDITDATA['added_for']['result_page']) && in_array('Result Page', $EDITDATA['added_for']['result_page']) ? 'checked' : ''; ?>>
                                    <label for="result_page">Result Page</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <legend>Winner Gallery</legend>
                        <div class="row">
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_winner_gallery[]" value="Website" id="Website4" <?= isset($EDITDATA['added_for_winner_gallery']) && in_array('Website', $EDITDATA['added_for_winner_gallery']) ? 'checked' : ''; ?>>
                                    <label for="Website4">Website</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_winner_gallery[]" value="App" id="App4" <?= isset($EDITDATA['added_for_winner_gallery']) && in_array('App', $EDITDATA['added_for_winner_gallery']) ? 'checked' : ''; ?>>
                                    <label for="App4">App</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_winner_gallery[]" value="POS" id="POS4" <?= isset($EDITDATA['added_for_winner_gallery']) && in_array('POS', $EDITDATA['added_for_winner_gallery']) ? 'checked' : ''; ?>>
                                    <label for="POS4">POS</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for[]" value="Winner Gallery" id="winner_gallery" <?= isset($EDITDATA['added_for']) && in_array('Winner Gallery', $EDITDATA['added_for']) ? 'checked' : ''; ?>>
                                    <label for="winner_gallery">Winner Gallery</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <legend>Scratch Card Banner</legend>
                        <div class="row">
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_scratch_card_banner[]" value="Website" id="Website5" <?= isset($EDITDATA['added_for_scratch_card_banner']) && in_array('Website', $EDITDATA['added_for_scratch_card_banner']) ? 'checked' : ''; ?>>
                                    <label for="Website3">Website</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_scratch_card_banner[]" value="App" id="App5" <?= isset($EDITDATA['added_for_scratch_card_banner']) && in_array('App', $EDITDATA['added_for_scratch_card_banner']) ? 'checked' : ''; ?>>
                                    <label for="App3">App</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-lg-4 col-md-4 col-xs-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for_scratch_card_banner[]" value="POS" id="POS5" <?= isset($EDITDATA['added_for_scratch_card_banner']) && in_array('POS', $EDITDATA['added_for_scratch_card_banner']) ? 'checked' : ''; ?>>
                                    <label for="POS3">POS</label>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <div class="form-group-inner">
                                    <input type="checkbox" name="added_for[]" value="Scratch Card Banner" id="scratch_card_banner" <?= isset($EDITDATA['added_for']) && in_array('Scratch Card Banner', $EDITDATA['added_for']) ? 'checked' : ''; ?>>
                                    <label for="scratch_card_banner">Scratch Card Banner</label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                            <button class="btn btn-primary mb-4">Submit</button>
                            <a href="<?php echo correctLink('CMSTERMSCONDITIONS', getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
                            <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong></span>
                        </div>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
</script>
<script>
  $(document).ready(function() {
      $(document).on('change','#upload_type' , function() {
        SeletedType = $(this).val();
        $('.contetnt-section').addClass('d-none');
        $('.'+SeletedType).removeClass('d-none')
      })
  });

</script>