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
</script>
<div class="modal fade" id="myViewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header header-color-modal bg-color-1">
                <h4 class="modal-title">Modal Header</h4>
                <div class="modal-close-area modal-close-df">
                    <a class="close" data-dismiss="modal" href="javascript:void(0);"><i class="fa fa-close"></i></a>
                </div>
            </div>
            <div class="modal-body">
                <p>Loading...</p>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" href="javascript:void(0);">Cancel</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myViewSubDetailsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header header-color-modal bg-color-1">
                <h4 class="modal-title">Modal Header</h4>
                <div class="modal-close-area modal-close-df">
                    <a class="close" data-dismiss="modal" href="javascript:void(0);"><i class="fa fa-close"></i></a>
                </div>
            </div>
            <div class="modal-body">
                <p>Loading...</p>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" href="javascript:void(0);">Cancel</a>
            </div>
        </div>
    </div>
</div>
<!-- <script type="text/javascript">
    $(document).ready(function(){
        $('table').addClass('table-responsive');
    });
</script> -->