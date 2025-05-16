<script src="<?=base_url('assets/frontend_mobile/js/bootstrap.bundle.min.js');?>"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="<?=base_url('assets/frontend_mobile/js/mobile.js');?>"></script>

<script src="<?=base_url('assets/frontend/js/bootstrap-notify.min.js');?>"></script>
<script src="<?=base_url('assets/frontend_mobile/js/piechart.js');?>"></script>

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
            from: 'bottom',
            align: 'center'
        },
        delay: 2000,
        animate: {
            enter: 'animated fadeInRight',
            exit: 'animated fadeOutRight'
        },
        offset: {
            x: 0,
            y: 80
        }
      });
    }
</script>