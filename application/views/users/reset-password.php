<div class="col-md-9">
    <div class="card_Big my_profiledetails">
        <div class="container">
        <form action="<?=base_url('profile/reset-password');?>" method="POST" enctype="multipart/form-data" class="contact-form">
            <div class="row">
                
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="otp">OTP</label>
                        <input type="text" class="placeholder-box" name="otp" id="otp" placeholder="OTP">
                        <?php if(form_error('otp')): ?>
                         <span for="otp" generated="true" class="help-inline"><?php echo form_error('otp'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="new_password">New Password</label>
                        <input type="password" class="placeholder-box" name="new_password" id="new_password" placeholder="New Password">
                        <?php if(form_error('new_password')): ?>
                         <span for="new_password" generated="true" class="help-inline"><?php echo form_error('new_password'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <div class="inner-form-content">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="placeholder-box" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                        <?php if(form_error('confirm_password')): ?>
                         <span for="confirm_password" generated="true" class="help-inline"><?php echo form_error('confirm_password'); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-ms-6 col-md-6 col-lg-6 text-start">
                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                        <input type="submit" value="Update Password" class="btn-submit" />
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


