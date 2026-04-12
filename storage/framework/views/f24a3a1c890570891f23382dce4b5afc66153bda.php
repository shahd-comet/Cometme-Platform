<!-- BEGIN: Theme CSS-->

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/boxicons.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/fontawesome.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/flag-icons.css')); ?>" />

<!-- Core CSS -->
<link rel="stylesheet"
      href="<?php echo e(asset('assets/vendor/css' . $configData['rtlSupport'] . '/core' . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.css')); ?>"
      class="<?php echo e($configData['hasCustomizer'] ? 'template-customizer-core-css' : ''); ?>" />

<link rel="stylesheet"
      href="<?php echo e(asset('assets/vendor/css' . $configData['rtlSupport'] . '/' . $configData['theme'] . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.css')); ?>"
      class="<?php echo e($configData['hasCustomizer'] ? 'template-customizer-theme-css' : ''); ?>" />

<link rel="stylesheet" href="<?php echo e(asset('assets/css/demo.css')); ?>" />

<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/typeahead-js/typeahead.css')); ?>" />

<!-- Vendor Styles -->
<?php echo $__env->yieldContent('vendor-style'); ?>

<!-- Page Styles -->
<?php echo $__env->yieldContent('page-style'); ?>
<?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/layouts/sections/styles.blade.php ENDPATH**/ ?>