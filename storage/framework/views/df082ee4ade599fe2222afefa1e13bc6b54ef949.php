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
        <div class="d-none d-lg-flex col-lg-7 col-xl-6 align-items-center">
            <div class="flex-row text-center mx-auto">
                <img src="<?php echo e(asset('assets/img/pages/comet-'.$configData['style'].'.gif')); ?>" alt="Auth Cover Bg color" width="520" class="img-fluid authentication-cover-img" 
                data-app-light-img="pages/comet-light.gif" data-app-dark-img="pages/login-dark.png">
                <div class="mx-auto">
                    <h3>Discover The Power of Our System🥳</h3>
                </div>
            </div>
        </div>
        <!-- /Left Text -->
        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 col-xl-6 align-items-center authentication-bg p-sm-5 p-4">
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

                <form method="POST" action="<?php echo e(route('2fa.post')); ?>">
                    <?php echo csrf_field(); ?>
                
                    <p class="text-center">We sent code to your email :
                        <?php echo e(Auth::guard('user')->user()->email); ?>
                    </p>

                    <?php if($message = Session::get('success')): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button> 
                                    <strong><?php echo e($message); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($message = Session::get('error')): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button> 
                                    <strong><?php echo e($message); ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group row">
                        <label for="code" class="col-md-4 col-form-label text-md-right">Code</label>

                        <div class="col-md-6">
                            <input id="code" type="number" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="code" value="<?php echo e(old('code')); ?>" required autocomplete="code" autofocus>

                            <?php $__errorArgs = ['code'];
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

                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <a class="btn btn-link" href="<?php echo e(route('2fa.resend')); ?>">
                                Resend Code?
                            </a>
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
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/auth/2fa.blade.php ENDPATH**/ ?>