

<?php $__env->startSection('title', 'Account settings - Account'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">Account Settings /</span> <?php echo e($user->name); ?> Account
</h4>

<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-md-row mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0);">
                    <i class="bx bx-user me-1"></i> Account
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="<?php echo e(route('logout')); ?>"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" 
                        style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                    Logout
                </a>
            </li>
        </ul>

        <?php if(session()->has('message')): ?>
            <div class="row">
                <div class="alert alert-success">
                    <?php echo e(session()->get('message')); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <h5 class="card-header">Profile Details</h5>
            <!-- Account -->
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('user.update', $user->id)); ?>"
                    enctype="multipart/form-data" >
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <?php if($user->image == ""): ?>
                        
                        <?php if($user->gender == "male"): ?>
                            <img src='/users/profile/male.jpg' alt="user-avatar" 
                                class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                        <?php else: ?>
                            <img src='/assets/images/female.png' alt="user-avatar" 
                                class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                        <?php endif; ?>
                        <?php else: ?>
                            <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="user-avatar" 
                                class="d-block rounded" height="100" width="100" id="uploadedAvatar"/>
                        <?php endif; ?>
                
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" name="image" class="account-file-input" 
                                    hidden accept="image/png, image/jpeg" />
                            </label>
                            <p class="mb-0">Allowed JPG, JPEG, GIF or PNG.</p>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input class="form-control" type="text" id="name" name="name" 
                                    value="<?php echo e($user->name); ?>" autofocus />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input class="form-control" type="text" id="email" name="email" 
                                    value="<?php echo e($user->email); ?>"/>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phone">Phone Number</label>
                                <input type="number" id="phone" name="phone" class="form-control" 
                                    value="<?php echo e($user->phone); ?>"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="password">Change password</label>
                                <input type="password" name="password" class="form-control"/>
                                <?php if($errors->has('password')): ?>
                                    <span class="error"><?php echo e($errors->first('password')); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="password">Confirm password</label>
                                <input type="password" name="confirm-password" class="form-control"/>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Save changes</button>
                            <button type="reset" class="btn btn-label-secondary">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
      
    $(document).ready(function (e) {

        $('#upload').change(function() {
                    
            let reader = new FileReader();
        
            reader.onload = (e) => { 
        
                $('#uploadedAvatar').attr('src', e.target.result); 
            }
        
            reader.readAsDataURL(this.files[0]); 
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/auth/profile.blade.php ENDPATH**/ ?>