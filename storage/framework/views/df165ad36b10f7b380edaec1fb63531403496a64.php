<div id="waterInProgressMaintenances" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                In Progress Refrigerator Maintenance
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($waterInProgressMaintenances)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Holder</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Call Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $waterInProgressMaintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                        <?php echo e($holder->holder); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($holder->community); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($holder->date_of_call); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($holder->maintenance_actions); ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/maintenance/water/in_progress.blade.php ENDPATH**/ ?>