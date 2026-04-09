<div id="viewMissingNewMale" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                Missing # of Male For New Households
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($missingMaleNewHouseholds) > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Community</th>
                                </tr>
                            </thead> 
                            <tbody>
                            <?php $__currentLoopData = $missingMaleNewHouseholds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                    <?php echo e($missing->household_name); ?>
                                    </td>
                                    <td class="text-center">
                                    <?php echo e($missing->community); ?>
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/internal/household/new/male.blade.php ENDPATH**/ ?>