<header class="navbar pcoded-header navbar-expand-lg navbar-light header-blue">
  <div class="m-header">
    <a href="{FULL_SITE_URL }" class="b-brand">
      <img src="{ASSET_INCLUDE_URL}image/inner_logo.png" alt="" class="logo" style="width:90%">
      <img src="{ASSET_INCLUDE_URL}image/inner_logo.png" alt="" class="logo-thumb">
    </a>
    <a href="javascript:void(0);" class="mob-toggler">
      <i class="feather icon-more-vertical"></i>
    </a>
  </div>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ml-auto">
      <li>
        <div class="dropdown drp-user">
          <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
            <i class="feather icon-user"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right profile-notification">
            <div class="pro-head">
              <img src="<?php echo showCorrectImage(sessionData('HCAP_ADMIN_IMAGE'),'thumb','profile'); ?>" class="img-radius" alt="User-Profile-Image">
              <span><?php echo stripslashes(sessionData('HCAP_ADMIN_FIRST_NAME')); ?></span>
            </div>
            <ul class="pro-body">
              <li><a href="{FULL_SITE_URL}profile" class="dropdown-item"><i class="feather icon-user"></i> My Profile</a></li>
              <li><a href="{FULL_SITE_URL}logout" class="dropdown-item"><i class="feather icon-log-out"></i> Logout</a></li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
  </div>
</header>