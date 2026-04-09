<div id="notYetSafteyCheckedFbs" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                Not Yet Checked (FBS)
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($notYetSafteyCheckedFbs)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Holder</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Energy System</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $notYetSafteyCheckedFbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                        <?php echo e($holder->holder); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($holder->community); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($holder->energy_system); ?>
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/maintenance/energy/saftey/fbs_not_checked.blade.php ENDPATH**/ ?>