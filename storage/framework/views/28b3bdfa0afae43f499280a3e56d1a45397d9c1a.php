<!-- Action Items for AC-->
<li class="timeline-item mb-md-4 mb-5 timeline-item-left">
    <span class="timeline-indicator timeline-indicator-warning" data-aos="zoom-in" data-aos-delay="200">
        <i class="bx bx-bulb"></i>
    </span>
    <div class="timeline-event card p-0" data-aos="fade-right">
        <div class="card-header border-0 d-flex justify-content-between">
            <h5 class="card-title mb-0">New Community/Compound</h5>
        </div>
        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-warning" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-alarm-exclamation"></i>
                        <a data-toggle="collapse" class="text-warning" 
                            href="#notYetStartedACSurveyTab" 
                            aria-expanded="false" 
                            aria-controls="notYetStartedACSurveyTab">
                            AC Survey Not Yet Started 
                        </a>  
                    </div>
                </li>
            </ul>
            <div class="collapse multi-collapse container mb-4" 
                id="notYetStartedACSurveyTab">
                <?php if(count($notStartedACSurveyCommunities) > 0): ?>
                <p>You've got <?php echo e($notStartedACSurveyCommunities->count()); ?> initial communities 
                    that need a visit to kickstart the survey process.
                    <br>
                    <span class="text-warning">
                    If you've already visited the community, please enter the survey details into the platform
                    </span>
                </p> 
                <?php $__currentLoopData = $notStartedACSurveyCommunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notStartedACSurveyCommunity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <span> <?php echo e($notStartedACSurveyCommunity->english_name); ?>  / 
                                <?php echo e($notStartedACSurveyCommunity->number_of_household); ?>
                            </span>   
                        </li>
                    </ul>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                <?php if(count($queryCompounds) > 0): ?>
                <p>You've got <?php echo e($queryCompounds->count()); ?> initial compounds 
                    that need a visit to kickstart the survey process.
                </p> 
                <?php $__currentLoopData = $queryCompounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queryCompound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <span> <?php echo e($queryCompound->english_name); ?>  / 
                            </span>   
                        </li>
                    </ul>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Not Yet Completed AC Installation -->
        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-danger" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-grid"></i>
                        <a data-toggle="collapse" class="text-danger" 
                            href="#notYetCompletedACInstallationTab" 
                            aria-expanded="false" 
                            aria-controls="notYetCompletedACInstallationTab">
                            AC Installation Not Yet Completed
                        </a>
                    </div>
                </li>
            </ul>
            <div class="collapse multi-collapse container mb-4" 
                id="notYetCompletedACInstallationTab">
                <?php if($totalNotStartedAC > 0): ?>
                    <p>You've got <?php echo e($totalNotStartedAC); ?> communities 
                        / Compounds
                        that need to complete the AC installation process.
                    </p>  
                    <?php if(count($notStartedACInstallationCommunities) > 0): ?>
                    <?php $__currentLoopData = $notStartedACInstallationCommunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notStartedACInstallationCommunity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <a  type="button" data-bs-toggle="modal"
                                data-bs-target="#NotCompletedHouseholdsCommunity<?php echo e($notStartedACInstallationCommunity->id); ?>">
                                <span> <?php echo e($notStartedACInstallationCommunity->english_name); ?>  -
                                    <?php echo e($notStartedACInstallationCommunity->number); ?> 
                                    <span class="text-info">/ 
                                    <?php echo e($notStartedACInstallationCommunity->number_of_households); ?>
                                    </span>
                                </span> 
                            </a>  
                        </li>
                    </ul>
                    <?php echo $__env->make('actions.admin.AC.not_completed_community_household', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php if(count($notStartedACInstallationCompounds) > 0): ?>
                    <?php $__currentLoopData = $notStartedACInstallationCompounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $queryCompound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <a  type="button" data-bs-toggle="modal"
                                data-bs-target="#NotCompletedHouseholdsCompound<?php echo e($queryCompound->id); ?>">
                                <span> <?php echo e($queryCompound->english_name); ?>  -
                                    <?php echo e($queryCompound->number); ?> 
                                    <span class="text-info">/ 
                                    <?php echo e($queryCompound->number_of_households); ?>
                                    </span>
                                </span> 
                            </a>  
                        </li>
                    </ul>
                    <?php echo $__env->make('actions.admin.AC.not_completed_compound_household', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Not Yet Completed Electricity Room-->
        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-primary" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-pulse"></i>
                        <a data-toggle="collapse" class="text-primary" 
                            href="#notYetCompletedElectricityRoomTab" 
                            aria-expanded="false" 
                            aria-controls="notYetCompletedElectricityRoomTab">
                            Electricity Room Not Yet Completed 
                        </a>
                    </div>
                </li>
            </ul>
            <div class="collapse multi-collapse container mb-4" 
                id="notYetCompletedElectricityRoomTab">
                <?php if(count($communitiesElecticityRoomMissing) == 0 && count($compoundsElecticityRoomMissing) == 0): ?>
                    <p>You've got no communities/compounds need to complete the electricity room
                    </p> 
                <?php else: ?> <?php if(count($communitiesElecticityRoomMissing) > 0 ||
                    count($compoundsElecticityRoomMissing) > 0): ?>
                    <p>You've got <?php echo e($communitiesElecticityRoomMissing->count()
                            + $compoundsElecticityRoomMissing->count()); ?> SMG/MG 
                        communities or compounds that need 
                        to complete the electricity room.
                    </p> 
                    <?php if(count($compoundsElecticityRoomMissing) > 0): ?>
                        <?php $__currentLoopData = $compoundsElecticityRoomMissing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compoundsElecticityRoom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <ul class="list-group">
                            <li class="d-flex list-group-item" style="margin-top:9px">
                                <a type="button" data-bs-toggle="modal" 
                                    data-bs-target="#updateElectricityGridCompound<?php echo e($compoundsElecticityRoom->id); ?>">
                                    <span><?php echo e($compoundsElecticityRoom->compound); ?></span>   
                                </a>  
                            </li>
                        </ul>
                        <?php echo $__env->make('actions.admin.AC.room_compound', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php if(count($communitiesElecticityRoomMissing) > 0): ?>
                        <?php $__currentLoopData = $communitiesElecticityRoomMissing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $communitiesElecticityRoom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <ul class="list-group">
                            <li class="d-flex list-group-item" style="margin-top:9px">
                                <a type="button" data-bs-toggle="modal" 
                                    data-bs-target="#updateElectricityGrid<?php echo e($communitiesElecticityRoom->id); ?>">
                                    <span><?php echo e($communitiesElecticityRoom->community); ?></span>   
                                </a> 
                            </li>
                        </ul>
                        <?php echo $__env->make('actions.admin.AC.room_community', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Not Yet Completed Grid-->
        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-info" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-grid-alt"></i>
                        <a data-toggle="collapse" class="text-info" 
                            href="#notYetCompletedGridTab" 
                            aria-expanded="false" 
                            aria-controls="notYetCompletedGridTab">
                            Grid Not Yet Completed
                        </a>
                    </div>
                </li>
            </ul>
            <div class="collapse multi-collapse container mb-4" 
                id="notYetCompletedGridTab">
                <?php if(count($communitiesGridMissing) == 0 && count($compoundsGridMissing) == 0): ?>
                    <p>You've got no communities/compounds need to complete the grid
                    </p> 
                <?php else: ?> <?php if(count($communitiesGridMissing) > 0 ||
                    count($compoundsGridMissing) > 0): ?>
                <p>You've got <?php echo e($communitiesGridMissing->count()
                        + $compoundsGridMissing->count()); ?> SMG/MG 
                    communities or compounds that need 
                    to complete the grid.
                </p> 
                    <?php if(count($compoundsGridMissing) > 0): ?>
                        <?php $__currentLoopData = $compoundsGridMissing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compoundsGrid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <ul class="list-group">
                            <li class="d-flex list-group-item" style="margin-top:9px">
                                <a type="button" data-bs-toggle="modal" 
                                    data-bs-target="#updateGridCompound<?php echo e($compoundsGrid->id); ?>">
                                    <span><?php echo e($compoundsGrid->compound); ?></span>   
                                </a> 
                            </li>
                        </ul>
                        <?php echo $__env->make('actions.admin.AC.grid_compound', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                    <?php if(count($communitiesGridMissing) > 0): ?>
                        <?php $__currentLoopData = $communitiesGridMissing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $communitiesGrid): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <ul class="list-group">
                            <li class="d-flex list-group-item" style="margin-top:9px">
                                <a type="button" data-bs-toggle="modal" 
                                    data-bs-target="#updateGridCommunity<?php echo e($communitiesGrid->id); ?>">
                                    <span><?php echo e($communitiesGrid->community); ?></span>   
                                </a> 
                            </li>
                        </ul>
                        <?php echo $__env->make('actions.admin.AC.grid_community', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Not Yet Completed DC Installations -->
        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-dark" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-barcode"></i>
                        <a data-toggle="collapse" class="text-dark" 
                            href="#notYetCompletedDCInstallationTab" 
                            aria-expanded="false" 
                            aria-controls="notYetCompletedDCInstallationTab">
                            DC installations Not Yet Completed 
                        </a>
                    </div>
                </li>
            </ul>

            <div class="collapse multi-collapse container mb-4" 
                id="notYetCompletedDCInstallationTab">
                <!-- <?php if(count($communitiesFbsNotDCInstallations) > 0): ?>
                    You've got 
                    <span class="text-danger">
                        <?php echo e($communitiesFbsNotDCInstallations->count()); ?> FBS
                    </span>   
                    communities that completed AC installations but didn't 
                    complete the DC installation process.
                <?php $__currentLoopData = $communitiesFbsNotDCInstallations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $communitiesFbs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <span> <?php echo e($communitiesFbs->community); ?>  - 
                                <?php echo e($communitiesFbs->number_of_holders); ?>
                            </span>   
                        </li>
                    </ul>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?> -->

                <?php if(count($communitiesFbsNotDCInstallations) > 0): ?>
                    You've got 
                    <span class="text-danger">
                        <?php echo e($holdersFbsNotDCInstallations->count()); ?> 
                        holders 
                    </span>  in
                    <span class="text-danger"> 
                        <?php echo e($communitiesFbsNotDCInstallations->count()); ?>
                        FBS communities 
                        </span> 
                        that need to complete the DC installation process.
                <?php $__currentLoopData = $communitiesFbsNotDCInstallations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holdersFbs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <a type="button" data-bs-toggle="modal" 
                                data-bs-target="#communitiesFbsNotDCInstallations<?php echo e($holdersFbs->id); ?>">
                                <span> <?php echo e($holdersFbs->community); ?>  - 
                                    <?php echo e($holdersFbs->number_of_holders); ?>
                                </span>   
                            </a>
                        </li>
                    </ul>
                    <?php echo $__env->make('actions.admin.DC.fbs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                <br>

                <?php if(count($communitiesMgSmgNotDCInstallations) > 0): ?>
                    You've got 
                    <span class="text-danger">
                        <?php echo e($holdersMgSmgNotDCInstallations->count()); ?> 
                        holders 
                    </span> in
                    <span class="text-danger"> 
                        <?php echo e($communitiesMgSmgNotDCInstallations->count()); ?>
                        MG/SMG communities 
                    </span> 
                    that need to complete the DC installation process.
                <?php $__currentLoopData = $communitiesMgSmgNotDCInstallations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holdersMgSmg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <ul class="list-group">
                        <li class="d-flex list-group-item" style="margin-top:9px">
                            <a type="button" data-bs-toggle="modal" 
                                data-bs-target="#communitiesMgSmgNotDCInstallations<?php echo e($holdersMgSmg->id); ?>">
                                <span> <?php echo e($holdersMgSmg->community); ?>  - 
                                    <?php echo e($holdersMgSmg->number_of_holders); ?>
                                </span>   
                            </a>
                        </li>
                    </ul>
                    <?php echo $__env->make('actions.admin.DC.grid', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <!-- <?php if(count($communitiesMgSmgNotDCInstallations) > 0): ?>
                <a type="button" data-bs-toggle="modal" 
                    data-bs-target="#communitiesMgSmgNotDCInstallations">
                    You've got 
                    <span class="text-danger">
                        <?php echo e($communitiesMgSmgNotDCInstallations->count()); ?> MG/SMG
                    </span>   
                    communities that completed AC installations but didn't 
                    complete the DC installation process.
                </a>
                <?php endif; ?> -->
            </div>
            <!-- 
            <p>
            You've got XX communities or compounds where you haven't 
            completed activating the meters.
            </p> -->
        </div>

        <!-- MISC FBS -->
        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-primary" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-data"></i>
                        <a data-toggle="collapse" class="text-primary" 
                            href="#miscFbsTab" 
                            aria-expanded="false" 
                            aria-controls="miscFbsTab">
                            MISC FBS -- "Requested Systems"
                        </a>
                    </div>
                </li>
            </ul>
            <div class="collapse multi-collapse container mb-4" 
                id="miscFbsTab">
                <?php if(count($miscRequested) > 0): ?>
                <p>You've got <?php echo e($miscRequested->count()); ?> MISC systems 
                    that need to begin the installation process.
                </p> 
                <?php endif; ?> 
            </div>
        </div>

        <!-- <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-info" style="font-weight:bold; font-size:16px">
                        <i class="bx bx-group"></i>
                        <span >AC Completed </span>
                    </div>
                </li>
            </ul>
            <?php if(count($acHouseholds) > 0): ?>
            <p>You've <?php echo e($acHouseholds->count()); ?>
                <a type="button" title="Export AC Households"
                    href="action-item/ac-household/export">
                    AC households 
                </a>
                which are not related to AC Survey "AC Community"
                ,Need to be checked
            </p> 
            <?php endif; ?>
        </div> -->

        <div class="card-body pb-0">
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                    <div class="text-light" style="font-weight:bold; font-size:16px">
                        <form method="POST" enctype='multipart/form-data' 
                            action="<?php echo e(route('energy-request.export')); ?>">
                            <?php echo csrf_field(); ?>
                            <button class="" type="submit">
                                <i class='fa-solid fa-file-excel'></i>
                                Export Energy Installation Progress Report 
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <div class="timeline-event-time">AC/DC Process</div>
    </div>
</li><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/installation/ac_dc_process.blade.php ENDPATH**/ ?>