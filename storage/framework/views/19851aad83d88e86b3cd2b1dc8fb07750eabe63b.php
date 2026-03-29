
<style>
    .menu-sub .menu-sub {
        margin-left: 20px; /* Adjust the value as needed */
    }
</style>
<ul class="menu-inner py-1" id="menu">

    <li class="menu-item" id="home" data-route="home">
        <a href="<?php echo e(url('home')); ?>" class="dashboard menu-link" >
            <i class="menu-icon tf-icons bx bx-tachometer"></i>
            <div>Dashboards</div>
        </a>
    </li>

    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
        Auth::guard('user')->user()->user_type_id == 2): ?>
        <li class="menu-item" id="work-plans" data-route="work-plans">
            <a href="<?php echo e(url('work-plan')); ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-list-check"></i>
                <div>Action Items</div>
            </a>
        </li>
    <?php endif; ?>
    
    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
        Auth::guard('user')->user()->user_type_id == 2): ?>
        <li class="menu-item" id="data-collection" data-route="data-collection">
            <a href="<?php echo e(url('data-collection')); ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-data"></i>
                <div>Data Collection</div>
            </a>
        </li>
    <?php endif; ?>

    <li class="menu-item" id="action-items" data-route="action-items">
        <a href="<?php echo e(url('action-item')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-task"></i>
            <div>Project Plans</div>
        </a>
    </li>

    <li class="menu-item" id="all-active" data-route="all-active">
        <a href="<?php echo e(url('all-active')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-show"></i>
            <div>Overview of Active Users</div>
        </a>
    </li>

    <li class="menu-item" id="communities" data-route="communities">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-home"></i>
            <div>Communities</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="all-community" data-route="community">
                <a href="<?php echo e(url('community')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>All</div>
                </a>
            </li>
            <li class="menu-item" id="served-community" data-route="served-community">
                <a href="<?php echo e(url('served-community')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Served</div>
                </a>
            </li>
            <li class="menu-item" id="in_progress_communities" data-route="in_progress_communities">
                <a class="menu-link menu-toggle">
                    <div>In Progress</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="initial-community" data-route="initial-community">
                        <a href="<?php echo e(url('initial-community')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Initial Survey</div>
                        </a>
                    </li>
                    <li class="menu-item" id="ac-community" data-route="ac-community">
                        <a href="<?php echo e(url('ac-community')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>AC in Progress</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item" id="representative" data-route="representative">
                <a href="<?php echo e(url('representative')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Community Representatives</div>
                </a>
            </li>
            <li class="menu-item" id="sub-community-household" data-route="sub-community-household">
                <a href="<?php echo e(url('sub-community-household')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Sub Communities</div>
                </a>
            </li>
            <li class="menu-item" id="community-compound" data-route="compound">
                <a href="<?php echo e(url('compound')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Community Compounds</div>
                </a>
            </li>
        </ul>
    </li>
    <li class="menu-item" id="households" data-route="households">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div>Households</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="household" data-route="household">
                <a href="<?php echo e(url('household')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>All</div>
                </a>
            </li>
            <!-- <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                Auth::guard('user')->user()->user_type_id == 2 || 
                Auth::guard('user')->user()->user_type_id == 3 || 
                Auth::guard('user')->user()->user_type_id == 4): ?>
                <li class="menu-item" id="requested-household">
                    <a href="<?php echo e(url('requested-household')); ?>" class="menu-link" >
                        <i class=""></i>
                        <div>Requested System/Meter</div>
                    </a>
                </li>
            <?php endif; ?> -->
            <li class="menu-item" id="in_progress_households" data-route="in-progress-households">
                <a href="<?php echo e(url('in-progress-households')); ?>" class="menu-link">
                    <i class=""></i>
                    <div>In Progress</div>
                </a>
                
            </li>
            <li class="menu-item" id="served-household" data-route="served-household">
                <a href="<?php echo e(url('served-household')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Served</div>
                </a>
            </li>
            <li class="menu-item" id="displaced-household" data-route="displaced-household">
                <a href="<?php echo e(url('displaced-household')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Displaced</div>
                </a>
            </li>
        </ul>
    </li>
   
    <li class="menu-item" id="public-structure" data-route="public-structure">
        <a href="<?php echo e(url('public-structure')); ?>" class="dashboard menu-link">
            <i class="menu-icon tf-icons bx bx-buildings"></i>
            <div>Public Structures</div>
        </a>
    </li>

    <li class="menu-item" id="other-holder" data-route="other-holder">
        <a href="<?php echo e(url('other-holder')); ?>" class="dashboard menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div>Other Holders</div>
        </a>
    </li>

    <li class="menu-item" id="requested-tab" data-route="requested-tab">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-hive"></i>
            <div>Requested Services</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="camera-request" data-route="camera-request">
                <a href="<?php echo e(url('camera-request')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Requested Camera</div>
                </a>
            </li> 
        </ul>
    </li>
 
    <li class="menu-item" id="services" data-route="services">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-tachometer"></i>
            <div>Services</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="energy-service" data-route="">
                <a class="menu-link menu-toggle">
                    <div>Energy </div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="all-meter" data-route="all-meter">
                        <a href="<?php echo e(url('all-meter')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Energy Holders</div>
                        </a>
                    </li>
                    <li class="menu-item" id="vendor" data-route="vendor">
                        <a href="<?php echo e(url('vending-point')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Vending Points</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item" id="water-service" data-route="water-service">
                <a class="menu-link menu-toggle">
                    <div>Water </div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="all-water" data-route="all-water">
                        <a href="<?php echo e(url('all-water')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Holders</div>
                        </a>
                    </li>
                    <li class="menu-item" id="shared-h2o" data-route="shared-h2o">
                        <a href="<?php echo e(url('shared-h2o')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Shared H2O Users</div>
                        </a>
                    </li>
                    <li class="menu-item" id="water-public" data-route="water-public">
                        <a href="<?php echo e(url('water-public')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Shared H2O Public Structures</div>
                        </a>
                    </li>
                    <li class="menu-item" id="shared-grid" data-route="shared-grid">
                        <a href="<?php echo e(url('shared-grid')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Shared Grid Holders</div>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="menu-item" id="internet-service" data-route="internet-service">
                <a class="menu-link menu-toggle">
                <!--  <i class="menu-icon tf-icons bx bx-wifi"></i>-->
                    <div>Internet </div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="internet-user" data-route="internet-user">
                        <a href="<?php echo e(url('internet-user')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Contract Holders</div>
                        </a>
                    </li>
                </ul>
            </li> 

            <li class="menu-item" id="agriculture-service" data-route="agriculture-service">
                <a class="menu-link menu-toggle">
                    <div>Agriculture</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="agriculture-user" data-route="argiculture-user">
                        <a href="<?php echo e(url('argiculture-user')); ?>" class="menu-link">
                            <i class=""></i>
                            <div>Agriculture Users</div>
                        </a>
                    </li>
                </ul>
            </li>

           <li class="menu-item" data-route="camera-service">
                <a class="menu-link menu-toggle">
                    <!--  <i class="menu-icon tf-icons bx bx-wifi"></i>-->
                    <div>Cameras </div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" data-route="all-cameras">
                        <a href="<?php echo e(url('all-cameras')); ?>" class="menu-link">
                            <i class=""></i>
                            <div>All Cameras</div>
                        </a>
                    </li>
                </ul>
                <?php if(
                        Auth::guard('user')->user()->user_type_id == 1 ||
                        Auth::guard('user')->user()->user_type_id == 6 ||
                        Auth::guard('user')->user()->user_type_id == 10
                    ): ?>
                    <ul class="menu-sub">
                        <li class="menu-item" data-route="camera-component">
                            <a href="<?php echo e(url('camera-component')); ?>" class="menu-link">
                                <i class=""></i>
                                <div>Camera Components</div>
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>
        </ul>
    </li>
 
    <li class="menu-item" id="meter-history" data-route="meter-history">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-history"></i>
            <div>Meter History</div>
        </a>
        <ul class="menu-sub">
            
            <li class="menu-item" id="all-meter-histories" data-route="all-meter-histories">
                <a href="<?php echo e(route('meter-history.all')); ?>" class="menu-link">
                    <i class=""></i>
                    <div>All Meter Histories</div>
                </a>
            </li>
            <li class="menu-item" id="meter-history-component" data-route="meter-history-component">
                <a href="<?php echo e(url('meter-history-component')); ?>" class="menu-link">
                    <i class=""></i>
                    <div>Add Meter History Component</div>
                </a>
            </li>

        </ul>
    </li>

    <li class="menu-item" id="maintenance" data-route="maintenance">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-extension"></i>
            <div>Maintenance and Monitoring</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="all-maintenance" data-route="all-maintenance">
                <a href="<?php echo e(url('all-maintenance')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>All Maintenance</div>
                </a>
            </li>
            <li class="menu-item" id="energy-maintenance-tab" data-route="energy-maintenance-tab">
                <a class="menu-link menu-toggle">
                    <div>Electricity Maintenance</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="energy-maintenance" data-route="energy-maintenance">
                        <a href="<?php echo e(url('energy-maintenance')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Maintenance</div>
                        </a>
                    </li>
                    <li class="menu-item" id="energy-issue" data-route="energy-issue">
                        <a href="<?php echo e(url('energy-issue')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Issues</div>
                        </a>
                    </li>  
                    <li class="menu-item" id="energy-action" data-route="energy-action">
                        <a href="<?php echo e(url('energy-action')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Actions</div>
                        </a>
                    </li>
                    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 4): ?>
                        <li class="menu-item" id="energy-generator-turbine" data-route="energy-generator-turbine">
                            <a href="<?php echo e(url('energy-generator-turbine')); ?>" class="menu-link" >
                                <i class=""></i>
                                <div>Generators/Turbines</div>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </li>
            <li class="menu-item" id="refrigerator-maintenance-tab" data-route="refrigerator-maintenance-tab">
                <a class="menu-link menu-toggle">
                    <div>Refrigerator Maintenance</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="refrigerator-maintenance" data-route="refrigerator-maintenance">
                        <a href="<?php echo e(url('refrigerator-maintenance')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Maintenance</div>
                        </a>
                    </li>
                    <li class="menu-item" id="refrigerator-issue" data-route="refrigerator-issue">
                        <a href="<?php echo e(url('refrigerator-issue')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Issues</div>
                        </a>
                    </li> 
                    <li class="menu-item" id="refrigerator-action" data-route="refrigerator-action">
                        <a href="<?php echo e(url('refrigerator-action')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Actions</div>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="menu-item" id="water-maintenance-tab" data-route="water-maintenance-tab">
                <a class="menu-link menu-toggle">
                    <div>Water Maintenance</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="water-maintenance" data-route="water-maintenance">
                        <a href="<?php echo e(url('water-maintenance')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Maintenance</div>
                        </a>
                    </li>
                    <li class="menu-item" id="water-issue" data-route="water-issue">
                        <a href="<?php echo e(url('water-issue')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Issues</div>
                        </a>
                    </li>
                    <li class="menu-item" id="water-action" data-route="water-action">
                        <a href="<?php echo e(url('water-action')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Actions</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item" id="internet-maintenance-tab" data-route="internet-maintenance-tab">
                <a class="menu-link menu-toggle">
                    <div>Internet Maintenance</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="internet-maintenance" data-route="internet-maintenance">
                        <a href="<?php echo e(url('internet-maintenance')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Maintenance</div>
                        </a>
                    </li>
                    <li class="menu-item" id="internet-issue" data-route="internet-issue">
                        <a href="<?php echo e(url('internet-issue')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Issues</div>
                        </a>
                    </li> 
                    <li class="menu-item" id="internet-action" data-route="internet-action">
                        <a href="<?php echo e(url('internet-action')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Actions</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item" id="agriculture-maintenance-tab" data-route="agriculture-maintenance-tab">
                <a class="menu-link menu-toggle">
                    <div>Agriculture Maintenance</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="agriculture-maintenance" data-route="agriculture-maintenance">
                        <a href="<?php echo e(url('agriculture-maintenance')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Actions/Issues</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item" id="energy-safety" data-route="energy-safety">
                <a href="<?php echo e(url('energy-safety')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Meters Safety Check</div>
                </a>
            </li>
            <li class="menu-item" id="results" data-route="results">
                <a class="menu-link menu-toggle">
                    <!-- <i class="menu-icon tf-icons bx bx-receipt"></i> -->
                    <div>Water Quality Results</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="water-summary" data-route="water-summary">
                        <a href="<?php echo e(url('water-summary')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Summary</div>
                        </a>
                    </li>
                    <li class="menu-item" id="quality-result" data-route="quality-result">
                        <a href="<?php echo e(url('quality-result')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Results</div>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>
    <li class="menu-item" id="systems" data-route="systems">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-data"></i>
            <div>Systems</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="energy" data-route="energy">
                <a class="menu-link menu-toggle">
                    <div>Energy</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="energy-system" data-route="energy-system">
                        <a href="<?php echo e(url('energy-system')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Energy System</div>
                        </a>
                    </li>
                <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                Auth::guard('user')->user()->user_type_id == 2): ?>
                    <li class="menu-item" id="energy-cost" data-route="energy-cost">
                        <a href="<?php echo e(url('energy-cost')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Energy Cost</div>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::guard('user')->user()->user_type_id == 1): ?>
                    <li class="menu-item" id="donor-cost" data-route="donor-cost">
                        <a href="<?php echo e(url('donor-cost')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Donor Fund</div>
                        </a>
                    </li>
                <?php endif; ?>
                </ul>
            </li>
            <li class="menu-item" id="water-system-tab" data-route="water-system-tab">
                <a class="menu-link menu-toggle">
                    <i class=""></i>
                    <div>Water System</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="water-system" data-route="water-system">
                        <a href="<?php echo e(url('water-system')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All water systems</div>
                        </a> 
                    </li>
                    <li class="menu-item" id="water-log" data-route="water-log">
                        <a href="<?php echo e(url('water-log')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>Water logframe</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item" id="internet-system-tab" data-route="internet-system-tab">
                <a class="menu-link menu-toggle">
                    <div>Internet System</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="internet-system" data-route="internet-system">
                        <a href="<?php echo e(url('internet-system')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Internet Systems</div>
                        </a>
                    </li>
                    <!-- <li class="menu-item" id="internet-cluster">
                        <a href="<?php echo e(url('internet-cluster')); ?>" class="menu-link" >
                            <i class=""></i>
                            <div>All Internet Clusters</div>
                        </a>
                    </li> -->
                </ul>
            </li> 

            <li class="menu-item" id="agriculture-system-tab" data-route="agriculture-system-tab">   
                <a class="menu-link menu-toggle">
                    <div>Agriculture System</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item" id="agriculture-system" data-route="agriculture-system">
                        <a href="<?php echo e(url('agriculture-system')); ?>" class="menu-link">
                            <i class=""></i>
                            <div>All Agriculture Systems</div>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </li>

    
    <li class="menu-item" id="incidents" data-route="">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-error-alt"></i>
            <div>Incidents</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="all-incident" data-route="all-incident">
                <a href="<?php echo e(url('all-incident')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>All Incidents</div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item" id="regions" data-route="regions">
        <a class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-map"></i>
            <div>Regions</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item" id="region" data-route="region">
                <a href="<?php echo e(url('region')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>All Regions</div>
                </a>
            </li>
            <li class="menu-item" id="sub-region" data-route="sub-region">
                <a href="<?php echo e(url('sub-region')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Sub Regions</div>
                </a>
            </li>
            <li class="menu-item" id="sub-sub-region" data-route="sub-sub-region">
                <a href="<?php echo e(url('sub-sub-region')); ?>" class="menu-link" >
                    <i class=""></i>
                    <div>Sub Sub Regions</div>
                </a>
            </li>
        </ul>
    </li>
    
    <li class="menu-item" id="workshop" data-route="workshop">
        <a href="<?php echo e(url('all-workshop')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book-alt"></i>
            <div>Workshops</div>
        </a>
    </li>

    <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
        Auth::guard('user')->user()->user_type_id == 2): ?>
    <li class="menu-item" id="donor" data-route="donor">
        <a href="<?php echo e(url('donor')); ?>" class="menu-link" >
            <i class="menu-icon tf-icons bx bx-money"></i>
            <div>Donors</div>
        </a>
    </li>
    <!-- <li class="menu-item" id="chart">
        <a href="<?php echo e(url('chart')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-chart"></i>
            <div>Charts</div>
        </a>
    </li> -->
    <li class="menu-item" id="user" data-route="user">
        <a href="<?php echo e(url('user')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div>Users</div>
        </a>
    </li>
    <li class="menu-item" id="setting" data-route="setting">
        <a href="<?php echo e(url('setting')); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div>Settings</div>
        </a>
    </li>
    <?php endif; ?>
</ul>


<script>
(function waitForjQuery(){
    function init($){
        const currentUrl = new URL(window.location.href);
        const pathSegments = currentUrl.pathname.split('/').filter(Boolean);
        const lastPart = pathSegments[pathSegments.length - 1] || '';

        const routes = {
        "home":                     { active: ["home"] },
        "all-active":               { active: ["all-active"] },
        "work-plan":                { active: ["work-plans"] },
        "action-item":              { active: ["action-items"] },
        "data-collection":          { active: ["data-collection"] },

        // Regions
        "region":                   { active: ["region"],           open: ["regions"] },
        "sub-region":               { active: ["sub-region"],       open: ["regions"] },
        "sub-sub-region":           { active: ["sub-sub-region"],   open: ["regions"] },
        "towns":                    { active: ["towns"],            open: ["regions"] },

        // Communities
        "in-progress-communities":  { active: ["all-community"],    open: ["communities"] },
        "all-community":            { active: ["all-community"],    open: ["communities"] },
        "initial-community":        { active: ["initial-community", "all-community"], open: ["communities"] },
        "ac-community":             { active: ["ac-community",      "all-community"], open: ["communities"] },
        "representative":           { active: ["representative"],   open: ["communities"] },
        "sub-community-household":  { active: ["sub-community-household"], open: ["communities"] },
        "community-compound":       { active: ["community-compound"], open: ["communities"] },
        "served-community":         { active: ["served-community"], open: ["communities"] },

        // Public Structures
        "public-structure":         { active: ["public-structure"] },

        // Other holder
        "other-holder":         { active: ["other-holder"] },

        // Requested Services
        "energy-request":           { active: ["energy-request"],   open: ["requested-tab"] },
        "water-request":            { active: ["water-request"],    open: ["requested-tab"] },
        "camera-request":           { active: ["camera-request"],   open: ["requested-tab"] },

        // Energy Services
        "all-meter":                { active: ["all-meter"],        open: ["energy-service", "services"] },
        "household-meter":          { active: ["household-meter"],  open: ["energy-service", "services"] },
        "energy-public":            { active: ["energy-public"],    open: ["energy-service", "services"] },
        "comet-meter":              { active: ["comet-meter"],      open: ["energy-service", "services"] },
        "refrigerator-user":        { active: ["refrigerator-user"], open: ["energy-service", "services"] },
        "vendor":                   { active: ["vendor"],           open: ["energy-service", "services"] },
        "vending-point":            { active: ["vendor"],           open: ["energy-service", "services"] },

        // Water Services
        "all-water":                { active: ["all-water"],        open: ["water-service", "services"] },
        "shared-h2o":               { active: ["shared-h2o"],       open: ["water-service", "services"] },
        "water-public":             { active: ["water-public"],     open: ["water-service", "services"] },
        "shared-grid":              { active: ["shared-grid"],      open: ["water-service", "services"] },

        // Internet Services
        "internet-user":            { active: ["internet-user"],    open: ["internet-service", "services"] },

        // Agriculture Services
        "agriculture-user":         { active: ["agriculture-user"], open: ["agriculture-service", "services"] },

        // Camera Services
        "all-cameras":              { active: ["all-cameras"],      open: ["camera-service", "services"] },
        "camera-component":         { active: ["camera-component"], open: ["camera-service", "services"] },

        // Households
        "household":                { active: ["household"],        open: ["households"] },
        "in-progress-households":   { active: ["in-progress-households"], open: ["households"] },
        "served-household":         { active: ["served-household"], open: ["households"] },
        "displaced-household":      { active: ["displaced-household"], open: ["households"] },

        // Meter History
        "all-meter-histories":          { active: ["all-meter-histories"],      open: ["meter-history"] },
        "meter-history-component":      { active: ["meter-history-component"],  open: ["meter-history"] },
        // handles /meter-history/component shape
        "component":                    { active: ["meter-history-component"],  open: ["meter-history"] },

        // Maintenance
        "all-maintenance":          { active: ["all-maintenance"],  open: ["maintenance"] },
        "energy-maintenance":       { active: ["energy-maintenance"], open: ["energy-maintenance-tab", "maintenance"] },
        "energy-issue":             { active: ["energy-issue"],     open: ["energy-maintenance-tab", "maintenance"] },
        "energy-action":            { active: ["energy-action"],    open: ["energy-maintenance-tab", "maintenance"] },
        "energy-generator-turbine": { active: ["energy-generator-turbine"], open: ["energy-maintenance-tab", "maintenance"] },
        "refrigerator-maintenance": { active: ["refrigerator-maintenance"], open: ["refrigerator-maintenance-tab", "maintenance"] },
        "refrigerator-issue":       { active: ["refrigerator-issue"], open: ["refrigerator-maintenance-tab", "maintenance"] },
        "refrigerator-action":      { active: ["refrigerator-action"], open: ["refrigerator-maintenance-tab", "maintenance"] },
        "water-maintenance":        { active: ["water-maintenance"], open: ["water-maintenance-tab", "maintenance"] },
        "water-issue":              { active: ["water-issue"],      open: ["water-maintenance-tab", "maintenance"] },
        "water-action":             { active: ["water-action"],     open: ["water-maintenance-tab", "maintenance"] },
        "internet-maintenance":     { active: ["internet-maintenance"], open: ["internet-maintenance-tab", "maintenance"] },
        "internet-issue":           { active: ["internet-issue"],   open: ["internet-maintenance-tab", "maintenance"] },
        "internet-action":          { active: ["internet-action"],  open: ["internet-maintenance-tab", "maintenance"] },
        "energy-safety":            { active: ["energy-safety"],    open: ["maintenance"] },
        "water-summary":            { active: ["water-summary"],    open: ["results", "maintenance"] },
        "quality-result":           { active: ["quality-result"],   open: ["results", "maintenance"] },

        // Systems
        "energy-system":            { active: ["energy-system"],    open: ["energy", "systems"] },
        "energy-cost":              { active: ["energy-cost"],      open: ["energy", "systems"] },
        "donor-cost":               { active: ["donor-cost"],       open: ["energy", "systems"] },
        "water-system":             { active: ["water-system"],     open: ["water-system-tab", "systems"] },
        "water-log":                { active: ["water-log"],        open: ["water-system-tab", "systems"] },
        "internet-system":          { active: ["internet-system"],  open: ["internet-system-tab", "systems"] },
        "agriculture-system":       { active: ["agriculture-system"], open: ["agriculture-system-tab", "systems"] },

        // Incidents
        "all-incident":             { active: ["all-incident"],     open: ["incidents"] },

        // Misc
        "all-workshop":             { active: ["workshop"] },
        "workshop":                 { active: ["workshop"] },
        "donor":                    { active: ["donor"] },
        "user":                     { active: ["user"] },
        "setting":                  { active: ["setting"] },
    };

    function markActive(dataRoute) {
        const $item = $(`[data-route="${dataRoute}"]`);
        if (!$item.length) return;
        $item.addClass("active");
        $item.children("a.menu-link").addClass("active");
        $item.parents("li.menu-item").each(function () {
            $(this).addClass("open");
            $(this).children("a.menu-link").addClass("active");
        });
        // ensure submenu is visible for active items
        $item.children("ul.menu-sub").show();
    }

    function markOpen(dataRoute) {
        const $item = $(`[data-route="${dataRoute}"]`);
        if (!$item.length) return;
        $item.addClass("open");
        $item.children("a.menu-link").addClass("active").attr('aria-expanded', 'true');
        $item.parents("li.menu-item").each(function () {
            $(this).addClass("open");
            $(this).children("a.menu-link").addClass("active").attr('aria-expanded', 'true');
        });
        $item.children("ul.menu-sub").show();
    }

    // Special case: /meter-history/all
    if (lastPart === "all" && pathSegments.includes("meter-history")) {
        markActive("all-meter-histories");
        markOpen("meter-history");
        return;
    }

    let route = routes[lastPart];

    if (!route) {
        // try any path segment (right-to-left)
        const candidates = [...pathSegments].reverse();
        for (const segment of candidates) {
            if (routes[segment]) {
                route = routes[segment];
                break;
            }
        }
    }

    if (!route) {
        // fallback: find any route key that appears in the pathname (prefer longer keys)
        const pathname = currentUrl.pathname;
        let bestKey = null;
        Object.keys(routes).forEach(key => {
            if (pathname.indexOf('/' + key) !== -1) {
                if (!bestKey || key.length > bestKey.length) bestKey = key;
            }
        });
        if (bestKey) route = routes[bestKey];
    }

    if (!route) {
        const pathname = currentUrl.pathname;
        let bestEl = null;
        $('[data-route]').each(function () {
            const dr = $(this).data('route');
            if (!dr) return;
            const exact = pathSegments.includes(String(dr));
            const contains = pathname.indexOf('/' + dr) !== -1;
            if (exact || contains) {
                if (!bestEl) bestEl = { el: $(this), key: dr };
                else if (String(dr).length > String(bestEl.key).length) bestEl = { el: $(this), key: dr };
            }
        });
        if (bestEl) {
            // build a synthetic route object using the data-route as active
            route = { active: [bestEl.key] };
        } else {
            return; // still nothing matched
        }
    }

    (route.active || []).forEach(markActive);
    (route.open   || []).forEach(markOpen);

    // make sure open parents' submenus are visible on load
    $('li.menu-item.open').each(function (){
        $(this).children('ul.menu-sub').show();
        $(this).children('a.menu-link').attr('aria-expanded','true');
    });

        // Toggle behavior for menu-toggle links
        $(document).on('click', 'a.menu-link.menu-toggle', function (e) {
        e.preventDefault();
        const $link = $(this);
        const $li = $link.closest('li.menu-item');
        const $sub = $li.children('ul.menu-sub');

        if (!$sub.length) return;

        if ($li.hasClass('open')) {
            $li.removeClass('open');
            $link.removeClass('active').attr('aria-expanded','false');
            $sub.slideUp(180);
        } else {
            // close sibling menus at same level for tidy behavior
            $li.siblings('li.menu-item.open').each(function(){
                $(this).removeClass('open').children('a.menu-link').removeClass('active').attr('aria-expanded','false');
                $(this).children('ul.menu-sub').slideUp(180);
            });
            $li.addClass('open');
            $link.addClass('active').attr('aria-expanded','true');
            $sub.slideDown(200);
        }
        });
    }

    if (window.jQuery) init(window.jQuery);
    else {
        const jqInterval = setInterval(function(){
            if (window.jQuery) {
                clearInterval(jqInterval);
                init(window.jQuery);
            }
        }, 50);
        setTimeout(function(){ clearInterval(jqInterval); }, 5000);
    }
})();
</script><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/layouts/sections/menu/vertical.blade.php ENDPATH**/ ?>