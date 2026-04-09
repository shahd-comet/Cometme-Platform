

<?php $__env->startSection('title', 'all internet users'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>

<style>
    label, input {
    display: block;
}

label, table { 
    margin-top: 20px;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<p>
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseInternetUsersVisualData" 
        role="button" aria-expanded="false" aria-controls="collapseInternetUsersVisualData">
        <i class="menu-icon tf-icons bx bx-show-alt"></i>
        Visualize Data
    </a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseInternetUsersExport" aria-expanded="false" 
        aria-controls="collapseInternetUsersExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseInternetUsersVisualData collapseInternetUsersExport">
        <i class="menu-icon tf-icons bx bx-expand-alt"></i>
        Toggle All
    </button>
</p> 

<div class="collapse multi-collapse container mb-4" id="collapseInternetUsersVisualData">
       <!-- Internet Users -->
<!-- 
    <div class="card mb-4">
        <div class="card-body">
            <h5>Internet Users</h5>
            <div class="row">
                <div class="">
                    <div class="">
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <a href="<?php echo e('internet-user'); ?>" target="_blank" type="button"> 
                                            <i class='bx bx-wifi'></i>
                                        </a>
                                    </span>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Internet Users</span>
                                        <span class="text-muted"><?php echo e($internetPercentage); ?>%</span> 
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-success" style="width: <?php echo e($internetPercentage); ?>%" 
                                            role="progressbar" aria-valuenow="<?php echo e($internetPercentage); ?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="<?php echo e($allInternetPeople); ?>">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div class="d-flex align-content-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <a type="button" data-bs-toggle="modal" 
                                            data-bs-target="#communityInternet">
                                            <i class='bx bx-home'></i>
                                        </a>
                                    </span>
                                </div>
                                <div class="chart-info">
                                    <h5 class="mb-0"><?php echo e($dataJson[0]["total_active_communities"]); ?></h5>
                                    <small class="text-muted">Active Communities</small>
                                </div>
                            </div>
                            <?php echo $__env->make('employee.community.service.internet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="d-flex align-content-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class='bx bx-book-content bx-large'></i>
                                    </span>
                                </div>
                                <div class="chart-info"> 
                                    <h5 class="mb-0"><?php echo e($allContractHolders); ?></h5>
                                    <small class="text-muted">Contract Holders</small>
                                </div>
                            </div>
                            <div class="d-flex align-content-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class='bx bx-user'></i>
                                    </span>
                                </div>
                                <div class="chart-info">
                                    <h5 class="mb-0"><?php echo e($allInternetUsersCounts); ?></h5>
                                    <small class="text-muted">Users</small>
                                </div>
                            </div>
                            <div class="d-flex align-content-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class='bx bx-happy bx-large'></i>
                                    </span>
                                </div>
                                <div class="chart-info"> 
                                    <h5 class="mb-0"><?php echo e($youngInternetHolders); ?></h5>
                                    <small class="text-muted">Young Holders</small>
                                </div>
                            </div>
                            <div class="d-flex align-content-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <i class='bx bx-buildings'></i>
                                    </span>
                                </div>
                                <div class="chart-info">
                                    <h5 class="mb-0"><?php echo e($InternetPublicCount); ?></h5>
                                    <small class="text-muted">Public Structures</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->


    <div class="card mb-4">
        <div class="card-body">
            <h5>Contracts Overview</h5>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="row">
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_communities"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Internet Communities</span>
                                <div class="primary">
                                    <i class="bx bx-wifi me-1 bx-lg text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_active_communities"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Active Communities</span>
                                <div class="">
                                    <a  target="_blank" type="button">
                                    <i class="bx bx-home-smile me-1 bx-lg text-success"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_inactive_communities"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Non-active Communities</span>
                                <div class="">
                                    <a  target="_blank" type="button">
                                    <i class="bx bx-home-alt me-1 bx-lg text-danger"></i>
                                    </a>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_sale_points"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Sale Points</span>
                                <div class="primary">
                                    <i class="bx bx-wallet me-1 bx-lg text-dark"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_paid_cash"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Total Paid Cash</span>
                                <div class="">
                                    <a  target="_blank" type="button">
                                    <i class="bx bx-shekel me-1 bx-lg text-primary"></i>
                                    </a>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_contracts"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Total Contracts</span>
                                <div class="primary">
                                    <i class="bx bx-user-voice me-1 bx-lg text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_active_contracts"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Active Contracts</span>
                                <div class="">
                                    <a href="<?php echo e('community'); ?>" target="_blank" type="button">
                                    <i class="bx bx-comment-add me-1 bx-lg text-success"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-3 col-md-3 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_expire_contracts"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Expire Contracts</span>
                                <div class="">
                                    <a  target="_blank" type="button">
                                    <i class="bx bx-comment-minus me-1 bx-lg text-danger"></i>
                                    </a>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-sm-4 col-md-4 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_accounts_expired_less_30_days"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Expire Contracts < month</span>
                                <div class="">
                                    <a  target="_blank" type="button">
                                    <i class="bx bx-calendar-week me-1 bx-lg text-info"></i>
                                    </a>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 mb-4">
                        <div class="col">
                            <div class="card-body text-center">
                                <h3 class="mb-1">
                                    <?php $__currentLoopData = $dataJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($data["total_accounts_expired_over_30_days"]); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </h3>
                                <span class="text-muted">Expire Contracts > month</span>
                                <div class="">
                                    <a  target="_blank" type="button">
                                    <i class="bx bx-calendar-alt me-1 bx-lg text-muted"></i>
                                    </a>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-2 pt-4 pb-1">Internet Clusters</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="h-100">
                        <div class="text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-purchase-tag fs-4"></i></span>
                            </div>
                            <h6 class="mb-0 text-primary">Name</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="h-100">
                        <div class="text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-purchase-tag fs-4"></i></span>
                            </div>
                            <h6 class="mb-0 text-warning">ISP</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="h-100">
                        <div class="text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-purchase-tag fs-4"></i></span>
                            </div>
                            <h6 class="mb-0 text-info">Attached Communities</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 mb-4">
                    <div class="h-100">
                        <div class="text-center">
                            <div class="avatar mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-purchase-tag fs-4"></i></span>
                            </div>
                            <h6 class="mb-0 text-success">Active Contracts</h6>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($clustersJson): ?>
            <?php $__currentLoopData = $clustersJson; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cluster): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="text-center">
                        <span class="d-block"><?php echo e($cluster["cluster_name"]); ?></span>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="text-center">
                        <span class="d-block"><?php echo e($cluster["isp"]); ?></span>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-12">
                    <div class="text-center">
                        <span class="d-block"><?php echo e($cluster["attached_communities"]); ?></span>
                    </div>
                </div>
            
                <div class="col-lg-3 col-md-6 col-12">
                    <div class="text-center">
                        <span class="d-block"><?php echo e($cluster["active_contracts"]); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-2 pt-4 pb-1">% of Non-paying Holders</h5>
        </div>
        <div class="card-body">
            <div style="width: 80%; margin: auto;">
                <canvas id="nonPayingChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-2 pt-4 pb-1">% of Non-paying Holders - Detailed</h5>
        </div>
        <div class="card-body">
            <div style="width: 80%; margin: auto;">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

</div>

<div class="collapse multi-collapse container mb-4" id="collapseInternetUsersExport">
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                Export Internet User Report
                                    <i class='fa-solid fa-file-excel text-info'></i>
                                </h5>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <fieldset class="form-group">
                                    <button class="" id="clearInternetUserFiltersButton">
                                    <i class='fa-solid fa-eraser'></i>
                                        Clear Filters
                                    </button>
                                </fieldset>
                            </div>
                        </div> 
                    </div>
                    <form method="POST" enctype='multipart/form-data' 
                        action="<?php echo e(route('internet-user.export')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="card-body"> 
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="community"
                                        class="selectpicker form-control" data-live-search="true">
                                        <option disabled selected>Search Community</option>
                                        <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($community->english_name); ?>">
                                            <?php echo e($community->english_name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="donor"
                                        class="selectpicker form-control" data-live-search="true">
                                        <option disabled selected>Search Donor</option>
                                        <?php $__currentLoopData = $donors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($donor->id); ?>">
                                            <?php echo e($donor->donor_name); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <input type="date" name="start_date" 
                                     id="startDate" class="form-control" title="Data from"> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <button class="btn btn-info" type="submit">
                                    <i class='fa-solid fa-file-excel'></i>
                                    Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
        </div> 
    </div> 
</div> 

<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">All </span> Internet Contract Holders
</h4>

<?php if(session()->has('message')): ?>
    <div class="row">
        <div class="alert alert-success">
            <?php echo e(session()->get('message')); ?>

        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="card my-2">
        <div class="card-header">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Community</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByCommunity">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($community->id); ?>"><?php echo e($community->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Town</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByTown">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $towns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $town): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($town->id); ?>"><?php echo e($town->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Type</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByType">
                            <option disabled selected>Choose one...</option>
                            <option value="community_holder">Community Holder</option>
                            <option value="town_holder">Town Holder</option>
                            <option value="community_internal">Community Internal</option>
                            <option value="activist_holder">Activist</option>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Installation Date</label>
                        <input type="date" name="date" class="form-control"
                            id="filterByInstallationDate">
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Clear All Filters</label>
                        <button class="btn btn-dark" id="clearFiltersButton">
                            <i class='fa-solid fa-eraser'></i>
                            Clear Filters
                        </button>
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div>
            <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                Auth::guard('user')->user()->user_type_id == 2 ||
                Auth::guard('user')->user()->user_type_id == 6 ||
                Auth::guard('user')->user()->user_type_id == 10  ||
                Auth::guard('user')->user()->user_type_id == 13): ?>
                <!--<div style="margin-top:30px">-->
                <!--    <button type="button" class="btn btn-success" -->
                <!--        id="getInternetHolders">-->
                <!--        Get Latest Internet Holders-->
                <!--    </button>-->
                <!--</div>-->
            <?php endif; ?> 
            </div>
            <!-- 
            <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                Auth::guard('user')->user()->user_type_id == 2 ): ?>
                <div style="margin-top:18px">
                    <a type="button" class="btn btn-success" 
                        href="<?php echo e(url('internet-user', 'create')); ?>" >
                        Create New Internet Holder
                    </a>
                </div>
            <?php endif; ?> -->

            <table id="internetAllUsersTable" class="table table-striped data-table-internet-users my-2">
                <thead>
                    <tr>
                        <th>Contract Holder</th>
                        <th>Community/Town</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $__env->make('users.internet.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script type="text/javascript">

    var table;
    function DataTableContent() {
        table = $('.data-table-internet-users').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('internet-user.index')); ?>",
                data: function (d) {
                    d.search = $('input[type="search"]').val();
                    d.community_filter = $('#filterByCommunity').val();
                    d.town_filter = $('#filterByTown').val();
                    d.type_filter = $('#filterByType').val();
                    d.date_filter = $('#filterByInstallationDate').val();
                }
            },
            columns: [
                {data: 'holder'},
                {data: 'community_town_name', name: 'community_town_name'},
                {data: 'type', name: 'type'},
                {data: 'start_date', name: 'start_date'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ] 
        });
    }


    $(function () {
        DataTableContent();
        
        $('#filterByCluster').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByInstallationDate').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByCommunity').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByTown').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByType').on('change', function() {
            table.ajax.reload(); 
        });

        // Clear Filter
        $('#clearFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            $('#filterByInstallationDate').val(' ');
            if ($.fn.DataTable.isDataTable('.data-table-internet-users')) {
                $('.data-table-internet-users').DataTable().destroy();
            }
            DataTableContent();
        });
    });

    // Clear Filters for Export
    $('#clearInternetUserFiltersButton').on('click', function() {

        $('.selectpicker').prop('selectedIndex', 0);
        $('.selectpicker').selectpicker('refresh');
        $('#startDate').val(' ');
    });

    // View record details
    $('#internetAllUsersTable').on('click', '.detailsInternetButton',function() {
        var id = $(this).data('id');
    
        // AJAX request
        $.ajax({
            url: 'internet-user/' + id,
            type: 'get',
            dataType: 'json',
            success: function(response) { 

                $('#internetModalTitle').html(" ");
                $('#nameHolder').html(" ");
                $('#phoneNumberHolder').html(" ");
                $('#cometIdHolder').html(" ");
                $('#energyMeterHolder').html(" ");
                $('#installationDate').html(" ");
                $('#communityOrTown').html(" ");
 

                if(response['household']) {

                    if(response['household'].english_name) {

                        $('#internetModalTitle').html(response['household'].english_name);
                        $('#nameHolder').html(response['household'].english_name);
                    } else {
                        
                        $('#internetModalTitle').html(response['household'].arabic_name);
                        $('#nameHolder').html(response['household'].arabic_name);
                    }

                    $('#phoneNumberHolder').html(response['household'].phone_number);
                    $("#holderIcon").removeClass('bx bx-building bx-sm me-3');
                    $("#holderIcon").removeClass('bx bx-group bx-sm me-3');
                    $("#holderIcon").addClass('bx bx-user bx-sm me-3');
                    $('#cometIdHolder').html(response['household'].comet_id);
                } else if(response['public']) {

                    if(response['public'].english_name) {

                        $('#internetModalTitle').html(response['public'].english_name);
                        $('#nameHolder').html(response['public'].english_name);
                    } else {

                        $('#internetModalTitle').html(response['public'].arabic_name);
                        $('#nameHolder').html(response['public'].arabic_name);
                    }

                    $('#phoneNumberHolder').html(response['public'].phone_number);
                    $("#holderIcon").removeClass('bx bx-user bx-sm me-3');
                    $("#holderIcon").removeClass('bx bx-group bx-sm me-3');
                    $("#holderIcon").addClass('bx bx-building bx-sm me-3');
                    $('#cometIdHolder').html(response['public'].comet_id);
                } else if(response['townHolder']) {

                    if(response['townHolder'].english_name) {

                        $('#internetModalTitle').html(response['townHolder'].english_name);
                        $('#nameHolder').html(response['townHolder'].english_name);
                    } else {

                        $('#internetModalTitle').html(response['townHolder'].arabic_name);
                        $('#nameHolder').html(response['townHolder'].arabic_name);
                    }

                    $('#phoneNumberHolder').html(response['townHolder'].phone_number);
                    $("#holderIcon").removeClass('bx bx-user bx-sm me-3');
                    $("#holderIcon").removeClass('bx bx-building bx-sm me-3');
                    $("#holderIcon").addClass('bx bx-group bx-sm me-3');
                    $('#cometIdHolder').html(response['townHolder'].comet_id);
                }

                $('#communityHolder').html(" ");
                if(response['community']) {

                    $('#communityOrTown').html("Community");
                    $('#communityHolder').html(response['community'].english_name);
                } else if(response['town']) {
  
                    $('#communityOrTown').html("Town");
                    $('#communityHolder').html(response['town'].english_name);
                }

                if(response['energyHolder']) {

                    $('#energyMeterHolder').html(response['energyHolder'].meter_number);
                    $('#installationDate').html(response['energyHolder'].installation_date);
                }

                $('#startContractDate').html(" ");
                $('#startContractDate').html(response['internetUser'].start_date);
                $('#internetSystem').html(" ");
                if(response['internetUser'].is_hotspot == 1) $('#internetSystem').html("Hotspot");
                else if(response['internetUser'].is_ppp == 1) $('#internetSystem').html("Broadband");
                $('#internetStatus').html(" ");
                $('#internetStatus').html(response['internetStatus'].name);
                $('#internetPaid').html(" ");
                if(response['internetUser'].paid == 1) $('#internetPaid').html("Yes");
                else if(response['internetUser'].paid == 0) $('#internetPaid').html("No");
                $('#lastPurchaseDate').html(" ");
                $('#lastPurchaseDate').html(response['internetUser'].last_purchase_date);
                $('#internetDonors').html(" ");
                for (var i = 0; i < response['donors'].length; i++) {
                    $("#internetDonors").append(
                        '<ul><li>'+ response['donors'][i].donor_name +'</li> </ul>');
                }
                
                $('#incidentName').html(" ");
                $('#IncidentDate').html(" ");
                if(response['internetIncidents'] != []) {

                    for (var i = 0; i < response['internetIncidents'].length; i++) {

                        $('#incidentName').html(response['internetIncidents'][i].incident);
                        $('#IncidentDate').html(response['internetIncidents'][i].incident_date);
                    }
                }
            }
        });
    }); 

    // Update record
    $('#internetAllUsersTable').on('click', '.updateInternetUser',function() {
        var id = $(this).data('id');
        var url = window.location.href; 
        url = url +'/'+ id +'/edit';
        // AJAX request
        $.ajax({
            url: 'internet-user/' + id + '/editpage',
            type: 'get',
            dataType: 'json',
            success: function(response) {
                window.open(url, "_self");
            }
        });
    });
  
    // Get all Contract Holders
    $('#getInternetHolders').on('click', function() {

        // AJAX request
        $.ajax({
            url: 'api/internet-holder',
            type: 'get',
            dataType: 'json',
            success: function(response) {

                Swal.fire({
                    icon: 'success',
                    title: 'Internet Contract Holders Gotten Successfully!',
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'Okay!' 
                }).then((result) => {

                    $('#internetAllUsersTable').DataTable().draw();
                });
            }
        });
    });

    // Delete record
    $('#internetAllUsersTable').on('click', '.deleteInternetUser', function() {
        var id = $(this).data('id');

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure you want to delete this user?',
            showDenyButton: true,
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: "<?php echo e(route('deleteInternetHolder')); ?>",
                    type: 'get',
                    data: {id: id},
                    success: function(response) {
                        if(response.success == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: response.msg,
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'Okay!'
                            }).then((result) => {
                                $('#internetAllUsersTable').DataTable().draw();
                            });
                        } else {

                            alert("Invalid ID.");
                        }
                    }
                });
            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }
        });
    });

    var data = <?php echo json_encode($percentageData, 15, 512) ?>;

    // Extract periods, cluster names, unpaid_percentage values, and total_contracts
    var periods = Object.keys(data);
    var clusterNames = [];
    var datasets = [];
    var colors = ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(153, 102, 255, 0.2)']; // Add more colors if needed

    // Extract cluster names, unpaid_percentage values, and total_contracts for each period
    periods.forEach(function(period) {
        var clusterData = data[period];
        Object.keys(clusterData).forEach(function(clusterName) {
            if (!clusterNames.includes(clusterName)) {
                clusterNames.push(clusterName);
            }
        });
    });

    // Prepare datasets for each cluster
    clusterNames.forEach(function(clusterName, index) {
        var dataset = {
            label: clusterName,
            data: [], 
            totalContracts: [], 
            unpaid: [], // Store total_contracts values
            backgroundColor: colors[index % colors.length], // Assign color based on index
            borderColor: colors[index % colors.length].replace('0.2', '1'), // Set border color
            borderWidth: 1,
            pointRadius: 5,
            pointHoverRadius: 7
        };
        periods.forEach(function(period) {
            var clusterData = data[period][clusterName];
            dataset.data.push(clusterData.unpaid_percentage);
            dataset.totalContracts.push(clusterData.total_contracts);
            dataset.unpaid.push(clusterData.total_unpaid); 
        });
        datasets.push(dataset);
    });

    // Create chart
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: periods, // X-axis: Periods
            datasets: datasets // Y-axis: Unpaid Percentage for each cluster
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Period (Date From - Date To)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: '% of Total Unpaid'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return periods[tooltipItems[0].dataIndex];
                        },
                        label: function(context) {
                            var clusterName = context.dataset.label;
                            var value = context.parsed.y;
                            var index = context.dataIndex;
                            var totalContracts = context.dataset.totalContracts[index]; 
                            var unpaid = context.dataset.unpaid[index]; 
                            return clusterName + ': ' + value + '% - Total Contracts: ' + totalContracts + ' ( Unpaid: ' + unpaid + ' )' ;
                        }
                    }
                }
            }
        }
    });


    var chartData = <?php echo json_encode($chartData); ?>;

    // Create a new Chart.js instance
    var ctx = document.getElementById('nonPayingChart').getContext('2d');
    var myChart1 = new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            scales: {
                xAxes: [{
                    type: 'time',
                    time: {
                        unit: 'month'
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Date'
                    }
                }],
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Number of Contracts'
                    }
                }]
            }
        }
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/users/internet/index.blade.php ENDPATH**/ ?>