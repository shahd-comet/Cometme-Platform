<div id="communitiesFbsNotDCInstallations<?php echo e($holdersFbs->id); ?>" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                Not Yet Completed DC installations - Holders (FBS Communities)
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($holdersFbsNotDCInstallations)): ?>
                        <table class="table table-striped"> 
                            <thead>
                                <tr>
                                    <th class="text-center">Holder</th>
                                    <th class="text-center">Community</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $holdersFbsNotDCInstallations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holders): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($holders->id == $holdersFbs->id): ?>
                                <tr> 
                                    <td class="text-center">
                                        <?php echo e($holders->holder); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($holders->community); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/DC/fbs.blade.php ENDPATH**/ ?>