<!-- laravel style -->

<script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>

<!-- beautify ignore:start -->

<?php if($configData['hasCustomizer']): ?>

  <!-- Template customizer -->
  <script src="<?php echo e(asset('assets/vendor/js/template-customizer.js')); ?>"></script>

<?php endif; ?>

<!-- Config -->
<script src="<?php echo e(asset('assets/js/config.js')); ?>"></script>

<?php if($configData['hasCustomizer']): ?>

  <script>
    window.templateCustomizer = new TemplateCustomizer({
      cssPath: '',
      themesPath: '',
      defaultShowDropdownOnHover: <?php echo e($configData['showDropdownOnHover']); ?>,
      displayCustomizer: <?php echo e($configData['displayCustomizer']); ?>,
      lang: '<?php echo e(app()->getLocale()); ?>',

      pathResolver: function(path) {

        var resolvedPaths = {

          // Core stylesheets
          <?php $__currentLoopData = ['core']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            '<?php echo e($name); ?>.css': '<?php echo e(asset("assets/vendor/css{$configData['rtlSupport']}/{$name}.css")); ?>',
            '<?php echo e($name); ?>-dark.css': '<?php echo e(asset("assets/vendor/css{$configData['rtlSupport']}/{$name}-dark.css")); ?>',
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          // Themes
          <?php $__currentLoopData = ['default', 'bordered', 'semi-dark']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            'theme-<?php echo e($name); ?>.css': '<?php echo e(asset("assets/vendor/css{$configData['rtlSupport']}/theme-{$name}.css")); ?>',
            'theme-<?php echo e($name); ?>-dark.css': '<?php echo e(asset("assets/vendor/css{$configData['rtlSupport']}/theme-{$name}-dark.css")); ?>',
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        };

        return resolvedPaths[path] || path;
      },

      'controls': <?php echo json_encode($configData['customizerControls']); ?>,
    });
  </script>

<?php endif; ?>

<!-- beautify ignore:end -->

<!-- Google Analytics -->
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }

  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/layouts/sections/scriptsIncludes.blade.php ENDPATH**/ ?>