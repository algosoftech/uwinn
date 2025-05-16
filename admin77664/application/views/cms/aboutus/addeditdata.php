<style>

  .image_remove{
    cursor: pointer;
  }

</style>
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSABOUTUD',getCurrentControllerPath('index')); ?>"> About Us</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA ? 'Edit' : 'Add'?> About Us</a></li>
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
                <h5><?=$EDITDATA ? 'Edit' : 'Add'?> About Us</h5>
                <a href="<?php echo correctLink('CMSABOUTUD',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                      <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="about_id"/>
                      <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['about_id']?>"/>
                      <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['about_id']?>"/>
                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                     
                      <div id="from-input-container">
                        <?php if($EDITDATA['sections']): ?>
                          <?php foreach($EDITDATA['sections'] as $key => $ItemArray): $Sno = $key + 1; ?>
                            <div class="input-section">
                            <?=validation_errors();?>
                              <fieldset>
                                <legend>Section <?=$Sno;?></legend>
                                <div class="row">
                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                    <label>Title<span class="required">*</span></label>
                                    <input type="text" name="title[]" id="title" class="form-control required" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($ItemArray->title); endif; ?>" placeholder="Title">
                                    <?php if(form_error('title')): ?>
                                      <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                                    <label>Image<span class="required">*</span></label>
                                    <input type="file" name="image[]" id="image" value="<?php if(set_value('image')): echo set_value('image'); endif; ?>" accept="image/png, image/jpeg, image/webp">
                                     <p style="font-family:italic; color:red;">[Web Image Size : 1600 x 795 px in jpg/jpeg/png]</p>
                                      <?php if($ItemArray->image): ?>
                                        <input type="hidden" name="old_image[]" value="<?=$ItemArray->image;?>" class="old_image" />
                                        <img src="<?php echo fileBaseUrl . $ItemArray->image; ?>" width="150" border="0" alt="">&nbsp;
                                        <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="" class="image_remove">
                                      <?php endif; ?>
                                    <?php if(form_error('image')): ?>
                                      <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>
                               
                                <div class="row">
                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('description')): ?>error<?php endif; ?>">
                                    <label>Description<span class="required">*</span></label>
                                    <textarea id="description<?=$Sno?>" name="description[]" class="form-control required"><?php if(set_value('description')): echo set_value('description'); else: echo stripslashes($ItemArray->description); endif; ?></textarea>
                                    <?php if(form_error('description')): ?>
                                      <span for="description" generated="true" class="help-inline"><?php echo form_error('description'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>
                                <button type="button" class="remove-section btn btn-danger">Remove Section</button>
                              </fieldset>
                            </div>
                          <?php endforeach; ?>
                        <?php else:?> 

                          <div class="input-section">
                              <fieldset>
                                <legend>Section 1 </legend>
                                <div class="row">
                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                    <label>Title<span class="required">*</span></label>
                                    <input type="text" name="title[]" id="title" class="form-control required" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($ItemArray->title); endif; ?>" placeholder="Title">
                                    <?php if(form_error('title')): ?>
                                      <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>

                                <div class="row">
                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                                    <label>Image<span class="required">*</span></label>
                                    <input type="file" name="image[]" id="image" value="<?php if(set_value('image')): echo set_value('image'); endif; ?>" accept="image/png, image/jpeg, image/webp">
                                     <p style="font-family:italic; color:red;">[Web Image Size : 1600 x 795 px in jpg/jpeg/png]</p>
                                      <?php if($ItemArray->image): ?>
                                        <img src="<?php echo fileBaseUrl . $ItemArray->image; ?>" width="150" border="0" alt="">&nbsp;
                                      <?php endif; ?>
                                    <?php if(form_error('image')): ?>
                                      <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>
                               
                                <div class="row">
                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('description')): ?>error<?php endif; ?>">
                                    <label>Description<span class="required">*</span></label>
                                    <textarea id="description<?=$Sno?>" name="description[]" class="form-control required"><?php if(set_value('description')): echo set_value('description'); else: echo stripslashes($ItemArray->description); endif; ?></textarea>
                                    <?php if(form_error('description')): ?>
                                      <span for="description" generated="true" class="help-inline"><?php echo form_error('description'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>
                              </fieldset>
                            </div>

                        <?php endif; ?>
                      </div>

                      <!-- Buttons Container -->
                      <div class="row">
                        <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                          <div class="inline-remember-me mt-4">
                            <button type="button" id="add-section" class="btn btn-secondary mb-4">Add Section</button>
                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                            <button class="btn btn-primary mb-4">Submit</button>
                            <a href="<?php echo correctLink('CMSABOUTUD', getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
                            <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong> </span> 
                          </div>
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
  $(document).ready(function() {
    function updateSectionLegends() {
      $('#from-input-container .input-section').each(function(index) {
        $(this).find('legend').text('Section ' + (index + 1));
      });
    }

    function createEditorsForAllTextareas() {
      $('#from-input-container textarea').each(function() {
        create_editor_for_textarea($(this).attr('id'));
      });
    }

    // Function to add a new section
    $('#add-section').click(function() {
      var sectionCount = $('#from-input-container .input-section').length + 1;
      var newSectionId = 'description' + sectionCount;
      var newSection = `
        <div class="input-section">
          <fieldset>
            <legend>Section ` + sectionCount + `</legend>
            <div class="row">
              <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Title<span class="required">*</span></label>
                <input type="text" name="title[]" class="form-control" placeholder="Title">
              </div>
            </div>
            <div class="row">
              <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <label>Image<span class="required">*</span></label>
                <input type="file" name="image[]" accept="image/png, image/jpeg, image/webp">
                <p style="font-family:italic; color:red;">[Web Image Size : 1600 x 795 px in jpg/jpeg/png]</p>
              </div>
            </div>
            <div class="row">
              <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <label>Description<span class="required">*</span></label>
                <textarea id="` + newSectionId + `" name="description[]" class="form-control required"></textarea>
              </div>
            </div>
            <button type="button" class="remove-section btn btn-danger">Remove Section</button>
          </fieldset>
        </div>`;
      $('#from-input-container').append(newSection);
      updateSectionLegends();
      create_editor_for_textarea(newSectionId);
    });

    // Function to remove a section
    $(document).on('click', '.remove-section', function() {
      $(this).closest('.input-section').remove();
      updateSectionLegends();
    });

    // Initialize editors for existing textareas on page load
    createEditorsForAllTextareas();
  });
</script>


<script>
  $(document).on('click','.image_remove', function(){
    if(confirm('Do you want to delete image')){
      $(this).siblings('img').remove();
      $(this).siblings('.old_image').val('');
      $(this).remove();
    }
  });
</script>