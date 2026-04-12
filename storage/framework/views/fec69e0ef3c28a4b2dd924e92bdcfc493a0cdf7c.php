<?php
$containerNav = $containerNav ?? 'container-fluid';
?>

<!-- Navbar -->
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="<?php echo e($containerNav); ?>">

    <!--  Brand demo (display only for navbar-full and hide on below xl) -->
    <?php if(isset($navbarFull)): ?>
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
          <?php echo $__env->make('_partials.macros', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </span>
        <span class="app-brand-text demo menu-text fw-bold"><?php echo e(config('variables.templateName')); ?></span>
      </a>

      <?php if(isset($menuHorizontal)): ?>
      <!-- Display menu close icon only for horizontal-menu with navbar-full -->
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="bx bx-x bx-sm align-middle"></i>
      </a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ! Not required for layout-without-menu -->
    <?php if(!isset($navbarHideToggle)): ?>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0<?php echo e(isset($menuHorizontal) ? ' d-xl-none ' : ''); ?> <?php echo e(isset($contentNavbar) ?' d-xl-none ' : ''); ?>">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
      </a>
    </div>
    <?php endif; ?>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

      <?php if(!isset($menuHorizontal)): ?>
      <!-- Search -->
      <!-- <div class="navbar-nav align-items-center">
        <div class="nav-item navbar-search-wrapper mb-0">
          <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
            <i class="bx bx-search-alt bx-sm"></i>
            <span class="d-none d-md-inline-block text-muted">Search </span>
          </a>
        </div>
      </div> -->
      <!-- /Search -->
      <?php endif; ?>

      <ul class="navbar-nav flex-row align-items-center ms-auto">

        <!-- Download PDF -->
        <li class="nav-item me-2 me-xl-0">
          <a class="nav-link hide-arrow" title="Click here to download the manual!" 
            href="<?php echo e(url('downloadPdf')); ?>">
            <i class='bx bx-sm bx-down-arrow-alt'></i>
          </a>
        </li>
        <!--/ Download PDF -->

        <!-- Style Switcher -->
        <li class="nav-item me-2 me-xl-0">
          <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">
            <i class='bx bx-sm'></i>
          </a>
        </li>
        <!--/ Style Switcher -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <div class="avatar avatar-online">
            <a href="<?php echo e(url('profile-user', Auth::guard('user')->user()->id)); ?>">
              <?php if(Auth::guard()): ?> 
                <?php if(Auth::guard('user')->user()->image == ""): ?>
                  <?php if(Auth::guard('user')->user()->gender == "male"): ?>
                    <img  src="/assets/images/male.png" alt class="rounded-circle">
                  <?php else: ?>
                    <img src="/assets/images/female.png" alt class="rounded-circle">
                  <?php endif; ?>
                <?php else: ?>
                <img alt class="rounded-circle" src="<?php echo e(url('users/profile/'.@Auth::guard('user')->user()->image)); ?>">
                <?php endif; ?>
              <?php endif; ?>
            </a>
          </div>
        </li>
        <!--/ User -->
      </ul>
    </div>
  </div>
</nav>
<!-- / Navbar --><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/layouts/sections/navbar/navbar.blade.php ENDPATH**/ ?>