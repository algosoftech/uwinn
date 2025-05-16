   <!--Script Start Here-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="<?=base_url('assets/frontend/js/jquery-1.11.0.min.js');?>"></script>
    <script src="<?=base_url('assets/frontend/js/bootstrap.bundle.min.js');?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="<?=base_url('assets/frontend/js/dealzscript.js');?>"></script>
    <script src="<?=base_url('assets/frontend/js/productpagescript.js');?>"></script>
    <!-- notification Js -->
    <script src="<?=base_url('assets/frontend/js/bootstrap-notify.min.js');?>"></script>

    <script type="text/javascript">
     $(function(){
        <?php if($this->session->flashdata('alert_error')): ?>
            alertMessageModelPopup('<?php echo $this->session->flashdata('alert_error'); ?>','danger');
        <?php elseif($this->session->flashdata('alert_warning')): ?>
            alertMessageModelPopup('<?php echo $this->session->flashdata('alert_warning'); ?>','warning');
        <?php elseif($this->session->flashdata('alert_success')): ?>
            alertMessageModelPopup('<?php echo $this->session->flashdata('alert_success'); ?>','success');
        <?php elseif($this->session->flashdata('alert_message')): ?>
            alertMessageModelPopup('<?php echo $this->session->flashdata('alert_message'); ?>','info');
        <?php endif; ?>
    });

    function alertMessageModelPopup(message,type){  
      $.notify({
        message: message
      },
      {
        type: type,
        allow_dismiss: false,
        label: 'Cancel',
        className: 'btn-xs btn-inverse',
        placement: {
            from: 'top',
            align: 'right'
        },
        delay: 2000,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        },
        offset: {
            x: 30,
            y: 30
        }
      });
    }


 
    var swiper = new Swiper(".winnerSwiper", {
          spaceBetween: 30,
          centeredSlides: false,
          autoplay: {
            delay: 2500,
            disableOnInteraction: false,
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          }, 
          breakpoints: {
          640: {
            slidesPerView: 1,
            spaceBetween: 20,
            centeredSlides: true,
          },
          768: {
            slidesPerView: 1,
            spaceBetween: 40,
            centeredSlides: true,
          },
          1024: {
            slidesPerView: 3,
            spaceBetween: 50,
            centeredSlides: true,
          },
          1200: {
            slidesPerView: 3,
            spaceBetween: 50,
            centeredSlides: true,
          },
         }
    });
</script>