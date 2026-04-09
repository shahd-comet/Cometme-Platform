<div id="missingSchoolDetails" class="modal fade" aria-hidden="true" 
    aria-labelledby="">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5">
                Need to Update Water Service to "Yes"
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($missingSchoolDetails)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Public "School"</th>
                                    <th class="text-center">Community</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $missingSchoolDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missingSchoolDetails): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                        <?php echo e($missingSchoolDetails->english_name); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($missingSchoolDetails->community); ?>
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/public/school.blade.php ENDPATH**/ ?>