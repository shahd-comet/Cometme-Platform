<div id="communityAC" class="modal fade" tabindex="-1" aria-hidden="true" 
    aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">
                    Communities - AC Surveyed
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <?php if(count($communitiesAC)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">English Name</th>
                                    <th class="text-center"># of Households</th>
                                    <th class="text-center">Region</th>
                                    <th class="text-center">Sub Region</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $communitiesAC; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr> 
                                    <td class="text-center">
                                        <?php echo e($community->english_name); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($community->number_of_people); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($community->Region->english_name); ?> 
                                    </td>
                                    <td class="text-center">
                                        <?php echo e($community->SubRegion->english_name); ?> 
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
</div><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/employee/community/service/ac_survey.blade.php ENDPATH**/ ?>