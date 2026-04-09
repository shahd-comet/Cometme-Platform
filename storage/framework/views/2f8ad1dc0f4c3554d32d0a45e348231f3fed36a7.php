

<?php $__env->startSection('title', 'communities'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>

<p> 
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseCommunityVisualData" 
        role="button" aria-expanded="false" aria-controls="collapseCommunityVisualData">
        <i class="menu-icon tf-icons bx bx-show-alt"></i>
        Visualize Data
    </a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseCommunityExport" aria-expanded="false" 
        aria-controls="collapseCommunityExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseCommunityVisualData collapseCommunityExport">
        <i class="menu-icon tf-icons bx bx-expand-alt"></i>
        Toggle All 
    </button> 
</p>

<div class="collapse multi-collapse" id="collapseCommunityVisualData">
    <div class="container" >
        <div class="row g-4 mb-4"> 
            <div class="col">
                <div class="card">
                    <div class="card-header"> 
                        <h5 class="card-title mb-0">Energy Service</h5>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        <a type="button" data-bs-toggle="modal" 
                                            data-bs-target="#communityInitial">
                                            <i class='bx bx-message'></i>
                                        </a>
                                    </span>
                                    <?php echo $__env->make('employee.community.service.initial', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Initial Communities</span>
                                        <span class="text-muted"><?php echo e($communityInitial); ?></span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-primary" style="width: <?php echo e($communityInitial); ?>%" 
                                            role="progressbar" aria-valuenow="<?php echo e($communityInitial); ?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="<?php echo e($communityRecords); ?>">
                                        </div>
                                    </div>
                                </div> 
                            </li>
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                        <a type="button" data-bs-toggle="modal" 
                                            data-bs-target="#communityAC">
                                            <i class='bx bx-message-alt-detail'></i>
                                        </a>
                                    </span>
                                    <?php echo $__env->make('employee.community.service.ac_survey', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>AC Communitites</span>
                                        <span class="text-muted">
                                            <?php if($communityAC): ?>
                                                <?php echo e($communityAC); ?>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-warning" style="width: <?php echo e($communityAC); ?>%" 
                                            role="progressbar" aria-valuenow="<?php echo e($communityAC); ?>" aria-valuemin="0" 
                                            aria-valuemax="<?php echo e($communityRecords); ?>">
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex mb-4 pb-2">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        <a type="button" data-bs-toggle="modal" 
                                            data-bs-target="#communitySurveyed">
                                            <i class='bx bx-bulb'></i>
                                        </a>
                                    </span>
                                    <?php echo $__env->make('employee.community.service.surveyed', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    </span>
                                </div>
                                <div class="d-flex flex-column w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Active Communities</span>
                                        <span class="text-muted">
                                            <?php echo e($communitySurvyed); ?>
                                        </span>
                                    </div>
                                    <?php
                                        $diff = ($communitySurvyed / $communityRecords ) * 100;
                                    ?>
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-success" 
                                            style="width: <?php echo e($diff); ?>%" 
                                            role="progressbar" 
                                            aria-valuenow="<?php echo e($diff); ?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="<?php echo e($communityRecords); ?>">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Water Service</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h4 class="mb-0 me-2">
                                        <?php if($communityWater): ?>
                                            <?php echo e($communityWater); ?>
                                        <?php endif; ?>
                                    </h4> <small>Communities</small>
                                </div>
                                
                                    <?php if($communityWater): ?>
                                    <?php
                                        $min = $communityRecords - $communityWater;
                                    ?>

                                        <?php if($min < $communityRecords/2): ?>
                                            <small class="text-success"><?php echo e($min); ?>
                                        <?php else: ?> 
                                            <small class="text-danger"><?php echo e($min); ?>
                                        <?php endif; ?>
                                        
                                    <?php endif; ?>
                                </small>
                                <small>Remaining</small>
                            </div>
                            <span class="badge bg-label-primary rounded p-2">
                                <a type="button" data-bs-toggle="modal" 
                                    data-bs-target="#communityWater">
                                    <i class="bx bx-water bx-sm"></i>
                                </a>
                                <?php echo $__env->make('employee.community.service.water', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Internet Service</span>
                                <div class="d-flex align-items-end mt-2">
                                    <h4 class="mb-0 me-2">
                                        <?php if($communityInternet): ?>
                                            <?php echo e($communityInternet); ?>
                                        <?php endif; ?>
                                    </h4>  
                                    <small>Communities</small>
                                </div>
                                <?php if($communityInternet): ?>
                                <?php
                                    $min = $communityRecords - $communityInternet;
                                ?>  
                                    <?php if($min < $communityRecords/2): ?>
                                        <small class="text-success"><?php echo e($min); ?>
                                    <?php else: ?> 
                                        <small class="text-danger"><?php echo e($min); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                </small>
                                <small>Remaining</small>
                            </div>
                            <span class="badge bg-label-success rounded p-2">

                                <a type="button" data-bs-toggle="modal" 
                                    data-bs-target="#communityInternet">
                                    <i class="bx bx-wifi bx-sm"></i>
                                </a>
    
                                <?php echo $__env->make('employee.community.service.internet', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col">
                <div class="panel panel-primary">
                    <div class="panel-body" >
                        <div id="pie_chart_regional_community" style="height:450px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="collapse multi-collapse container mb-4" id="collapseCommunityExport">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <h5> 
                                Export Community Report 
                                <i class='fa-solid fa-file-excel text-info'></i>
                            </h5>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2">
                            <fieldset class="form-group">
                                <button class="" id="clearCommunityFiltersButton">
                                <i class='fa-solid fa-eraser'></i>
                                    Clear Filters
                                </button>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <form method="POST" enctype='multipart/form-data' 
                    action="<?php echo e(route('community.export')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="region[]" class="selectpicker form-control" 
                                    data-live-search="true" multiple>
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
                                    <select name="public[]" class="selectpicker form-control" 
                                    data-live-search="true" multiple>
                                        <option disabled selected>Search Public Structure</option>
                                        <?php $__currentLoopData = $publicCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $publicCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($publicCategory->id); ?>">
                                            <?php echo e($publicCategory->name); ?>
                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="system_type[]"
                                        class="selectpicker form-control" 
                                        data-live-search="true" multiple>
                                        <option disabled selected>Search System Type</option>
                                        <?php $__currentLoopData = $energySystemTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energySystemType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($energySystemType->id); ?>">
                                                <?php echo e($energySystemType->name); ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="donor[]" class="selectpicker form-control" 
                                    data-live-search="true" multiple>
                                        <option disabled selected>Search Donor</option>
                                        <?php $__currentLoopData = $donors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($donor->id); ?>">
                                                <?php echo e($donor->donor_name); ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select> 
                                </fieldset>
                            </div>
                        </div>
                        <div class="row" style="margin-top:18px">
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <button class="btn btn-info" type="submit">
                                        <i class='fa-solid fa-file-excel'></i>
                                        Export Excel
                                    </button>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </form>
            </div>  
        </div>
    </div> 
</div> 

<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">All </span> communities
</h4>

<?php if(session()->has('message')): ?>
    <div class="row">
        <div class="alert alert-success">
            <?php echo e(session()->get('message')); ?>
        </div>
    </div>
<?php endif; ?>
<?php echo $__env->make('employee.community.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> 
 
<div class="container">
    <div class="card my-2"> 
        <div class="card-header">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Region</label>
                        <select name="region_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByRegion">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $regions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $region): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($region->id); ?>"><?php echo e($region->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Sub Region</label>
                        <select name="sub_region_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterBySubRegion">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $subregions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subRegion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($subRegion->id); ?>"><?php echo e($subRegion->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Status</label>
                        <select name="status_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByStatus">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $communityStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $communityStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($communityStatus->id); ?>"><?php echo e($communityStatus->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                Auth::guard('user')->user()->user_type_id == 2 ||
                Auth::guard('user')->user()->user_type_id == 3 ): ?>
                <div>
                    <a type="button" class="btn btn-success" 
                        href="<?php echo e(url('community', 'create')); ?>">
                        Create New Community	
                    </a>
                </div>
            <?php endif; ?>
            <table id="communityTable" class="table table-striped data-table-communities my-2">
                <thead>
                    <tr>
                        <th>English Name</th>
                        <th>Arabic Name</th>
                        <th># of Households</th>
                        <th># of People</th>
                        <th>Region</th>
                        <th>Sub Region</th>
                        <th>Status</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <input type="hidden" name="txtCommunityId" id="txtCommunityId" value="0">
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
 
<script type="text/javascript">
    
    var table;
    function DataTableContent() {

        table = $('.data-table-communities').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('community.index')); ?>",
                data: function (d) {
                    d.search = $('input[type="search"]').val();
                    d.filter = $('#filterByRegion').val();
                    d.second_filter = $('#filterBySubRegion').val();
                    d.third_filter = $('#filterByStatus').val();
                }
            },
            columns: [
                {data: 'english_name', name: 'english_name'},
                {data: 'arabic_name', name: 'arabic_name'},
                {data: 'number_of_household', name: 'number_of_household'},
                {data: 'number_of_people', name: 'number_of_people'},
                {data: 'name', name: 'name'},
                {data: 'subname', name: 'subname'},
                {data: 'status_name', name: 'status_name'},
                {data: 'action'}
            ]
        });
    }

    $(function () {
        
        var analytics = <?php echo $regionsData; ?>;
        var analyticsSubRegions = <?php echo $subRegionsData; ?>;

        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart()
        {
            var data = google.visualization.arrayToDataTable(analytics);
            var options = {
                title : 'Communities by Region'
            };

            var data1 = google.visualization.arrayToDataTable(analyticsSubRegions);
            var options1 = {
                title : 'Communities by Sub-Region'
            };

            var chart = new google.visualization.PieChart(document.getElementById('pie_chart_regional_community'));
            chart.draw(data, options);

            var chart1 = new google.visualization.PieChart(document.getElementById('pie_chart_sub_regional_community'));
            chart1.draw(data1, options1);
        }

        DataTableContent();
        
        $('#filterByRegion').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterBySubRegion').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByStatus').on('change', function() {
            table.ajax.reload(); 
        });

        // Clear Filter
        $('#clearFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            if ($.fn.DataTable.isDataTable('.data-table-communities')) {
                $('.data-table-communities').DataTable().destroy();
            }
            DataTableContent();
        });

        // Clear Filters for Export
        $('#clearCommunityFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
        });

        // View record details
        $('#communityTable').on('click', '.detailsCommunityButton', function() {
            var id = $(this).data('id');
            var url = window.location.href; 
           
            url = url +'/'+ id ;
            window.open(url); 
        }); 
        
        // View record photos
        $('#communityTable').on('click', '.imageCommunity',function() {
            var id = $(this).data('id');
            var url = window.location.href; 
           
            url = url +'/'+ id +'/photo';
            window.open(url); 
        });

        // View record map
        $('#communityTable').on('click', '.mapCommunityButton', function() {
            var id = $(this).data('id');
            var url = window.location.href; 
            url = url +'/'+ id +'/map';
            window.open(url); 
        });

        // View record update page
        $('#communityTable').on('click', '.updateCommunity', function() {
            var id = $(this).data('id');
            var url = window.location.href; 
            url = url +'/'+ id +'/edit';
            
            // AJAX request
            $.ajax({
                url: 'community/' + id + '/editpage',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    window.open(url, "_self"); 
                }
            });
        });

        // delete community
        $('#communityTable').on('click', '.deleteCommunity',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this community?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteCommunity')); ?>",
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
                                    $('#communityTable').DataTable().draw();
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
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/employee/community/index.blade.php ENDPATH**/ ?>