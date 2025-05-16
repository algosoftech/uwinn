<div class="col-md-9">
    <div class="card_Big my_profiledetails">
        <div class="container">
        <p class="profile_Details">Profile Details</p>
        <form action="<?=base_url('my-profile');?>" method="POST" enctype="multipart/form-data" class="contact-form">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                   <div class="profile-section"> 
                        <img src="<?= base_url($userDetails['profile']?$userDetails['profile']: 'assets/img/NO_IMAGE.jpg');?>" width="75"> 
                        <?php if($userDetails['profile'] !== ""):?>
                            <span class="delete-pic" data-profile="<?=$userDetails['profile'];?>"  >x</span>
                        <?php endif;?>
                    </div>


                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <div class="inner-form-content">
                           
                            <input type="FILE" class="placeholder-box" name="profile" id="profile" placeholder="Profile" value="<?=(set_value('profile')) ? set_value('profile') : $userDetails['profile']; ?>" accept=".png,.jpeg,.jpg">
                            <?php if(form_error('profile')): ?>
                             <span for="profile" generated="true" class="help-inline"><?php echo form_error('profile'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="name">First Name</label>
                        <input type="text" class="placeholder-box" name="first_name" id="first_name" placeholder="First Name" value="<?=(set_value('first_name')) ? set_value('first_name') : $userDetails['users_name']; ?>">
                        <?php if(form_error('first_name')): ?>
                         <span for="first_name" generated="true" class="help-inline"><?php echo form_error('first_name'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                    <label for="name">Last Name</label>
                        <input type="text" class="placeholder-box" name="last_name" id="last_name" placeholder="Last Name" value="<?=(set_value('last_name')) ? set_value('last_name') : $userDetails['last_name']; ?>">
                        <?php if(form_error('last_name')): ?>
                         <span for="last_name" generated="true" class="help-inline"><?php echo form_error('last_name'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="name">Country Code</label>
                        <?php if(set_value('country_code')): $country_Code = set_value('country_code'); elseif($userDetails['country_code']): $country_Code = $userDetails['country_code']; else: $country_Code = '+971'; endif; ?>
                        <select name="country_code" id="country_code"  placeholder="Country Code*" class="placeholder-box" required  disabled/>
                            <option value=''>Select</option>
                            <?php if($country_code):?>
                                <?php foreach ($country_code as $countryCode => $CountryName): ?>
                                  <option value="<?=$countryCode;?>" <?php if($country_Code == $countryCode): echo 'selected="selected"'; endif; ?>><?=$CountryName?></option>  
                                <?php endforeach ?>
                            <?php endif;?>
                        </select>
                        <?php if(form_error('country_code')): ?>
                         <span for="country_code" generated="true" class="help-inline"><?php echo form_error('country_code'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="name">Mobile Number</label>
                        <input type="text" class="placeholder-box" name="mobile" id="mobile" placeholder="Mobile" value="<?=(set_value('mobile')) ? set_value('mobile') : $userDetails['users_mobile']; ?>" disabled>
                        <?php if(form_error('mobile')): ?>
                          <span for="mobile" generated="true" class="help-inline"><?php echo form_error('mobile'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="name">Email Address</label>
                        <input type="email" class="placeholder-box" name="email" id="users_email" placeholder="contact@moralizer.com" value="<?=(set_value('users_email')) ? set_value('users_email') : $userDetails['users_email']; ?>">
                         <?php if(form_error('users_email')): ?>
                          <span for="users_email" generated="true" class="help-inline"><?php echo form_error('users_email'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
               <div class="row">
                    <div class="col-ms-6 col-md-6 col-lg-6 text-start">
                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                        <input type="submit" value="Update Details" class="btn-submit" />
                    </div>
                    <div class="col-ms-6 col-md-6 col-lg-6 text-end">
                        <a href="<?=base_url('profile/reset-password');?>" class="btn-reset"> Reset Password </a>
                    </div>
               </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).on('click', '.delete-pic', function() {
        let Profile = $(this).data('profile');
        let usersID = "<?= Encript($this->session->userdata('users_id')); ?>";
        let deleteButton = $(this); // Save reference to the clicked button

        if (confirm("Sure to delete?")) {
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


