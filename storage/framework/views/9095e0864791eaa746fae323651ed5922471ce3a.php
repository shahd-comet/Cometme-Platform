<div id="<?php echo e($modalNetworkIncidentDetailsId); ?>" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                <?php if($incidentStatus == 1): ?>
                Not Retrieved
                <?php else: ?> <?php if($incidentStatus == 6): ?>
                In Progress
                <?php endif; ?>
                <?php endif; ?>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            
            <?php

                $filteredNetworkIncidentDetails = $networkIncidentDetails->filter(function ($item) use ($incidentStatus) {
                    return $item->internet_incident_status_id == $incidentStatus;
                });
            ?>

            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($filteredNetworkIncidentDetails) > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Incident</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $filteredNetworkIncidentDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                    <?php echo e($missing->community_name); ?>
                                    </td>
                                    <td class="text-center">
                                    <?php echo e($missing->incident); ?>
                                    </td>
                                    <td class="text-center">
                                    <?php echo e($missing->date); ?>
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/internal/incident/network_details.blade.php ENDPATH**/ ?>