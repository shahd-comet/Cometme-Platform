<?php
  $pricingModal = true;
?>



<?php $__env->startSection('title', 'energy holders'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
 
<p>
    <!-- <a class="btn btn-primary" data-toggle="collapse" href="#collapseEnergyUserVisualData" 
        role="button" aria-expanded="false" aria-controls="collapseEnergyUserVisualData">
        <i class="menu-icon tf-icons bx bx-show-alt"></i>
        Visualize Data
    </a> -->
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseEnergyUserExport" aria-expanded="false" 
        aria-controls="collapseEnergyUserExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button>
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseEnergyUserPurchaseReport" 
        role="button" aria-expanded="false" aria-controls="collapseEnergyUserPurchaseReport">
        <i class="menu-icon tf-icons bx bx-purchase-tag"></i>
        Purchase Report
    </a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseEnergyUserVisualData collapseEnergyUserExport">
        <i class="menu-icon tf-icons bx bx-expand-alt"></i>
        Toggle All
    </button> 
</p> 
 

<div class="collapse multi-collapse mb-4" id="collapseEnergyUserVisualData">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="panel panel-primary">
                    <div class="panel-header">
                        <h5>Electricity Meter Issues</h5>
                    </div>
                    <div class="panel-body">
                        <div id="energyUserChart">
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="collapse multi-collapse container mb-4" id="collapseEnergyUserExport">
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                Export Electricity Meter Users Report 
                                    <i class='fa-solid fa-file-excel text-info'></i>
                                </h5>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <fieldset class="form-group">
                                    <button class="" id="clearEnergyHolderFiltersButton">
                                    <i class='fa-solid fa-eraser'></i>
                                        Clear Filters
                                    </button>
                                </fieldset>
                            </div>
                        </div> 
                    </div>
                    <form method="POST" enctype='multipart/form-data' id="exportFromEnergyHolder"
                        action="<?php echo e(route('energy-meter.export')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="card-body"> 
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>File Type</label>
                                        <select name="file_type" required id="fileType"
                                            class="selectpicker form-control" data-live-search="true" >
                                            <option disabled selected>Select File Type</option>
                                            <option value="all">All Energy Holders</option>
                                            <option value="comet">Comet Holders</option>
                                            <option value="refrigerator">Refrigerator Holders</option>
                                            <option value="deactivated">Deactivated Holders</option>
                                        </select> 
                                        <div id="file_type_error" style="color: red;"></div>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Region</label>
                                        <select name="region_id"
                                            class="selectpicker form-control" data-live-search="true">
                                            <option disabled selected>Search Region</option>
                                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($region->id); ?>">
                                                    <?php echo e($region->english_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Community</label>
                                        <select name="community_id"
                                            class="selectpicker form-control" data-live-search="true">
                                            <option disabled selected>Search Community</option>
                                            <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($community->id); ?>">
                                                    <?php echo e($community->english_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>New/MISC/Grid extension</label>
                                        <select name="type" 
                                            class="selectpicker form-control" >
                                            <option disabled selected>Search Installation Type</option>
                                            <?php $__currentLoopData = $installationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installationType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($installationType->id); ?>">
                                                    <?php echo e($installationType->type); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>System Type</label>
                                        <select name="energy_system_type_id" class="selectpicker form-control" 
                                            data-live-search="true">
                                            <option disabled selected>Search System Type</option>
                                            <?php $__currentLoopData = $energySystemTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energySystemType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($energySystemType->id); ?>"><?php echo e($energySystemType->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Meter Status</label>
                                        <select name="meter_case_id" class="selectpicker form-control" 
                                            data-live-search="true" >
                                            <option disabled selected>Search Meter Status</option>
                                            <?php $__currentLoopData = $meterCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meterCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($meterCase->id); ?>"><?php echo e($meterCase->meter_case_name_english); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Service Year</label>
                                        <select name="service_year" class="selectpicker form-control" 
                                            data-live-search="true">
                                            <option disabled selected>Search</option>
                                            <?php 
                                                $startYear = 2010; // C
                                                $currentYear = date("Y");
                                            ?>
                                            <?php for($year = $currentYear; $year >= $startYear; $year--): ?>
                                                <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                                            <?php endfor; ?>
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Installation date from</label>
                                        <input type="date" class="form-control" name="date_from"
                                        id="installationEnergyDateFrom">
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Installation date to</label>
                                        <input type="date" class="form-control" name="date_to"
                                        id="installationEnergyDateTo">
                                    </fieldset>
                                </div>
                            </div><br>
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <label class='col-md-12 control-label'>Download Excel</label>
                                    <button class="btn btn-info" type="submit" id="exportEnergyHoldersButton">
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

<div class="collapse multi-collapse mb-4" id="collapseEnergyUserPurchaseReport">
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                Import Your File from Vending Software 
                                    <i class='menu-icon tf-icons bx bx-export text-info'></i>
                                </h5>
                            </div>
                        </div> 
                    </div>
                    <form method="POST" enctype='multipart/form-data' 
                        action="<?php echo e(route('energy-meter.import')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-4">
                                    <fieldset class="form-group">
                                        <label for="">Upload the file</label>
                                        <input name="first_file" type="file"
                                            class="form-control" required>
                                    </fieldset>
                                </div>
                                <!-- <div class="col-xl-4 col-lg-4 col-md-4">
                                    <fieldset class="form-group">
                                        <label for="">Upload the second file</label>
                                        <input name="second_file" type="file"
                                            class="form-control">
                                    </fieldset>
                                </div> -->
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <label for="">Click here!</label>
                                    <button class="btn btn-info" type="submit">
                                        <i class='fa-solid fa-file-excel'></i>
                                        Proccess
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
    <span class="text-muted fw-light">This page shows </span> Services Related to Energy
    <!-- <h5 class="text-success">Toatl Meters: 
        <span class="text-secondary"><?php echo e($totalMeters); ?></span>
        <span class="text-muted fw-light"> [ </span>
        <span class="text-secondary"><?php echo e($totalHouseholdMeters); ?></span>
        <span class="text-muted fw-light"> Households</span>
        <span class="text-success"> & </span>
        <span class="text-secondary"><?php echo e($totalHouseholdPublicMeters); ?></span>
        <span class="text-muted fw-light"> Public</span>
        <span class="text-muted fw-light"> ]</span>
    </h5> -->
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
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Region</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByRegion">
                            <option disabled selected>Search Region</option>
                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($region->id); ?>"><?php echo e($region->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Community</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByCommunity">
                            <option disabled selected>Search Community</option>
                            <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($community->id); ?>"><?php echo e($community->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>New/MISC/Grid extension</label>
                        <select name="type" id="filterByType" 
                            class="selectpicker form-control" >
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $installationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installationType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($installationType->id); ?>">
                                    <?php echo e($installationType->type); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By System Type</label>
                        <select name="energy_system_type_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByEnergySystemType">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $energySystemTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energySystemType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($energySystemType->id); ?>"><?php echo e($energySystemType->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Meter Status</label>
                        <select name="meter_case_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByMeterStatus">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $meterCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meterCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($meterCase->id); ?>"><?php echo e($meterCase->meter_case_name_english); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Cycle Year</label>
                        <select name="meter_case_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByCycleYear">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $cycleYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cycleYear): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cycleYear->id); ?>"><?php echo e($cycleYear->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Service Year</label>
                        <select name="service_year" class="selectpicker form-control" 
                            data-live-search="true" id="filterByYear">
                            <option disabled selected>Choose one...</option>
                            <?php
                                $startYear = 2010; // C
                                $currentYear = date("Y");
                            ?>
                            <?php for($year = $currentYear; $year >= $startYear; $year--): ?>
                                <option value="<?php echo e($year); ?>"><?php echo e($year); ?></option>
                            <?php endfor; ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Installation date from</label>
                        <input type="date" class="form-control" name="date_from"
                        id="filterByDateFrom">
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
               
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role="tablist" style="padding-top:25px">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#energy-holders" role="tab">
                        <i class='fas fa-lightbulb me-2'></i>
                        All Holders
                        <span id="energyHoldersCount" class="badge ms-2" style="background-color: #d6f7fa; color: #00cfdd;">
                     
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#shared-holders" role="tab">
                        <i class='fas fa-plug me-2'></i> 
                        Shared 
                        <span id="sharedHoldersCount" class="badge ms-2" style="background-color: #e7ebef; color: #69809a;">
                     
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#comet-holders" role="tab">
                        <i class='fas fa-code me-2'></i> 
                        Comet meters 
                        <span id="cometCount" class="badge ms-2" style="background-color: #e7ebef; color: #69809a;">
    
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#refrigerator-holders" role="tab">
                        <i class='fas fa-solar-panel me-2'></i> 
                        Refrigerator
                        <span id="refrigeratorHoldesCount" class="badge ms-2" style="background-color: #e7ebef; color: #69809a;">
      
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#deactivated-holders" role="tab">
                        <i class='fas fa-battery-quarter me-2'></i> 
                        Reactivated
                        <span id="deactivatedHoldersCount" class="badge ms-2" style="background-color: #e7ebef; color: #69809a;">
             
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3">
                <!-- All Energy Holders Tab (users and public) -->
                <div class="tab-pane fade show active" id="energy-holders" role="tabpanel" 
                    aria-labelledby="allHolders-tab">
                    <table id="energyAllUsersTable" class="table table-striped data-table-energy-users my-2">
                        <thead>
                            <tr>
                                <th class="text-center">Holder</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Meter Number</th>
                                <th class="text-center">Meter Active</th>
                                <th class="text-center">Energy System</th>
                                <th class="text-center">Energy System Type</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- All Shared Holders Tab -->
                <div class="tab-pane fade" id="shared-holders" role="tabpanel" aria-labelledby="shared-tab">
                    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 3 ||
                        Auth::guard('user')->user()->user_type_id == 4 ||
                        Auth::guard('user')->user()->user_type_id == 12): ?>
                        <div>
                            <button type="button" class="btn btn-success" 
                                data-bs-toggle="modal" data-bs-target="#createHouseholdMeter">
                                Create New Shared Holder
                            </button>
                            <?php echo $__env->make('users.energy.shared.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endif; ?>
                    <table id="allHouseholdMeterTable" class="table table-striped data-table-energy-shared my-2">
                        <thead>
                            <tr>
                                <th class="text-center">Shared Holder (Household/Public)</th>
                                <th class="text-center">Meter Holder</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- All Comet Holders Tab -->
                <div class="tab-pane fade" id="comet-holders" role="tabpanel" aria-labelledby="comet-tab">
                    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 3 ||
                        Auth::guard('user')->user()->user_type_id == 4 ||
                        Auth::guard('user')->user()->user_type_id == 12 ||
                        Auth::guard('user')->user()->role_id == 21): ?>
                        <div>
                            <button type="button" class="btn btn-success" 
                                data-bs-toggle="modal" data-bs-target="#createMeterPublic">
                                Create New Comet Meter	
                            </button>
                            <?php echo $__env->make('users.energy.comet.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endif; ?>
                    <table id="energyCometMeterTable" 
                        class="table table-striped data-table-energy-comet-meters my-2">
                        <thead>
                            <tr>
                                <th class="text-center">Name</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Meter Number</th>
                                <th class="text-center">Energy System</th>
                                <th class="text-center">Energy System Type</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- All Refrigerator Holders Tab -->
                <div class="tab-pane fade" id="refrigerator-holders" role="tabpanel" aria-labelledby="refrigerator-tab">
                    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||  
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 3 ||
                        Auth::guard('user')->user()->user_type_id == 7 ): ?>
                        <div>
                            <button type="button" class="btn btn-success" 
                                data-bs-toggle="modal" data-bs-target="#createRefrigeratorHolder">
                                Create New Refrigerator Holder
                            </button>
                            <?php echo $__env->make('users.refrigerator.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                    <?php endif; ?>
                    <table id="refrigeratorTable" class="table table-striped data-table-refrigerators my-2">
                        <thead>
                            <tr>
                                <th class="text-center">Energy Holder</th>
                                <th class="text-center">Meter Number</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Year</th>
                                <th class="text-center">Refrigerator Type</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- All Reactivated Holders Tab -->
                <div class="tab-pane fade" id="deactivated-holders" role="tabpanel" aria-labelledby="deactivated-tab">
                    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||  
                        Auth::guard('user')->user()->user_type_id == 2 ): ?>
                        <form action="<?php echo e(route('deactivated-holder.import')); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <input type="file" name="excel_file" class="form-control-file" id="excel_file"required>
                                <?php $__errorArgs = ['excel_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger mt-2"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div> <br>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class='fa-solid fa-upload'></i>
                                    Import New Reactivated Holder
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                    <table id="deactivatedTable" class="table table-striped data-table-deactivated my-2">
                        <thead>
                            <tr>
                                <th class="text-center">Energy Holder</th>
                                <th class="text-center">Meter Number</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Visit Date</th>
                                <th class="text-center">Deactivation after the war</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('users.energy.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('users.energy.shared.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('users.energy.comet.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('users.refrigerator.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('users.energy.reactivated.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('components.meter-history-complete', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
 
    $('#exportFromEnergyHolder').on('submit', function (event) {

        event.preventDefault(); 

        let valid = true;

        var fileTypeValue = $('#fileType').val();

        if (fileTypeValue == null) {

            $('#file_type_error').html('Please select a type!'); 
            return false;
        } else  if (fileTypeValue != null) {

            $('#file_type_error').empty();
        }

        $('#file_type_error').empty();

        if (valid) {

            $(this).addClass('was-validated');
            this.submit(); 
        }
    });

    // Update the countable values for water 
    function updateCountValue() {

        $.ajax({
            url: "<?php echo e(route('energy.counts')); ?>",
            type: "GET",
            success: function(response) {

                $("#energyHoldersCount").text(response.allEnergyCount);
                $("#sharedHoldersCount").text(response.sharedCount);
                $("#cometCount").text(response.cometCount);
                $("#refrigeratorHoldesCount").text(response.refrigeratorCount);
                $("#deactivatedHoldersCount").text(response.deactivatedCount);
            }
        });
    }

    // Clear Filters for Export
    $('#clearEnergyHolderFiltersButton').on('click', function() {

        $('.selectpicker').prop('selectedIndex', 0);
        $('.selectpicker').selectpicker('refresh');
        $('#installationEnergyDateFrom').val(' ');
        $('#installationEnergyDateTo').val(' ');
    });

    $(function () {

        // keep track of initialized tables
        var tables = {};

        function initAllEnergyHoldersTable() {

            if (tables.allHolders) return;
            tables.allHolders = $('.data-table-energy-users').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('all-meter.index')); ?>",
                    data: function (d) {
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.type_filter = $('#filterByType').val();
                        d.date_filter = $('#filterByDateFrom').val();
                        d.system_type_filter = $('#filterByEnergySystemType').val();
                        d.meter_filter = $('#filterByMeterStatus').val();
                        d.cycle_filter = $('#filterByCycleYear').val();
                        d.year_filter = $('#filterByYear').val();
                    }
                },
                columns: [
                    {data: 'holder', name: 'holder'},
                    {data: 'community_name', name: 'community_name'},
                    {
                        data: 'meter_number',
                        name: 'meter_number',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // clickable meter number that opens modal
                                return '<a href="#" class="show-meter-history" data-meter="' + data + '">' + data + '</a>';
                            }
                            return data;
                        }
                    },
                    {data: 'meter_case_name_english', name: 'meter_case_name_english'},
                    {data: 'energy_name', name: 'energy_name'},
                    {data: 'energy_type_name', name: 'energy_type_name'},
                    {data: 'action'} 
                ]
            });
        }

        function initSharedHoldersTable() {

            if (tables.shared) return;
            tables.shared = $('.data-table-energy-shared').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('household-meter.index')); ?>",
                    data: function (d) {
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.type_filter = $('#filterByType').val();
                        d.date_filter = $('#filterByDateFrom').val();
                        d.system_type_filter = $('#filterByEnergySystemType').val();
                        d.meter_filter = $('#filterByMeterStatus').val();
                        d.cycle_filter = $('#filterByCycleYear').val();
                        d.year_filter = $('#filterByYear').val();
                    }
                },
                columns: [
                    {data: 'holder', name: 'holder'},
                    {data: 'user_name', name: 'user_name'},
                    {data: 'community_name', name: 'community_name'},
                    {data: 'action'},
                ]
            });
        }

        function initCometHoldersTable() {

            if (tables.comet) return;
            tables.comet = $('.data-table-energy-comet-meters').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('comet-meter.index')); ?>",
                    data: function (d) {
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.type_filter = $('#filterByType').val();
                        d.date_filter = $('#filterByDateFrom').val();
                        d.system_type_filter = $('#filterByEnergySystemType').val();
                        d.meter_filter = $('#filterByMeterStatus').val();
                        d.cycle_filter = $('#filterByCycleYear').val();
                        d.year_filter = $('#filterByYear').val();
                    }
                },
                columns: [
                    {data: 'public_name', name: 'public_name'},
                    {data: 'community_name', name: 'community_name'},
                    {data: 'meter_number', name: 'meter_number'},
                    {data: 'energy_name', name: 'energy_name'},
                    {data: 'energy_type_name', name: 'energy_type_name'},
                    {data: 'action'}
                ]
            });
        }

        function initRefrigeratorHoldersTable() {
            
            if (tables.refrigerator) return;
            tables.refrigerator = $('.data-table-refrigerators').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('refrigerator-user.index')); ?>",
                    data: function (d) {
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.type_filter = $('#filterByType').val();
                        d.date_filter = $('#filterByDateFrom').val();
                        d.system_type_filter = $('#filterByEnergySystemType').val();
                        d.meter_filter = $('#filterByMeterStatus').val();
                        d.cycle_filter = $('#filterByCycleYear').val();
                        d.year_filter = $('#filterByYear').val();
                    }
                },
                columns: [
                    {data: 'holder'},
                    {data: 'meter_number', name: 'meter_number'},
                    {data: 'community_name', name: 'community_name'},
                    {data: 'year', name: 'year'},
                    {data: 'refrigerator_type_id', name: 'refrigerator_type_id'},
                    {data: 'action'}
                ]
            });
        }

        function initDeactivatedHoldersTable() {

            if (tables.deactivated) return;
            tables.deactivated = $('.data-table-deactivated').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('deactivated-holder.index')); ?>",
                    data: function (d) {
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.type_filter = $('#filterByType').val();
                        d.date_filter = $('#filterByDateFrom').val();
                        d.system_type_filter = $('#filterByEnergySystemType').val();
                        d.meter_filter = $('#filterByMeterStatus').val();
                        d.cycle_filter = $('#filterByCycleYear').val();
                        d.year_filter = $('#filterByYear').val();
                    }
                },
                columns: [
                    {data: 'holder', name: 'holder'},
                    {data: 'meter_number', name: 'meter_number'},
                    {data: 'community_name', name: 'community_name'},
                    {data: 'visit_date', name: 'visit_date'},
                    {data: 'deactivated_after_war', name: 'deactivated_after_war'},
                   {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        }

        initAllEnergyHoldersTable();
        updateCountValue();

        // This function called after deletion
        function resetDataForAllTables() {

            $('#energyAllUsersTable').DataTable().draw();
            $('#allHouseholdMeterTable').DataTable().draw();
            $('#energyCometMeterTable').DataTable().draw();
            $('#refrigeratorTable').DataTable().draw();
            $('#deactivatedTable').DataTable().draw();
            updateCountValue();
        }

        // On tab shown, lazy-init the target table
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr('href');
            if (target == '#refrigerator-holders') initRefrigeratorHoldersTable();
            if (target == '#shared-holders') initSharedHoldersTable();
            if (target == '#comet-holders') initCometHoldersTable();
            if (target == '#energy-holders') initAllEnergyHoldersTable();
            if (target == '#deactivated-holders') initDeactivatedHoldersTable();

            // Show the requested filters only when Requested tab is active
            if (target == '#energy-holders') {

                $('#waterRequestedDate').show();
                $('#filterByHolderStatus').prop('disabled', false);
                $('#FilterByInstallationYear').prop('disabled', true);
            } else {

                $('#waterRequestedDate').hide();
                $('#filterByHolderStatus').prop('disabled', true);
                $('#FilterByInstallationYear').prop('disabled', false);
            }

            if ($('.selectpicker').length && typeof $('.selectpicker').selectpicker === 'function') {

                $('.selectpicker').selectpicker('refresh');
            }
        });

        // Reload initialized tables when any filter changes
        function reloadInitializedTables() {

            if (tables.comet) tables.comet.ajax.reload();
            if (tables.shared) tables.shared.ajax.reload();
            if (tables.allHolders) tables.allHolders.ajax.reload();
            if (tables.refrigerator) tables.refrigerator.ajax.reload();
            if (tables.deactivated) tables.deactivated.ajax.reload();
        }

        $('#filterByCommunity, #filterByRegion, #filterByType, #filterByEnergySystemTyp, #filterByMeterStatus, #filterByCycleYear, #filterByYear').on('change', function () {

            reloadInitializedTables();
            updateCountValue();
        });

        // Clear filters
        $(document).on('click', '#clearFiltersButton', function () {

            $('#filterByCommunity').prop('selectedIndex', 0);
            $('#filterByRegion').prop('selectedIndex', 0);
            $('#filterByType').prop('selectedIndex', 0);
            $('#filterByEnergySystemType').prop('selectedIndex', 0);
            $('#filterByMeterStatus').prop('selectedIndex', 0);
            $('#filterByCycleYear').prop('selectedIndex', 0);
            $('#filterByYear').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            $('#filterByDateFrom').val(' ');

            reloadInitializedTables();
            updateCountValue();
        });

        // View record edit page for energy holder
        $('#energyAllUsersTable').on('click', '.updateAllEnergyUser',function() {
            var id = $(this).data('id');
            var url = window.location.href; 
            url = url +'/'+ id +'/edit';
            // AJAX request
            $.ajax({
                url: 'allMeter/' + id + '/editpage',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    window.open(url); 
                }
            });
        });

        // View record details for energy holder
        $('#energyAllUsersTable').on('click', '.viewEnergyUser',function() {
            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'energy-user/' + id,
                type: 'get',
                dataType: 'json', 
                success: function(response) { 
                    $('#energyUserModalTitle').html(" ");
                    $('#energyUserModalTitle').html(response['household'].english_name);
                    $('#englishNameUser').html(" ");
                    $('#englishNameUser').html(response['household'].english_name);
                    $('#communityUser').html(" ");
                    $('#communityUser').html(response['community'].english_name);
                    $('#meterActiveUser').html(" ");
                    $('#meterActiveUser').html(response['energy'].meter_active);
                    $('#meterCaseUser').html(" ");
                    $('#meterCaseUser').html(response['meter'].meter_case_name_english);
                    $('#systemNameUser').html(" ");
                    $('#systemNameUser').html(response['system'].name);
                    $('#systemTypeUser').html(" ");
                    $('#systemTypeUser').html(response['type'].name);
                    $('#systemLimitUser').html(" ");
                    $('#systemLimitUser').html(response['energy'].daily_limit);
                    $('#systemDateUser').html(" ");
                    $('#systemDateUser').html(response['energy'].installation_date);
                    $('#systemNotesUser').html(" ");
                    if(response['energy']) $('#systemNotesUser').html(response['energy'].notes);
                    $('#vendorDateUser').html(" ");
                    if(response['vendor']) $('#vendorDateUser').html(response['vendor'].name);
                    
                    $('#systemGroundUser').html(" ");
                    $('#systemGroundUser').html(response['energy'].ground_connected);
                    if(response['energy'].ground_connected == "Yes") {

                        $('#systemGroundUser').css('color', 'green');
                    } else if(response['energy'].ground_connected == "No") {

                        $('#systemGroundUser').css('color', 'red');
                    }

                    if(response['energyCycleYear'] != []) {

                        $('#energyCycleYear').html(" ");
                        $('#energyCycleYear').html(response['energyCycleYear'].name);
                    }

                    $('#installationTypeUser').html(" ");
                    if(response['installationType']) $('#installationTypeUser').html(response['installationType'].type);

                    $('#donorsDetails').html(" ");
                    if(response['energyMeterDonors'] != []) {
                        for (var i = 0; i < response['energyMeterDonors'].length; i++) {
                            if(response['energyMeterDonors'][i].donor_name == "0")  {
                                response['energyMeterDonors'][i].donor_name = "Not yet attributed";
                            }
                            $("#donorsDetails").append(
                            '<ul><li>'+ response['energyMeterDonors'][i].donor_name +'</li></ul>');
                               
                        }
                    }

                    $('#donorsNewDetails').html(" ");
                    if(response['energyMeterNewDonors'] != []) {
                        for (var i = 0; i < response['energyMeterNewDonors'].length; i++) {
                            if(response['energyMeterNewDonors'][i].donor_name == "0")  {
                                response['energyMeterNewDonors'][i].donor_name = "Not yet attributed";
                            }
                            $("#donorsNewDetails").append(
                            '<ul><li>'+ response['energyMeterNewDonors'][i].donor_name +'</li></ul>');
                               
                        }
                    }

                    $('#sharedHousehold').empty();
                    if (response.householdMeters && response.householdMeters.length > 0) {

                        let html = '<ul>';
                        for (let i = 0; i < response.householdMeters.length; i++) {
                            html += '<li>' + response.householdMeters[i].english_name + '</li>';
                        }
                        html += '</ul>';
                        $('#sharedHousehold').html(html);
                    }

 
                    $('#incidentUser').html(" ");
                    $('#incidentDate').html(" ");
                    if(response['fbsIncident'] != []) {
                        for (var i = 0; i < response['fbsIncident'].length; i++) {
                            $('#incidentUser').html(response['fbsIncident'][i].english_name);
                            $('#incidentDate').html(response['fbsIncident'][i].incident_date);
                        }
                    }
                    if(response['mgIncident'] != []) {
                        for (var i = 0; i < response['mgIncident'].length; i++) {
                            $('#incidentUser').html(response['mgIncident'][i].english_name);
                            $('#incidentDate').html(response['mgIncident'][i].incident_date);
                        }
                    }

                }
            });
        }); 

        // delete energy user
        $('#energyAllUsersTable').on('click', '.deleteAllEnergyUser',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this user?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteEnergyUser')); ?>",
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
                                    resetDataForAllTables();
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

        // View record details for shared holder
        $('#allHouseholdMeterTable').on('click', '.viewHouseholdMeterUser',function() {
            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'household-meter/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) { 

                    $('#energySharedUserModalTitle').html(" ");
                    $('#englishNameSharedUser').html(" ");

                    if(response['sharedUser'] != null) {

                        $('#energySharedUserModalTitle').html(response['sharedUser'].english_name);
                        $('#englishNameSharedUser').html(response['sharedUser'].english_name);
                    } 

                    if(response['sharedPublic'] != null) {

                        $('#energySharedUserModalTitle').html(response['sharedPublic'].english_name);
                        $('#englishNameSharedUser').html(response['sharedPublic'].english_name);
                    }
                    
                    $('#englishNameMainUser').html(" ");
                    $('#englishNameMainUser').html(response['user'].english_name);

                    $('#communityName').html(" ");
                    $('#communityName').html(response['community'].english_name);

                    $('#meterCaseSharedUser').html(" ");
                    $('#meterCaseSharedUser').html(response['meter'].meter_case_name_english);
                    $('#systemNameSharedUser').html(" ");
                    $('#systemNameSharedUser').html(response['system'].name);
                    $('#systemTypeSharedUser').html(" ");
                    $('#systemTypeSharedUser').html(response['type'].name);
                    $('#systemLimitSharedUser').html(" ");
                    $('#systemLimitSharedUser').html(response['mainUser'].daily_limit);
                    $('#systemDateSharedUser').html(" ");
                    $('#systemDateSharedUser').html(response['mainUser'].installation_date);
                    $('#systemNotesSharedUser').html(" ");
                    if(response['mainUser']) $('#systemNotesSharedUser').html(response['mainUser'].notes);
                    $('#vendorDateUser').html(" ");
                    if(response['vendor']) $('#vendorDateSharedUser').html(response['vendor'].name);
                    
                    $('#systemGroundSharedUser').html(" ");
                    $('#systemGroundSharedUser').html(response['mainUser'].ground_connected);
                    if(response['mainUser'].ground_connected == "Yes") {

                        $('#systemGroundSharedUser').css('color', 'green');
                    } else if(response['mainUser'].ground_connected == "No") {

                        $('#systemGroundSharedUser').css('color', 'red');
                    }

                    $('#installationTypeSharedUser').html(" ");
                    if(response['installationType']) $('#installationTypeSharedUser').html(response['installationType'].type);

                }
            });
        }); 

        // Delete record for the shared holder
        $('#allHouseholdMeterTable').on('click', '.deleteAllHouseholdMeterUser',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this household meter?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                
                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteHouseholdMeter')); ?>",
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
                                    resetDataForAllTables();
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

        // Delete record for comet meter
        $('#energyCometMeterTable').on('click', '.deleteEnergyComet',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this comet meter?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteCometMeter')); ?>",
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
                                    resetDataForAllTables();
                                });
                            } else {

                                alert("Invalid ID.");
                            }
                        }
                    });
                } else if(result.isDenied) {

                    Swal.fire('Changes are not saved', '', 'info')
                }
            });
        });

        // View record update page for comet meter
        $('#energyCometMeterTable').on('click', '.updateEnergyComet',function() {

            var id = $(this).data('id');
            var url = '/comet-meter/' + id + '/edit';
     
            // AJAX request
            $.ajax({
                url: 'comet-meter/' + id + '/editpage',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    window.open(url, "_blank"); 
                }
            });
        });

        // View record details for comet meter
        $('#energyCometMeterTable').on('click', '.viewCometMeterUser',function() {
            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'energy-public/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    $('#energyCometModalTitle').html(" ");
                    $('#englishNameComet').html(" ");
                    $('#communityComet').html(" ");
                    $('#meterActiveComet').html(" ");
                    $('#meterCaseComet').html(" ");
                    $('#systemNameComet').html(" ");
                    $('#systemTypeComet').html(" ");
                    $('#systemLimitComet').html(" ");
                    $('#systemDateComet').html(" ");
                    $('#systemNotesComet').html(" ");

                    $('#energyCometModalTitle').html(response['public'].english_name);
                    $('#englishNameComet').html(response['public'].english_name);
                    $('#communityComet').html(response['community'].english_name);
                    $('#meterActiveComet').html(response['energyPublic'].meter_active);
                    $('#meterCaseComet').html(response['meter'].meter_case_name_english);
                    $('#systemNameComet').html(response['system'].name);
                    $('#systemTypeComet').html(response['type'].name);
                    $('#systemLimitComet').html(response['energyPublic'].daily_limit);
                    $('#systemDateComet').html(response['energyPublic'].installation_date);
                    $('#systemNotesComet').html(response['energyPublic'].notes);
                    if(response['vendor']) $('#vendorDatePublic').html(response['vendor'].name);
                    $('#installationTypeComet').html(" ");
                    if(response['installationType']) $('#installationTypePublic').html(response['installationType'].type);

                    $('#donorsDetails').html(" ");
                    if(response['energyMeterDonors'] != []) {
                        for (var i = 0; i < response['energyMeterDonors'].length; i++) {
                            if(response['energyMeterDonors'][i].donor_name == "0")  {
                                response['energyMeterDonors'][i].donor_name = "Not yet attributed";
                            }
                            $("#donorsDetails").append(
                            '<ul><li>'+ response['energyMeterDonors'][i].donor_name +'</li></ul>');  
                        }
                    }
                }
            });
        });

        // Delete record for Refrigerator
        $('#refrigeratorTable').on('click', '.deleteRefrigeratorHolder', function() {

            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this refrigerator holder?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteRefrigeratorHolder')); ?>",
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
                                    resetDataForAllTables();
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

        // View update for Refrigerator
        $('#refrigeratorTable').on('click', '.updateRefrigeratorHolder',function() {

            var id = $(this).data('id');
            var url = '/refrigerator-user/' + id + '/edit';
    
            window.open(url, "_blank"); 
        });

        // View record details for Refrigerator
        $('#refrigeratorTable').on('click', '.viewRefrigeratorHolder',function() {
            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'refrigerator-user/' + id,
                type: 'get',
                dataType: 'json', 
                success: function(response) {

                    $('#refrigeratorHolderModalTitle').html(" ");
                    $('#communityRefrigeratorHolder').html(" ");
                    $('#communityRefrigeratorHolder').html(response['community'].english_name);
                    $('#phoneNumberUser').html(" ");
                    
                    if(response['household'] != null) {

                        $('#refrigeratorHolderModalTitle').html(response['household'].english_name);
                        $('#englishNameRefrigeratorHolder').html(" ");
                        $('#englishNameRefrigeratorHolder').html(response['household'].english_name);
                        $('#phoneNumberUser').html(" ");
                        $('#phoneNumberUser').html(response['household'].phone_number);

                    } else if(response['public'] != null) {

                        $('#refrigeratorHolderModalTitle').html(response['public'].english_name);
                        $('#englishNameRefrigeratorHolder').html(" ");
                        $('#englishNameRefrigeratorHolder').html(response['public'].english_name);
                    }

                    $('#refrigeratorDate').html(" ");
                    $('#refrigeratorDate').html(response['refrigerator'].date);
                    $('#refrigeratorYear').html(" ");
                    $('#refrigeratorYear').html(response['refrigerator'].year);
                    $('#status').html(" ");
                    $('#status').html(response['refrigerator'].status);
                    $('#refrigeratorTypeDeNo').html(" ");
                    $('#refrigeratorTypeDeNo').html(response['refrigerator'].refrigerator_type_id);
                    $('#refrigeratorIsPaid').html(" ");
                    $('#refrigeratorIsPaid').html(response['refrigerator'].is_paid);
                    $('#refrigeratorPayment').html(" ");
                    $('#refrigeratorPayment').html(response['refrigerator'].payment);
                    $('#refrigeratorNote').html(" ");
                    $('#refrigeratorNote').html(response['refrigerator'].notes);
                    $('#refrigeratorReceiveNumber').html(" ");
                    if(response['refrigeratorHolderNumber']) {

                        $('#refrigeratorReceiveNumber').html(response['refrigeratorHolderNumber'][0].receive_number);
                    }
                }
            });
        });

        // Delete record for Reactivated
        $('#deactivatedTable').on('click', '.deleteDeactivatedEnergyHolder', function() {

            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this Reactivated holder?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteReactivatedHolder')); ?>",
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
                                    resetDataForAllTables();
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

        // View update for Reactivated
        $('#deactivatedTable').on('click', '.updateDeactivatedEnergyHolder',function() {

            var id = $(this).data('id');
            var url = '/deactivated-holder/' + id + '/edit';
    
            window.open(url, "_blank"); 
        });

        // View record details for Reactivated
        $('#deactivatedTable').on('click', '.viewDeactivatedEnergyHolder',function() {

            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'deactivated-holder/' + id,
                type: 'get',
                dataType: 'json', 
                success: function(response) {

                    $('#reactivatedHolderCommunity').html(" ");
                    $('#reactivatedHolderCommunity').html(response['community'].english_name);
                    $('#reactivatedHolderModalTitle').html(" ");
                    $('#reactivatedHolderModalTitle').html(response['energyHolder'].english_name);
                    $('#reactivatedHolder').html(" ");
                    $('#reactivatedHolder').html(response['energyHolder'].english_name);

                    $('#reactivatedHolderSystem').html(" ");
                    $('#reactivatedHolderSystem').html(response['system'].name);
                    $('#reactivatedHolderSystemType').html(" ");
                    $('#reactivatedHolderSystemType').html(response['systemType'].name);
                    $('#reactivatedHolderMeterNumber').html(" ");
                    $('#reactivatedHolderMeterNumber').html(response['energyMeter'].meter_number);
                    $('#reactivatedHolderDailyLimit').html(" ");
                    $('#reactivatedHolderDailyLimit').html(response['energyMeter'].daily_limit);
                    $('#reactivatedHolderDate').html(" ");
                    $('#reactivatedHolderDate').html(response['energyMeter'].installation_date);

                    $('#reactivatedHolderVisitDate').html(" ");
                    $('#reactivatedHolderVisitDate').html(response['reactivatedHolder'].visit_date);
                    $('#reactivatedHolderByUser').html(" ");
                    $('#reactivatedHolderByUser').html(response['user'].name);
                    $('#reactivatedHolderAfterWar').html(" ");
                    $('#reactivatedHolderAfterWar').html(response['reactivatedHolder'].deactivated_after_war);
                    $('#reactivatedHolderPaid').html(" ");
                    $('#reactivatedHolderPaid').html(response['reactivatedHolder'].is_paid);
                    $('#reactivatedHolderPaidAmount').html(" ");
                    $('#reactivatedHolderPaidAmount').html(response['reactivatedHolder'].paid_amount);
                    $('#reactivatedHolderIsReturn').html(" ");
                    $('#reactivatedHolderIsReturn').html(response['reactivatedHolder'].is_return);
                    $('#reactivatedHolderReactivationDate').html(" ");
                    $('#reactivatedHolderReactivationDate').html(response['reactivatedHolder'].reactivation_date);
                    $('#reactivatedHolderSystemStatus').html(" ");
                    $('#reactivatedHolderSystemStatus').html(response['reactivatedHolder'].system_status);
                    $('#reactivatedHolderNotes').html(" ");
                    $('#reactivatedHolderNotes').html(response['reactivatedHolder'].notes);
                }
            });
        });
    });

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/users/energy/index.blade.php ENDPATH**/ ?>