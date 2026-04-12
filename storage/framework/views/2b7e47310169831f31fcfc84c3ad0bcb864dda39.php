<?php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
?>



<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('vendor-style'); ?>
<!-- Vendor -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-style'); ?>
<!-- Page -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script src="<?php echo e(asset('assets/js/pages-auth.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="authentication-wrapper authentication-cover">
  <div class="authentication-inner row m-0">
    <!-- /Left Text -->
    <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center">
      <div class="flex-row text-center mx-auto">
        <img src="<?php echo e(asset('assets/img/pages/comet-'.$configData['style'].'.gif')); ?>" alt="Auth Cover Bg color" width="520" class="img-fluid authentication-cover-img" 
        data-app-light-img="pages/comet-light.gif" data-app-dark-img="pages/login-dark.png">
        <div class="mx-auto">
          <h3>Discover The Power of Our System🥳</h3>
          <p>
           
          </p>
        </div>
      </div>
    </div>
    <!-- /Left Text -->
    <!-- Login -->
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <!-- Logo -->
        <div class="app-brand mb-4">
          <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2 mb-2">
          <img width=50 type="image/x-icon" src="<?php echo e(('/logo.jpg')); ?>">
            <span class="app-brand-text demo h3 mb-0 fw-bold"><?php echo e(config('variables.templateName')); ?></span>
          </a>
        </div>
        <!-- /Logo -->
        <h4 class="mb-2">Welcome to <?php echo e(config('variables.templateName')); ?>! 👋</h4>
        <p class="mb-4">Please sign-in to your account and start the adventure</p>

        <?php if(session()->has('message')): ?>
          <div class="row">
            <div class="alert alert-danger">
              <?php echo e(session()->get('message')); ?>
            </div>
          </div>
        <?php endif; ?>

        <form id="formAuthentication" class="mb-3" method="POST" 
            action="<?php echo e(route('login')); ?>">
        <?php echo csrf_field(); ?>
          <div class="form-group">
            <label class="label">Email</label>
            <div class="input-group">
              <input id="email" type="email"
                class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email"
                value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus>
              
            </div>
          </div>
          <div class="form-group">
            <label class="label">Password</label>
            <div class="input-group">
              <input id="password" type="password"
                  class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="password"
                  required autocomplete="current-password">
             
              <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <span class="invalid-feedback" role="alert">
                  <strong><?php echo e($message); ?></strong>
                </span>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary submit-btn btn-block">
              <?php echo e(__('Login')); ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/auth/login.blade.php ENDPATH**/ ?>