<!-- BEGIN: Vendor JS-->
<script src="<?php echo e(asset('assets/vendor/libs/jquery/jquery.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/popper/popper.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/js/bootstrap.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/hammer/hammer.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/i18n/i18n.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/typeahead-js/typeahead.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/js/menu.js')); ?>"></script>

<?php echo $__env->yieldContent('vendor-script'); ?>
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>
<!-- END: Theme JS-->

<!-- Pricing Modal JS-->
<?php echo $__env->yieldPushContent('pricing-script'); ?>
<!-- END: Pricing Modal JS-->

<!-- BEGIN: Page JS-->
<?php echo $__env->yieldContent('page-script'); ?>
<!-- END: Page JS-->
<?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/layouts/sections/scripts.blade.php ENDPATH**/ ?>