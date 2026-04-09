

<?php $__env->startSection('title', 'Action Items'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 breadcrumb-wrapper mb-2">
    <a class="text-primary">
        <span class="text-muted fw-light">Teams /</span> Action Items
    </a>
</h4>

<div class="container mb-4">
    <?php if(count($groupedActionItems) > 0): ?>
        <?php $__currentLoopData = $groupedActionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userId => $userActionItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="user-tasks">
                <div class="d-flex flex-wrap mb-4">
                    <div>
                        <div class="avatar avatar-xs me-2">
                            <?php if($userActionItems->first()->User->image == ""): ?>
                                <?php if($userActionItems->first()->User->gender == "male"): ?>
                                    <img src="<?php echo e(url('users/profile/male.png')); ?>" class="rounded-circle">
                                <?php else: ?>
                                    <img src="<?php echo e(url('users/profile/female.png')); ?>" class="rounded-circle">
                                <?php endif; ?>
                            <?php else: ?>
                                <img src="<?php echo e(url('users/profile/'.$userActionItems->first()->User->image)); ?>" alt="Avatar" class="rounded-circle" />
                            <?php endif; ?>
                        </div>
                    </div> 
                    <a data-toggle="collapse" class="text-dark" 
                        href="#userCollapse<?php echo e($userId); ?>" 
                        aria-expanded="false" 
                        aria-controls="userCollapse<?php echo e($userId); ?>">
                        Action Items for <strong><?php echo e($userActionItems->first()->User->name); ?></strong>
                    </a>
                </div>

                <div id="userCollapse<?php echo e($userId); ?>" class="collapse multi-collapse timeline-event p-0 mb-4" 
                    data-aos="fade-right">
                    <div class="pb-0">
                        <table class="dt-advanced-search table table-bordered">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Timeline</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $userActionItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actionItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($actionItem->task); ?></td>
                                        <td contenteditable="true">
                                            <?php if($actionItem->ActionStatus->id == 1): ?>
                                            <span class='badge rounded-pill bg-label-info'>
                                                <?php echo e($actionItem->ActionStatus->status); ?>
                                            </span>
                                            <?php else: ?> <?php if($actionItem->ActionStatus->id == 2): ?>
                                            <span class='badge rounded-pill bg-label-warning'>
                                                <?php echo e($actionItem->ActionStatus->status); ?>
                                            </span>
                                            <?php else: ?> <?php if($actionItem->ActionStatus->id == 3): ?>
                                            <span class='badge rounded-pill bg-label-danger'>
                                                <?php echo e($actionItem->ActionStatus->status); ?>
                                            </span>
                                            <?php else: ?> <?php if($actionItem->ActionStatus->id == 4): ?>
                                            <span class='badge rounded-pill bg-label-success'>
                                                <?php echo e($actionItem->ActionStatus->status); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($actionItem->ActionPriority->id == 1): ?>
                                            <span class='badge bg-primary'>
                                                <?php echo e($actionItem->ActionPriority->name); ?>
                                            </span>
                                            <?php else: ?> <?php if($actionItem->ActionPriority->id == 2): ?>
                                            <span class='badge bg-warning text-dark'>
                                                <?php echo e($actionItem->ActionPriority->name); ?>
                                            </span>
                                            <?php else: ?> <?php if($actionItem->ActionPriority->id == 3): ?>
                                            <span class='badge bg-danger'>
                                                <?php echo e($actionItem->ActionPriority->name); ?>
                                            </span>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo e($actionItem->date); ?> 
                                            <strong>to </strong>
                                            <?php echo e($actionItem->due_date); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>

<?php if(Auth::guard('user')->user()->user_type_id == 1 ||
    Auth::guard('user')->user()->user_type_id == 2): ?>
<hr>
<h4 class="py-3 breadcrumb-wrapper mb-2">
    <a data-toggle="collapse" class="text-primary" 
        href="#projectCostsTab" 
        aria-expanded="false" 
        aria-controls="projectCostsTab">
        <span class="text-muted fw-light">Report /</span> Files
    </a>
</h4>

<div class="container" >
    <h6 class="py-3 breadcrumb-wrapper">
        <a data-toggle="collapse" class="text-warning" 
            href="#EnergyProjectFiles" 
            aria-expanded="false" 
            aria-controls="EnergyProjectFiles">
            <i class="bx bx-bulb bx-sm me-3"></i>
            Energy Project
        </a>
    </h6>
    
    <div class="row collapse multi-collapse container" id="EnergyProjectFiles">
        <div class="user-tasks">
            <div class="">
                <form method="POST" enctype='multipart/form-data' id="EnergyProjectFileForm"
                    action="<?php echo e(route('energy-request.export')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Export</label>
                                <input type="text" class="form-control" disabled
                                value="Energy Installation Progress Report"> 
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Cycle Year</label>
                                <select name="energy_cycle_id" id="cycleYearSelected"
                                    class="selectpicker form-control" data-live-search="true">
                                    <option disabled selected>Search Cycle Year</option>
                                    <?php $__currentLoopData = $energyCycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energyCycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($energyCycle->id); ?>">
                                            <?php echo e($energyCycle->name); ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select> 
                            </fieldset>
                            <div id="energy_cycle_id_error" style="color: red;"></div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4" >
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Click Here</label>
                                <button class="form-control btn-warning" type="submit">
                                    <i class='fa-solid fa-file-excel'></i>
                                    Export Energy Installation Progress Report
                                </button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
            <!-- <div class="">
                <form method="POST" enctype='multipart/form-data' 
                    action="<?php echo e(route('energy-cost.export')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Export</label>
                                <input type="text" class="form-control" disabled
                                value="Energy Installation Cost File"> 
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4" style="height: 180px;z-index: 200;">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Cycle Year</label>
                                <select name="energy_cycle_id"
                                    class="selectpicker form-control" data-live-search="true">
                                    <option disabled selected>Search Cycle Year</option>
                                    <?php $__currentLoopData = $energyCycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energyCycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($energyCycle->id); ?>">
                                            <?php echo e($energyCycle->name); ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select> 
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Click Here</label>
                                <button class="form-control btn-warning" type="submit">
                                    <i class='fa-solid fa-file-excel'></i>
                                    Export Energy Installation Cost File
                                </button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div> -->
        </div>
    </div>

    <h6 class="py-3 breadcrumb-wrapper">
        <a data-toggle="collapse" class="text-info" 
            href="#waterProjectFiles" 
            aria-expanded="false" 
            aria-controls="waterProjectFiles">
            <i class="bx bx-droplet bx-sm me-3"></i>
            Water Project
        </a>
    </h6> 

    <div class="row overflow-hidden collapse multi-collapse container" id="waterProjectFiles">
        <div class=" mb-4">
            <form method="POST" enctype='multipart/form-data' 
                action="<?php echo e(route('water-progress.export')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Export</label>
                            <input type="text" class="form-control" disabled
                            value="Water Progress Report File"> 
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Click Here</label>
                            <button class="form-control btn-info" type="submit">
                                <i class='fa-solid fa-file-excel'></i>
                                Export Water Progress Report File
                            </button>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <h6 class="py-3 breadcrumb-wrapper">
        <a data-toggle="collapse" class="text-success" 
            href="#InternetProjectFiles" 
            aria-expanded="false" 
            aria-controls="InternetProjectFiles">
            <i class="bx bx-wifi bx-sm me-3"></i>
            Internet Project
        </a>
    </h6> 

    <div class="row overflow-hidden collapse multi-collapse container" id="InternetProjectFiles">
        <div class=" mb-4">
            <form method="POST" enctype='multipart/form-data' 
                action="<?php echo e(route('internet-user.export')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Export</label>
                            <input type="text" class="form-control" disabled
                            value="Internet Metrics Report File"> 
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Click Here</label>
                            <button class="form-control btn-success" type="submit">
                                <i class='fa-solid fa-file-excel'></i>
                                Export Internet Metrics Report File
                            </button>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <h6 class="py-3 breadcrumb-wrapper">
        <a data-toggle="collapse" class="text-danger" 
            href="#IncidentsReportFiles" 
            aria-expanded="false" 
            aria-controls="IncidentsReportFiles">
            <i class="bx bx-error-alt bx-sm me-3"></i>
            Incidents Report
        </a>
    </h6> 

    <div class="row overflow-hidden collapse multi-collapse container" id="IncidentsReportFiles">
        <div class=" mb-4">
            <form method="POST" enctype='multipart/form-data' 
                action="<?php echo e(route('all-incident.export')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Export</label>
                            <input type="text" class="form-control" disabled
                            value="Incidents Report File"> 
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Click Here</label>
                            <button class="form-control btn-success" type="submit">
                                <i class='fa-solid fa-file-excel'></i>
                                Export Incidents Report File
                            </button>
                        </fieldset>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>

<hr>
<h4 class="py-3 breadcrumb-wrapper mb-2">
    <a data-toggle="collapse" class="text-primary" 
        href="#" 
        aria-expanded="false" 
        aria-controls="">
        <span class="text-muted fw-light">Surveys /</span> Files
    </a>
</h4>

<div class="container" >
    <h6 class="py-3 breadcrumb-wrapper">
        <a data-toggle="collapse" class="text-info" 
            href="#IssueHouseholdFiles" 
            aria-expanded="false" 
            aria-controls="IssueHouseholdFiles">
            <i class="bx bx-group bx-sm me-3"></i>
            Households
        </a>
    </h6>
    
    <div class="row collapse multi-collapse container" id="IssueHouseholdFiles">
        <div class="user-tasks">
            <div class="">
                <form method="POST" enctype='multipart/form-data' 
                    action="<?php echo e(route('missing-household.export')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Export</label>
                                <input type="text" class="form-control" disabled
                                value="Missing Households Details Report"> 
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4" >
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Click Here</label>
                                <button class="form-control btn-info" type="submit">
                                    <i class='fa-solid fa-file-excel'></i>
                                    Export Missing Households Details Report
                                </button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<hr>
<h4 class="py-3 breadcrumb-wrapper mb-2">
    <a data-toggle="collapse" class="text-primary" 
        href="#" 
        aria-expanded="false" 
        aria-controls="">
        <span class="text-muted fw-light">Projects /</span> Work Plan Progress
    </a>
</h4>

<div class="container mb-4">
    <?php 
        $userTypeId = 3;
        $collapseId = 'energyWorkPlans';
        $includeView = 'actions.admin.installation.ac_dc_process';
        $flag = 1;
    ?>
    <?php echo $__env->make('actions.admin.user_tasks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<div class="container mb-4">
    <?php
        $userTypeId = 4;
        $collapseId = 'maintenanceEnergyWorkPlans';
        $includeView = 'actions.admin.energy.maintenance';
    ?>
    <?php echo $__env->make('actions.admin.user_tasks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<hr>

<h4 class="py-3 breadcrumb-wrapper mb-2">
    <a data-toggle="collapse" class="text-primary" 
        href="#" 
        aria-expanded="false" 
        aria-controls="">
        <span class="text-muted fw-light">Platform /</span> Internal
    </a>
</h4>


<div class="container mb-4">
    <div class="user-tasks">
        <div class="d-flex flex-wrap mb-4">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($user->user_type_id == 1 && $user->id == 6): ?>
            <div>
                <div class="avatar avatar-xs me-2">
                    <?php if($user->image == ""): ?>
                        <?php if($user->gender == "male"): ?>
                            <img src="<?php echo e(url('users/profile/male.png')); ?>" class="rounded-circle">
                        <?php else: ?>
                            <img src="<?php echo e(url('users/profile/female.png')); ?>" class="rounded-circle">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" class="rounded-circle" />
                    <?php endif; ?>
                </div>
            </div> 
            <a data-toggle="collapse" class="text-dark" 
                href="#energySystemWorkPlans" 
                aria-expanded="false" 
                aria-controls="energySystemWorkPlans">
                Assigned this task to <strong><?php echo e($user->name); ?></strong>
            </a> 
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="energySystemWorkPlans" data-aos="fade-right"
            class="collapse multi-collapse timeline-event p-0 mb-4">
            <div class="row overflow-hidden container mb-4" >
                <div class="col-12">
                    <ul class="timeline timeline-center mt-5">
                        <!-- Action Items for internal -->
                        <?php echo $__env->make('actions.admin.internal.energy_system', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-4">
    <div class="user-tasks">
        <div class="d-flex flex-wrap mb-4">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($user->user_type_id == 2 && $user->id == 3): ?>
            <div>
                <div class="avatar avatar-xs me-2">
                    <?php if($user->image == ""): ?>
                        <?php if($user->gender == "male"): ?>
                            <img src="<?php echo e(url('users/profile/male.png')); ?>" class="rounded-circle">
                        <?php else: ?>
                            <img src="<?php echo e(url('users/profile/female.png')); ?>" class="rounded-circle">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" class="rounded-circle" />
                    <?php endif; ?>
                </div>
            </div> 
            <a data-toggle="collapse" class="text-dark" 
                href="#householdMissingDetailsWorkPlans" 
                aria-expanded="false" 
                aria-controls="householdMissingDetailsWorkPlans">
                Assigned this task to <strong><?php echo e($user->name); ?></strong>
            </a> 
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="householdMissingDetailsWorkPlans" data-aos="fade-right"
            class="collapse multi-collapse timeline-event p-0 mb-4">
            <div class="row overflow-hidden container mb-4" >
                <div class="col-12">
                    <ul class="timeline timeline-center mt-5">
                        <!-- Action Items for internal -->
                        <?php echo $__env->make('actions.admin.internal.missing_household_details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-4">
    <div class="user-tasks">
        <div class="d-flex flex-wrap mb-4">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($user->user_type_id == 1 && $user->id == 2): ?>
            <div>
                <div class="avatar avatar-xs me-2">
                    <?php if($user->image == ""): ?>
                        <?php if($user->gender == "male"): ?>
                            <img src="<?php echo e(url('users/profile/male.png')); ?>" class="rounded-circle">
                        <?php else: ?>
                            <img src="<?php echo e(url('users/profile/female.png')); ?>" class="rounded-circle">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" class="rounded-circle" />
                    <?php endif; ?>
                </div>
            </div> 
            <a data-toggle="collapse" class="text-dark" 
                href="#incidentsDonorPlans" 
                aria-expanded="false" 
                aria-controls="incidentsDonorPlans">
                Assigned this task to <strong><?php echo e($user->name); ?></strong>
            </a>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="incidentsDonorPlans" data-aos="fade-right"
            class="collapse multi-collapse timeline-event p-0 mb-4">
            <div class="row overflow-hidden container mb-4" >
                <div class="col-12">
                    <ul class="timeline timeline-center mt-5">
                        <!-- Action Items for incident -->
                        <?php echo $__env->make('actions.admin.internal.incident', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <!-- Action Items for donor -->
                        <?php echo $__env->make('actions.admin.internal.donor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mb-4">
    <div class="user-tasks">
        <div class="d-flex flex-wrap mb-4">
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($user->user_type_id == 1 && $user->id == 1): ?>
            <div>
                <div class="avatar avatar-xs me-2">
                    <?php if($user->image == ""): ?>
                        <?php if($user->gender == "male"): ?>
                            <img src="<?php echo e(url('users/profile/male.png')); ?>" class="rounded-circle">
                        <?php else: ?>
                            <img src="<?php echo e(url('users/profile/female.png')); ?>" class="rounded-circle">
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" class="rounded-circle" />
                    <?php endif; ?>
                </div>
            </div> 
            <a data-toggle="collapse" class="text-dark" 
                href="#internalWorkPlans" 
                aria-expanded="false" 
                aria-controls="internalWorkPlans">
                Assigned this task to <strong><?php echo e($user->name); ?></strong>
            </a>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="internalWorkPlans" data-aos="fade-right"
            class="collapse multi-collapse timeline-event p-0 mb-4">
            <div class="row overflow-hidden container mb-4" >
                <div class="col-12">
                    <ul class="timeline timeline-center mt-5">
                        <!-- Action Items for internal -->
                        <?php echo $__env->make('actions.admin.internal.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container mb-4">
    <?php
        $userTypeId = 6;
        $collapseId = 'internetWorkPlans';
        $includeView = 'actions.admin.internet.index';
    ?>
    <?php echo $__env->make('actions.admin.user_tasks', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>

<div class="row overflow-hidden collapse multi-collapse container mb-4" id="actionPlanTab">
    <div class="col-12">
        <ul class="timeline timeline-center mt-5">
            
            <!-- Action Items for AC/DC/MISC -->
            <?php echo $__env->make('actions.admin.installation.ac_dc_process', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- Action Items for maintenance -->
            <?php echo $__env->make('actions.admin.energy.maintenance', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <!-- Action Items for internet -->
            <?php echo $__env->make('actions.admin.internet.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <!-- Action Items for internal -->
            <?php echo $__env->make('actions.admin.internal.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          
            <!-- Action Items for adding energy system for In Progress
            <li class="timeline-item mb-md-4 mb-5 timeline-item-right">
                <span class="timeline-indicator timeline-indicator-primary" data-aos="zoom-in" data-aos-delay="200">
                    <i class="bx bx-reset"></i>
                </span>
                <div class="timeline-event card p-0" data-aos="fade-right">
                    <div class="card-header border-0 d-flex justify-content-between">
                        <h6 class="card-title mb-0">In Progress</h6>
                    </div>
                    <div class="card-body pb-0">
                        <div class="d-flex flex-wrap mb-4">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->user_type_id == 3): ?>
                            <div>
                                <div class="avatar avatar-xs me-2">
                                <?php if($user->image == ""): ?>
                    
                                <?php if($user->gender == "male"): ?>
                                    <img src='/users/profile/male.jpg'
                                        class="rounded-circle">
                                <?php else: ?>
                                    <img src='/assets/images/female.png'
                                        class="rounded-circle">
                                <?php endif; ?>
                                <?php else: ?>
                                    <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" 
                                        class="rounded-circle" />
                                <?php endif; ?>
                                </div>
                            </div>
                            <span>Assigned this task to <strong><?php echo e($user->name); ?></strong></span>
                            <?php endif; ?> 
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                         <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center ps-0">
                                <div>
                                    <i class="text-success bx bx-home"></i>
                                    <span>AC Communities</span>
                                </div>
                                <div>
                                    <a type="button" data-bs-toggle="modal" 
                                        data-bs-target="#communityAC">
                                        <i class='bx bx-message-alt-detail'></i>
                                    </a>
                                </div>
                                <?php echo $__env->make('employee.community.service.ac_survey', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </li>
                        </ul> 
                        <?php if(count($inProgressHouseholdsAcCommunity) > 0): ?>
                        <p>You've <?php echo e($inProgressHouseholdsAcCommunity->count()); ?>
                            <a type="button" title="Export AC Households"
                                href="action-item/in-progress-household/export">
                                In-Progress households 
                            </a>
                            Need an AC Installation, Follow up!
                        </p> 
                        <?php endif; ?>
                    </div>
                    <div class="timeline-event-time">In Progress</div>
                </div>
            </li> -->

            <!-- Action Items for adding energy details -->
            <!-- <li class="timeline-item mb-md-4 mb-5 timeline-item-right">
                <span class="timeline-indicator timeline-indicator-warning" data-aos="zoom-in" data-aos-delay="200">
                    <i class="bx bx-bulb"></i>
                </span>
                <div class="timeline-event card p-0" data-aos="fade-right">
                    <div class="card-header border-0 d-flex justify-content-between">
                        <h6 class="card-title mb-0">Energy Users</h6>
                    </div>
                    <div class="card-body pb-0">
                        <div class="d-flex flex-wrap mb-4">
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($user->user_type_id == 4): ?>
                            <div>
                                <div class="avatar avatar-xs me-2">
                                <?php if($user->image == ""): ?>
                    
                                <?php if($user->gender == "male"): ?>
                                    <img src='/users/profile/male.jpg'
                                        class="rounded-circle">
                                <?php else: ?>
                                    <img src='/assets/images/female.png'
                                        class="rounded-circle">
                                <?php endif; ?>
                                <?php else: ?>
                                    <img src="<?php echo e(url('users/profile/'.$user->image)); ?>" alt="Avatar" 
                                        class="rounded-circle" />
                                <?php endif; ?>
                                </div>
                            </div>
                            <span>Assigned this task to <strong><?php echo e($user->name); ?></strong></span>
                            <?php endif; ?> 
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <p>
                        <?php if(count($newEnergyUsers) > 0): ?>
                            <p>You've <?php echo e($newEnergyUsers->count()); ?>
                                <a title="Export AC Households"
                                    href="all-meter" target="_blank">
                                    New Energy Holders
                                </a>
                                need to fill out meter number, daily limit and other details.
                            </p> 
                        <?php endif; ?>
                        </p>
                    </div>
                    <div class="timeline-event-time">Energy Service</div>
                </div>
            </li> -->
        </ul>
    </div>
</div>

<script>

    $('#EnergyProjectFileForm').on('submit', function (event) {

        var englishValue = $('#cycleYearSelected').val();

        if (englishValue == null) {

            $('#energy_cycle_id_error').html('Please Select a cycle year!');
            return false;
        } else if (englishValue != null){

            $('#energy_cycle_id_error').empty();
        }

        $(this).addClass('was-validated');  
        $('#energy_cycle_id_error').empty();

        this.submit();
    });

    // View record photos
    $(document).on('click', '.viewMissingEnergyUserDonors', function() {

        community_id = $(this).data("id");

        $.ajax({
            url: "missing/donors/" + community_id,
            type: 'get',
            success: function(response) {
                //$('#missingEnergyUserDonorsModel').modal('toggle');
                $('#missingEnergyUserDonorsModelTitle').html(response.community.english_name);
                $('#missingEnergyUserDonorsContent').find('tbody').html('');
                response.html.forEach(refill_table);
                function refill_table(item, index){
                    $('#missingEnergyUserDonorsContent').find('tbody').append('<tr><td>'+item.holder_name+'</td><td>'+item.name+'</td><td>'+item.status+'</td></tr>');
                }

                $('#missingEnergyUserDonorsModel').modal('show');
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/index.blade.php ENDPATH**/ ?>