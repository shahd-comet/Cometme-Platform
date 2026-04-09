<?php
  $pricingModal = true;
?>



<?php $__env->startSection('title', 'other holders'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
 
<p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseOtherHoldersExport" aria-expanded="false" 
        aria-controls="collapseOtherHoldersExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export  
    </button>
</p> 


<div class="collapse multi-collapse mb-4" id="collapseOtherHoldersExport">
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                Export Holders Report
                                    <i class='fa-solid fa-file-excel text-info'></i>
                                </h5>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <fieldset class="form-group">
                                    <button class="" id="clearAllHoldersFiltersButton">
                                    <i class='fa-solid fa-eraser'></i>
                                        Clear Filters
                                    </button>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <form method="POST" enctype='multipart/form-data' id="exportFormWaterHolder"
                        action="<?php echo e(route('other-holder.export')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="card-body"> 
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>File Type</label>
                                        <select name="file_type" required id="fileType"
                                            class="selectpicker form-control" data-live-search="true" >
                                            <option disabled selected>Select File Type</option>
                                            <option value="town_holders">Town Holders</option>
                                            <option value="activist_holders">Activist Holders</option>
                                            <option value="internal_holders">Community Internal Holders</option>
                                        </select> 
                                        <div id="file_type_error" style="color: red;"></div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row" style="margin-top:20px">
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
  <span class="text-muted fw-light">All </span> Holders
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
                        <label class='col-md-12 control-label'>Filter By Town</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByTown">
                            <option disabled selected>Search Town</option>
                            <?php $__currentLoopData = $towns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $town): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($town->id); ?>"><?php echo e($town->english_name); ?></option>
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
                    <a class="nav-link active" data-bs-toggle="tab" href="#town-holders" role="tab">
                        <i class='fas fa-city me-2'></i>
                        Town Holder
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activist-holders" role="tab">
                        <i class='fas fa-users me-2'></i>
                        Activists
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#internal-holders" role="tab">
                        <i class='fas fa-user me-2'></i>
                        Community Internal Holders
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="holderTabContent">
                <!-- All Town Holders Tab -->
                <div class="tab-pane fade show active" id="town-holders" role="tabpanel" 
                    aria-labelledby="requested-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                            <div style="margin-top:18px">
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#createTownHolderModal">
                                    Create New Town Holder
                                </button>
                                <?php echo $__env->make('holders.town.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>
                    <table id="townHoldersTable" class="table table-striped my-2 data-table-town-holder">
                        <thead>
                            <tr>
                                <th class="text-center">English Name</th>
                                <th class="text-center">Arabic Name</th>
                                <th class="text-center">Town</th>
                                <th class="text-center">Phone Number</th>
                                <th class="text-center">Has Internet</th>
                                <th class="text-center">Has Refrigerator</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>

                <!-- All Activists Tab -->
                <div class="tab-pane fade show" id="activist-holders" role="tabpanel" 
                    aria-labelledby="activist-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                            <div style="margin-top:18px">
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#createActivistHolderModal">
                                    Create New Activist Holder
                                </button>
                                <?php echo $__env->make('holders.activist.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>
                    <table id="activistHoldersTable" class="table table-striped my-2 data-table-activist-holder">
                        <thead>
                            <tr>
                                <th class="text-center">English Name</th>
                                <th class="text-center">Arabic Name</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Phone Number</th>
                                <th class="text-center">Has Internet</th>
                                <th class="text-center">Has Refrigerator</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>

                <!-- All Internal Tab -->
                <div class="tab-pane fade show" id="internal-holders" role="tabpanel" 
                    aria-labelledby="internal-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                            <div style="margin-top:18px">
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#createInternalHolderModal">
                                    Create New Internal Holder
                                </button>
                                <?php echo $__env->make('holders.internal.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>
                    <table id="internalHoldersTable" class="table table-striped my-2 data-table-internal-holder">
                        <thead>
                            <tr>
                                <th class="text-center">English Name</th>
                                <th class="text-center">Arabic Name</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Phone Number</th>
                                <th class="text-center">Has Internet</th>
                                <th class="text-center">Has Refrigerator</th>
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

<?php echo $__env->make('holders.town.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('holders.activist.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('holders.internal.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script>
 
    $('#exportFormWaterHolder').on('submit', function (event) {

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

    // Clear Filters for Export
    $('#clearAllHoldersFiltersButton').on('click', function() {

        $('.selectpicker').prop('selectedIndex', 0);
        $('.selectpicker').selectpicker('refresh');
    });

    $(function () {

        // keep track of initialized tables
        var tables = {};

        function initTownHoldersTable() {

            if (tables.town) return;
            tables.town = $('.data-table-town-holder').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('town-holder.index')); ?>",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.town_filter = $('#filterByTown').val();
                    }
                }, 
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'arabic_name', name: 'arabic_name'},
                    {data: 'town', name: 'town'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'has_internet', name: 'has_internet'},
                    {data: 'has_refrigerator', name: 'has_refrigerator'},
                    {data: 'action'}
                ]
            });
        } 

        function initActivistHoldersTable() {

            if (tables.activist) return;
            tables.activist = $('.data-table-activist-holder').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('activist-holder.index')); ?>",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.town_community = $('#filterByCommunity').val();
                    }
                }, 
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'arabic_name', name: 'arabic_name'},
                    {data: 'community', name: 'community'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'has_internet', name: 'has_internet'},
                    {data: 'has_refrigerator', name: 'has_refrigerator'},
                    {data: 'action'}
                ]
            });
        } 

        function initInternalHoldersTable() {

            if (tables.internal) return;
            tables.internal = $('.data-table-internal-holder').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "<?php echo e(route('internal-holder.index')); ?>",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.town_community = $('#filterByCommunity').val();
                    }
                }, 
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'arabic_name', name: 'arabic_name'},
                    {data: 'community', name: 'community'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'has_internet', name: 'has_internet'},
                    {data: 'has_refrigerator', name: 'has_refrigerator'},
                    {data: 'action'}
                ]
            });
        } 

        initTownHoldersTable();

        // On tab shown, lazy-init the target table
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr('href');
            if (target == '#town-holders') initTownHoldersTable();
            if (target == '#activist-holders') initActivistHoldersTable();
            if (target == '#internal-holders') initInternalHoldersTable();

            // Show the requested filters only when Requested tab is active
            if (target == '#town-holders') {

                $('#filterByTown').prop('disabled', false);
                $('#filterByCommunity').prop('disabled', true);
            } else if (target == '#activist-holders' || target == '#internal-holders' ) {

                $('#filterByCommunity').prop('disabled', false);
                $('#filterByTown').prop('disabled', true);
            }

            if ($('.selectpicker').length && typeof $('.selectpicker').selectpicker === 'function') {

                $('.selectpicker').selectpicker('refresh');
            }
        });


        // Reload initialized tables when any filter changes
        function reloadInitializedTables() {

            if (tables.town) tables.town.ajax.reload();
            if (tables.activist) tables.activist.ajax.reload();
            if (tables.internal) tables.internal.ajax.reload();

        }
        $('#filterByTown, #filterByCommunity').on('change', function () {

            if (tables.town) tables.town.ajax.reload();
            if (tables.activist) tables.activist.ajax.reload();
            if (tables.internal) tables.internal.ajax.reload();
        });

        // Clear filters
        $(document).on('click', '#clearFiltersButton', function () {

            $('#filterByTown').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            if (tables.town) tables.town.ajax.reload();
            if (tables.activist) tables.activist.ajax.reload();
            if (tables.internal) tables.internal.ajax.reload();
        });


        // View record details for the requested water holder
        $('#townHoldersTable').on('click', '.viewTownHolder',function() {

            var id = $(this).data('id');
            $.ajax({
                url: 'town-holder/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    $('#townHolderModalTitle').html(" ");
                    $('#townHolderEnglishName').html(" ");
                    $('#townHolderArabicName').html(" ");
                    $('#townHolderPhoneNumber').html(" ");
                    $('#townHolder').html(" ");
                    
 
                    $('#townHolderModalTitle').html(response['townHolder'].arabic_name);
                    $('#townHolderEnglishName').html(response['townHolder'].english_name);
                    $('#townHolderArabicName').html(response['townHolder'].arabic_name);
                    $('#townHolderPhoneNumber').html(response['townHolder'].phone_number);
                    $('#townHolder').html(response['town'].english_name);
                }
            });
        }); 

        // Delete record for the requested water holder 
        $('#townHoldersTable').on('click', '.deleteTownHolder',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this town holder?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    $.ajax({
                        url: '/delete-town-holder/' + id,  
                        type: 'DELETE',  
                        data: {
                            "_token": "<?php echo e(csrf_token()); ?>" 
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                $('#townHoldersTable').DataTable().draw();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while trying to delete the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // View update form
        $('#townHoldersTable').on('click', '.updateTownHolder',function() {

            var id = $(this).data('id');
            var url = "<?php echo e(url('town-holder')); ?>/" + id + "/edit";
            window.location.href = url;
        });

        // View record details for the Activist
        $('#activistHoldersTable').on('click', '.viewActivistHolder',function() {

            var id = $(this).data('id');
            $.ajax({
                url: 'activist-holder/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    $('#activistHolderModalTitle').html(" ");
                    $('#activistHolderEnglishName').html(" ");
                    $('#activistHolderArabicName').html(" ");
                    $('#activistHolderPhoneNumber').html(" ");
                    $('#communityHolder').html(" ");
                    
 
                    $('#activistHolderModalTitle').html(response['townHolder'].arabic_name);
                    $('#activistHolderEnglishName').html(response['townHolder'].english_name);
                    $('#activistHolderArabicName').html(response['townHolder'].arabic_name);
                    $('#activistHolderPhoneNumber').html(response['townHolder'].phone_number);
                    $('#communityHolder').html(response['community'].english_name);
                }
            });
        }); 

        // View record details for the Internal
        $('#internalHoldersTable').on('click', '.viewInternalHolder',function() {

            var id = $(this).data('id');
            $.ajax({
                url: 'internal-holder/' + id,
                type: 'get',
                dataType: 'json',
                success: function(response) {

                    $('#internalHolderModalTitle').html(" ");
                    $('#internalHolderEnglishName').html(" ");
                    $('#internalHolderArabicName').html(" ");
                    $('#internalHolderPhoneNumber').html(" ");
                    $('#communityInternalHolder').html(" ");
                    
 
                    $('#communityInternalHolder').html(response['community'].english_name);
                    $('#internalHolderModalTitle').html(response['townHolder'].arabic_name);
                    $('#internalHolderEnglishName').html(response['townHolder'].english_name);
                    $('#internalHolderArabicName').html(response['townHolder'].arabic_name);
                    $('#internalHolderPhoneNumber').html(response['townHolder'].phone_number);
                }
            });
        }); 

        // View update form Activist
        $('#activistHoldersTable').on('click', '.updateActivistHolder',function() {

            var id = $(this).data('id');
            var url = "<?php echo e(url('activist-holder')); ?>/" + id + "/edit";
            window.location.href = url;
        });

        // View update form Internal
        $('#internalHoldersTable').on('click', '.updateInternalHolder',function() {

            var id = $(this).data('id');
            var url = "<?php echo e(url('internal-holder')); ?>/" + id + "/edit";
            window.location.href = url;
        });

        // Delete record for the Activist
        $('#activistHoldersTable').on('click', '.deleteActivistHolder',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this Activist holder?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    $.ajax({
                        url: '/delete-town-holder/' + id,  
                        type: 'DELETE',  
                        data: {
                            "_token": "<?php echo e(csrf_token()); ?>" 
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                $('#townHoldersTable').DataTable().draw();
                                $('#activistHoldersTable').DataTable().draw();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while trying to delete the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        // Delete record for the Internal
        $('#internalHoldersTable').on('click', '.deleteInternalHolder',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this Internal holder?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    $.ajax({
                        url: '/delete-town-holder/' + id,  
                        type: 'DELETE',  
                        data: {
                            "_token": "<?php echo e(csrf_token()); ?>" 
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                );
                                $('#townHoldersTable').DataTable().draw();
                                $('#activistHoldersTable').DataTable().draw();
                                $('#internalHoldersTable').DataTable().draw();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while trying to delete the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/holders/index.blade.php ENDPATH**/ ?>