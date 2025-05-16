<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Account Deletion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <style type="text/css">
    .logo{
        // min-width: 160px;
        max-width: 220px;
        width: 145px;
        margin-top: 20px;: 
    }
  </style>

</head>
<body>


<?php if($step == 'delete-request'): ?>
  <div class="container">

    <div class="container m-auto">
        <img src="<?=base_url('assets/img/u-winn.png');?>" class="logo"> 
        <h6 class="mt-2">U WINN TRADING L.L.C</h6>
        
    </div>

    <h1 class="mt-5">Request Account Deletion</h1>
    <p class="mt-3">To request account deletion, please provide your registered email address or mobile number. Additionally, please provide a reason for deletion.</p>
    
    <!-- Flash Messages -->
    <?php if($this->session->flashdata('alert_error')):?> 
      <div class="alert alert-danger"><?=$this->session->flashdata('alert_error');?></div> 
    <?php endif; ?>
    
    <?php if($this->session->flashdata('alert_success')):?> 
      <div class="alert alert-success"><?=$this->session->flashdata('alert_success');?></div> 
    <?php endif; ?>
    
    <form action="<?=base_url('delete-request')?>" method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Registered Email Address</label>
        <input type="email" name="email" id="email" class="form-control" value="<?=set_value('email')?>">
        <?php if(form_error('email')): ?>
          <div class="text-danger"><?=form_error('email')?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label for="mobile" class="form-label">Registered Mobile Number</label>
        <input type="tel" name="mobile" id="mobile" class="form-control" value="<?=set_value('mobile')?>">
        <?php if(form_error('mobile')): ?>
          <div class="text-danger"><?=form_error('mobile')?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <label for="reason" class="form-label">Reason for Deletion</label>
        <textarea name="reason" id="reason" class="form-control" rows="4" required><?=set_value('reason')?></textarea>
        <?php if(form_error('reason')): ?>
          <div class="text-danger"><?=form_error('reason')?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <input type="hidden" name="SaveChanges" value="SaveChanges">
        <input type="submit" value="Request Account Deletion" class="btn btn-primary">
      </div>
    </form>
    
    <p class="mt-3">By clicking "Request Account Deletion", you agree that all data associated with your account will be permanently deleted and cannot be restored.</p>
  </div>
<?php elseif($step == 'verify-otp'): ?>
   <div class="container">
    <h1 class="mt-5">Verify Request Account Deletion</h1>
    
    <!-- Flash Messages -->
    <?php if($this->session->flashdata('alert_error')):?> 
      <div class="alert alert-danger"><?=$this->session->flashdata('alert_error');?></div> 
    <?php endif; ?>
    
    <?php if($this->session->flashdata('alert_success')):?> 
      <div class="alert alert-success"><?=$this->session->flashdata('alert_success');?></div> 
    <?php endif; ?>
    
    <form action="<?=base_url('verify-otp')?>" method="POST">
      
      <div class="mb-3">
        <label for="otp" class="form-label">Enter OTP</label>
        <input type="text" name="otp" id="otp" class="form-control" value="<?=set_value('otp')?>">
        <?php if(form_error('otp')): ?>
          <div class="text-danger"><?=form_error('otp')?></div>
        <?php endif; ?>
      </div>
      
      <div class="mb-3">
        <input type="hidden" name="SaveChanges" value="SaveChanges">
        <input type="submit" value="Request Account Deletion" class="btn btn-primary">
      </div>

    </form>
  </div>


<?php endif;?>



</body>
</html>
