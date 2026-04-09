<div id="missingEnergyPublicDonors" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                Missing Donors For Energy Public
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($missingEnergyPublicDonors) > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">User</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Energy System</th>
                                    <th class="text-center">Energy System Type</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $missingEnergyPublicDonors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                    <?php echo e($missing->public); ?>
                                    </td>
                                    <td class="text-center">
                                    <?php echo e($missing->english_name); ?>
                                    </td>
                                    <td class="text-center">
                                    <?php echo e($missing->energy_name); ?>
                                    </td>
                                    <td class="text-center">
                                    <?php echo e($missing->type); ?>
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/donors/energy_public.blade.php ENDPATH**/ ?>