<?php if ($_SERVER['SERVER_NAME'] == 'localhost'): ?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<?php else: ?>
<script src="{ASSET_INCLUDE_URL}js/vendor-all.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/plugins/bootstrap.min.js"></script>
<script src="{ASSET_INCLUDE_URL}js/ripple.js"></script>
<script src="{ASSET_INCLUDE_URL}js/pcoded.min.js"></script>
<?php endif; ?>
<script src="{ASSET_INCLUDE_URL}js/jquery.validate.js"></script>
<script src="{ASSET_INCLUDE_URL}js/manoj.login.js"></script>
