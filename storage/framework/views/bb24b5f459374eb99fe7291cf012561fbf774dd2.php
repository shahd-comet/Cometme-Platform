

<?php $__env->startSection('title', 'edit internet system'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>

<div class="container">
    <h2>Create A new Internet System Return</h2>
    <form id="returnForm" action="<?php echo e(route('internet.returns.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <!-- validation errors -->
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
                <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Return Details</h5>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="internet_system_community_id">Community</label>
                        <select name="internet_system_community_id" id="internet_system_community_id" class="form-control selectpicker" data-live-search="true">
                            <option value="">-- Select community --</option>
                            <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>"><?php echo e($c->english_name ?? ($c->arabic_name ?? $c->id)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="internet_system_id">Internet System</label>
                        <select name="internet_system_id" id="internet_system_id" class="form-control selectpicker" data-live-search="true" disabled>
                            <option value="">-- Select system --</option>
                            <?php $__currentLoopData = $systems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s->id); ?>"><?php echo e($s->system_name ?? $s->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="returned_by">Returned By (user)</label>
                        <select name="returned_by" id="returned_by" class="form-control selectpicker" data-live-search="true">
                            <option value="">-- Select user --</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4 form-group">
                        <label for="return_date">Return Date</label>
                        <input type="datetime-local" name="return_date" id="return_date" class="form-control" />
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="0">Pending</option>
                            <option value="1">Received</option>
                            <option value="2">Inspected</option>
                            <option value="3">Approved</option>
                            <option value="4">Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="reason_id">Reason</label>
                        <select name="reason_id" id="reason_id" class="form-control selectpicker">
                            <option value="">-- Reason (optional) --</option>
                            <?php $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                </div>

            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<!-- components Section -->


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
    // important Functions
    // Fetch systems for selected community

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/system/internet/returns/create.blade.php ENDPATH**/ ?>