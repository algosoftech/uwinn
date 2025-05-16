 
            <div class="row">
                <div class="col-12 px-0">
                    <div class="myprofile">
                       <div  class="profile_details">
                        <h2>Profile Details</h2>

                       <div class="profile_innersection">
                        <div class="upload_img profile-section">
                            <img src="<?= base_url($userDetails['profile']?$userDetails['profile']: 'assets/img/NO_IMAGE.jpg');?>" class="Profile_updates"> 
                            <?php if($userDetails['profile'] !== ""):?>
                                <span class="delete-pic" data-profile="<?=$userDetails['profile'];?>"  >x</span>
                            <?php endif;?>
                            <i class="bi bi-pencil edit_icon" id="edit_profile"></i>
                        </div>
                          <div>
                            <ul>
                                <li>
                                    <a href="#">
                                        <i class="bi bi-person"></i> <?= $userDetails['users_name'];?>
                                    </a>
                                </li>
                                <li>
                                     <a href="emailto:<?=$userDetails['users_email'];?>">
                                        <i class="bi bi-envelope"></i> <?=$userDetails['users_email'];?>
                                    </a>
                                </li>
                                <li>
                                    <a href="tel:<?=$userDetails['country_code'].$userDetails['users_mobile'];?>">
                                        <i class="bi bi-telephone"></i> <?=$userDetails['country_code'].$userDetails['users_mobile'];?>
                                    </a>
                                </li>
                            </ul>
                          </div>
                           <div class="mt-3 d-flex">
                            <button class="update_profile" id="update_profile">Update Details</button>
                            <button class="reset_password"><a href="<?=base_url('profile/reset-password');?>">Reset Password</a></button>
                          </div>
                       </div>
                       </div>
                    </div>
                </div>
            </div>
        
    <form action="<?=base_url('my-profile');?>" method="POST" enctype="multipart/form-data" class="contact-form">
        <div class="image_upload" tabindex="-1" role="dialog">
            <div class="modal-dialog mx-0 my-0" role="document">
              <div class="modal-content">
                <button type="button" class="cross"><i class="bi bi-x"></i></button>
                <div class="modal-body">
                  <h1>Update Profile</h1>

                    <div class="btn-container">
                        <div id="uploadBtn" class="custom-btn" data-type="uploadBtn" >
                            <label> Upload Image   </label>
                        </div>
                        <div id="captureBtn" class="custom-btn" data-type="captureBtn">
                            <label> Capture Image   </label>
                        </div>
                    </div>
                    <input type="file" id="imageFile" capture="user" name="profile" accept="image/*" style="display:none;" />
                    <input type="file" id="avatar" name="profile1" accept="image/png, image/jpeg" style="display:none;" />
                    <!-- Placeholder for the uploaded image -->
                    <div id="imagePreview d-none">
                      <img id="previewImg" src="" alt="Image Preview" style="display:none; max-width: 100%; height: auto; margin-top: 10px;" />
                    </div>
                    <button type="submit" value="Submit" class="contact_btn">Submit</button>
                </div>
              </div>
            </div>
          </div>
        <div class="Voucher_model popup  <?= !empty(validation_errors()) ? 'modal-active' : ''; ?>" tabindex="-1" role="dialog">
            <?=validation_errors();?>
            <div class="modal-dialog mx-0 my-0" role="document">
                <div class="modal-content">
                    <div class="modal-body pb-0">
                 
                        <i class="bi bi-x cross_icons_headingpops"></i>
                        <h1> Update Profile</h1>

                        <div class="form-group row">
                            <div class="col-sm-12 col-md-12 col-lg-12 p-0">
                                <input type="text" class="placeholder-box" name="first_name" id="first_name" placeholder="Enter First Name" value="<?=set_value('first_name',$userDetails['users_name']);?>">
                                <?php if(form_error('first_name')): ?>
                                  <span for="first_name" generated="true" class="error-class"><?php echo form_error('first_name'); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="col-sm-12 col-md-12 col-lg-12 p-0">
                                <input type="text" class="placeholder-box" name="last_name" id="last_name" placeholder="Enter Last Name" value="<?=set_value('last_name',$userDetails['last_name']);?>">
                                <?php if(form_error('last_name')): ?>
                                  <span for="last_name" generated="true" class="error-class"><?php echo form_error('last_name'); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="col-sm-12 col-md-12 col-lg-12 p-0">
                                <input type="email" class="placeholder-box" name="email" id="users_email" placeholder="Enter Email ID" value="<?=set_value('email',$userDetails['users_email']);?>">
                                <?php if(form_error('email')): ?>
                                  <span for="email" generated="true" class="error-class"><?php echo form_error('email'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-center">
                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                            <button type="submit" value="Submit" class="contact_btn">Submit</button>
                        </div>
                 
                    </div>
                </div>
            </div>
        </div>
    </form>
     <!--More menu List End-->
        
    <script>
    $(document).on('click', '.delete-pic', function() {
        let Profile = $(this).data('profile');
        let usersID = "<?= Encript($this->session->userdata('users_id')); ?>";
        let deleteButton = $(this); // Save reference to the clicked button

        if (confirm("Do you want to delete?")) {
            $.ajax({
                type: 'post',
                url: "<?= base_url('delete-profile-image') ?>",
                data: { imageName: Profile, id: usersID },
                success: function(result) {
                    if (result == 1) {
                        console.log(result);
                        // Update the image src after deletion
                        deleteButton.closest('div').find('img').attr('src', '<?= base_url("assets/img/NO_IMAGE.jpg") ?>');
                        deleteButton.remove();
                    } else {
                        console.log('Failed to delete the image.');
                    }
                },
                error: function() {
                    console.log('Error while processing the request.');
                }
            });
        }
    });

</script>      
