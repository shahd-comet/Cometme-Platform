

<?php $__env->startSection('title', 'sub communities'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
 
<p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseSubCommunityExport" aria-expanded="false" 
        aria-controls="collapseSubCommunityExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button>
</p>

<div class="collapse multi-collapse container mb-4" id="collapseSubCommunityExport">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <h5>Export Sub-Community Report 
                                <i class='fa-solid fa-file-excel text-info'></i>
                            </h5>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2">
                            <fieldset class="form-group">
                                <button class="" id="clearSubCommunityFiltersButton">
                                <i class='fa-solid fa-eraser'></i>
                                    Clear Filters
                                </button>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <form method="POST" enctype='multipart/form-data' 
                    action="<?php echo e(route('sub-community-household.export')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="community" class="selectpicker form-control" 
                                    data-live-search="true">
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
                                    <select name="region" class="selectpicker form-control" 
                                    data-live-search="true">
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
                                    <select name="system_type"  class="selectpicker form-control" 
                                    data-live-search="true">
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
  <span class="text-muted fw-light">All </span> sub communities
</h4>

<?php if(session()->has('message')): ?>
    <div class="row">
        <div class="alert alert-success">
            <?php echo e(session()->get('message')); ?>
        </div>
    </div>
<?php endif; ?>

<?php if(session()->has('error')): ?>
    <div class="row">
        <div class="alert alert-danger">
            <?php echo e(session()->get('error')); ?>
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
                Auth::guard('user')->user()->user_type_id == 2  ): ?>
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4">
                    <button type="button" class="btn btn-success" 
                        data-bs-toggle="modal" data-bs-target="#createSubCommunity">
                        Create New Sub Community	
                    </button>
                    <?php echo $__env->make('admin.community.sub.create_sub', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <button type="button" class="btn btn-success" 
                        data-bs-toggle="modal" data-bs-target="#createSubCommunityHousehold">
                        Create New Sub Community Household	
                    </button>
                    <?php echo $__env->make('admin.community.sub.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <table id="subCommunityTable" class="table table-striped data-table-sub-communities my-2">
                <thead>
                    <tr>
                        <th >Household</th>
                        <th >Community</th>
                        <th >Region</th>
                        <th >Sub-community English Name</th>
                        <th >Sub-community Arabic Name</th>
                        <?php if(Auth::guard('user')->user()->user_type_id == 1 ||
                            Auth::guard('user')->user()->user_type_id == 2  ): ?>
                            <th >Options</th>
                        <?php else: ?>
                            <th></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">

    var table;
    function DataTableContent() {

        table = $('.data-table-sub-communities').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('sub-community-household.index')); ?>",
                data: function (d) {
                    d.search = $('input[type="search"]').val();
                    d.filter = $('#filterByCommunity').val();
                    d.second_filter = $('#filterByRegion').val();
                    d.third_filter = $('#filterBySubRegion').val();
                }
            },
            columns: [
                {data: 'household', name: 'household'},
                {data: 'community_english_name', name: 'community_english_name'},
                {data: 'name', name: 'name'},
                {data: 'english_name', name: 'english_name'},
                {data: 'arabic_name', name: 'arabic_name'},
                {data: 'action'}
            ]
        });
    }

    $(function () {

        DataTableContent();

        $('#filterByRegion').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterBySubRegion').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByCommunity').on('change', function() {
            table.ajax.reload(); 
        });

        // Clear Filter
        $('#clearFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            if ($.fn.DataTable.isDataTable('.data-table-sub-communities')) {
                $('.data-table-sub-communities').DataTable().destroy();
            }
            DataTableContent();
        });

        // Clear Filters for Export
        $('#clearSubCommunityFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
        });

        // View record details
        $('#subCommunityTable').on('click','.detailsSubCommunityButton',function() {
            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'sub-community-household/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    $('#communityModalTitle').html(" ");
                    $('#englishNameCommunity').html(" ");
                    $('#arabicNameCommunity').html(" ");
                    $('#numberOfCompoundsCommunity').html(" ");
                    $('#numberOfPeopleCommunity').html(" ");
                    $('#englishNameRegion').html(" ");
                    $('#numberOfHouseholdCommunity').html(" ");
                    $('#englishNameSubRegion').html(" ");
                    $('#statusCommunity').html(" ");
                    $('#energyServiceCommunity').html(" ");
                    $('#energyServiceYearCommunity').html(" ");
                    $('#waterServiceCommunity').html(" ");
                    $('#waterServiceYearCommunity').html(" ");
                    $('#internetServiceCommunity').html(" ");
                    $('#internetServiceYearCommunity').html(" ");

                    $('#communityModalTitle').html(response['community'].english_name);
                    $('#englishNameCommunity').html(response['community'].english_name);
                    $('#arabicNameCommunity').html(response['community'].arabic_name);
                    $('#numberOfCompoundsCommunity').html(response['community'].number_of_compound);
                    $('#numberOfPeopleCommunity').html(response['community'].number_of_people);
                    $('#englishNameRegion').html(response['region'].english_name);
                    $('#numberOfHouseholdCommunity').html(response['community'].number_of_households);
                    $('#englishNameSubRegion').html(response['sub-region'].english_name);
                    $('#statusCommunity').html(response['status'].name);
                    $('#energyServiceCommunity').html(response['community'].energy_service);
                    $('#energyServiceYearCommunity').html(response['community'].energy_service_beginning_year);
                    $('#waterServiceCommunity').html(response['community'].water_service);
                    $('#waterServiceYearCommunity').html(response['community'].water_service_beginning_year);
                    $('#internetServiceCommunity').html(response['community'].internet_service);
                    $('#internetServiceYearCommunity').html(response['community'].internet_service_beginning_year);

                    $("#waterSourcesCommunity").html(" ");
                    for (var i = 0; i < response['communityWaterSources'].length; i++) {
                        $("#waterSourcesCommunity").append(
                            '<ul><li>'+ response['communityWaterSources'][i].name +'</li> </ul>');
                    }
                }
            });
        });

        // View record update page
        $('#subCommunityTable').on('click', '.updateSubCommunity', function() {
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
        $('#subCommunityTable').on('click', '.deleteSubCommunityHousehold',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this sub community household?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deleteSubCommunityHousehold')); ?>",
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
                                    $('#subCommunityTable').DataTable().draw();
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
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/admin/community/sub/index.blade.php ENDPATH**/ ?>