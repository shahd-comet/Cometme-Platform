<!-- user_task.blade.php -->

<div class="user-tasks">
    <div class="d-flex flex-wrap mb-4">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($user->user_type_id == $userTypeId): ?>
                <?php if($flag == 1): ?> 
                <div>
                    <div class="avatar avatar-xs me-2">
                        <?php if($user->image == ""): ?>
                            <?php if($user->gender == "male"): ?>
                                <img src="<?php echo e(url('users/profile/male.png')); ?>" class="rounded-circle">
                            <?php else: ?>
                                <img src="<?php echo e(url('users/profile/female.png')); ?>" class="rounded-circle">
                            <?php endif; ?>
                        <?php else: ?>
                            <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" class="rounded-circle" />
                        <?php endif; ?>
                    </div>
                </div>
                <a data-toggle="collapse" class="text-dark" 
                    href="#<?php echo e($collapseId); ?>" 
                    aria-expanded="false" 
                    aria-controls="<?php echo e($collapseId); ?>">
                    Assigned this task to <strong><?php echo e($user->name); ?></strong>
                </a>
                <?php endif; ?>
            <?php endif; ?> 
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php if($flag == 1): ?>
    <div id="<?php echo e($collapseId); ?>" data-aos="fade-right"
        class="collapse multi-collapse timeline-event p-0 mb-4">
        <div class="row overflow-hidden container mb-4">
            <div class="col-12">
                <ul class="timeline timeline-center mt-5">
                    <?php echo $__env->make($includeView, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </ul>
            </div>
        </div>
    </div>
    <?php else: ?>
    
        <div mb-4>
            <h5>
                Platform Tasks
            </h5>
        </div>
        <div class="timeline-event p-0 mb-4">
            <div class="row overflow-hidden container mb-4">
                <div class="col-12">
                    <ul class="timeline timeline-center mt-5">
                        <?php echo $__env->make($includeView, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/user_tasks.blade.php ENDPATH**/ ?>