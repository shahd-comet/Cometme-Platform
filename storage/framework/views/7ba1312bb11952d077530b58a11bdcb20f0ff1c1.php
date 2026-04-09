<?php
$configData = Helper::appClasses();
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <?php if(!isset($navbarFull)): ?> 
  <div class="app-brand demo">
    <a href="<?php echo e(url('/')); ?>" class="app-brand-link">
      <img width=50 type="image/x-icon" src="<?php echo e(('/logo.jpg')); ?>">
      <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size:18px">
        <?php echo e(config('variables.templateName')); ?>
      </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
      <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
    </a>
  </div>
  <?php endif; ?>

  <!-- ! Hide menu divider if navbar-full -->
  <?php if(!isset($navbarFull)): ?>
    <div class="menu-divider mt-0 ">
    </div>
  <?php endif; ?>
 
  <div class="menu-inner-shadow"></div>

  <?php echo $__env->make('layouts.sections.menu.vertical', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</aside><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/layouts/sections/menu/verticalMenu.blade.php ENDPATH**/ ?>