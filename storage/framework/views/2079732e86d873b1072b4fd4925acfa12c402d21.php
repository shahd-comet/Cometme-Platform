


<?php $__env->startSection('title', 'all internet systems'); ?>


<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php $__env->startSection('content'); ?>


<style>
    .img-fluid:hover {
        transform: scale(1.5);
    }
</style>

<ul class="nav nav-tabs mb-3" id="internetTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="summary-tab" data-toggle="tab" href="#summary" role="tab" aria-controls="summary" aria-selected="true">Summary</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="all-systems-tab" data-toggle="tab" href="#all-systems" role="tab" aria-controls="all-systems" aria-selected="false">All Systems</a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="returned-tab" data-toggle="tab" href="#returned-systems" role="tab" aria-controls="returned-systems" aria-selected="false">Removed Internet Systems</a>
    </li>
</ul>

<div class="tab-content" id="internetTabsContent">
    <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h6 class="card-title">Total Installed Systems</h6>
                        <h3 class="card-text"><?php echo e($totalInstalled ?? 0); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Total Internet Holders</h6>
                        <h3 class="card-text"><?php echo e($totalHolders ?? 0); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Returned Internet Systems</h6>
                        <h3 class="card-text"><?php echo e($totalReturned ?? 0); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Components Lost</h6>
                        <p class="card-text"><?php echo e($componentsLost ?? 0); ?> (placeholder — will use new components table)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Components Reused</h6>
                        <p class="card-text"><?php echo e($componentsReused ?? 0); ?> (placeholder — will use new components table)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="all-systems" role="tabpanel" aria-labelledby="all-systems-tab">

        <p>
            <a class="btn btn-primary" data-toggle="collapse" href="#collapseInternetSystemVisualData" 
                role="button" aria-expanded="false" aria-controls="collapseInternetSystemVisualData">
                <i class="menu-icon tf-icons bx bx-show-alt"></i>
                Visualize Data
            </a>
        </p>

        <div class="collapse multi-collapse mb-4" id="collapseInternetSystemVisualData">
            <div class="py-3 container">
                <h4 class="py-3 breadcrumb-wrapper">
                    <span class="text-muted fw-light">Network </span> Diagram
                </h4>
                <img src="/assets/images/diagram.png" class="img-fluid" alt="Responsive image">
            </div>
            <br>
        </div>

        <h4 class="py-3 breadcrumb-wrapper mb-4">
          <span class="text-muted fw-light">All </span> Internet Systems
        </h4>

        <?php if(session()->has('message')): ?>
            <div class="row">
                <div class="alert alert-success">
                    <?php echo e(session()->get('message')); ?>

                </div>
            </div>
        <?php endif; ?>

        <?php echo $__env->make('system.internet.details', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="container">
            <div class="card my-2">
                <div class="card-body">
                    <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 6 ||
                        Auth::guard('user')->user()->user_type_id == 10 ||
                        Auth::guard('user')->user()->user_type_id == 13): ?>
                        <div>
                            <a type="button" class="btn btn-success" 
                                href="<?php echo e(url('internet-system', 'create')); ?>">
                                Create New Internet System	
                            </a>
                            <a type="button" class="btn btn-success" 
                                href="<?php echo e(url('internet-component', 'create')); ?>">
                                Create New Internet Components	
                            </a>
                        </div>
                    <?php endif; ?>
                    <table id="internetAllSystemsTable" class="table table-striped data-table-internet-system my-2">
                        <thead>
                            <tr>
                                <th class="text-center">System Name</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">System Type</th>
                                <th class="text-center">Start Year</th>
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

    <div class="tab-pane fade" id="returned-systems" role="tabpanel" aria-labelledby="returned-tab">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <h5 class="card-title">Removed Internet Systems</h5>
                        <p class="card-text">Removed systems listing will appear here.</p>
                    </div>
                    <div>
                        <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                            Auth::guard('user')->user()->user_type_id == 2 ||
                            Auth::guard('user')->user()->user_type_id == 6 ||
                            Auth::guard('user')->user()->user_type_id == 10 ||
                            Auth::guard('user')->user()->user_type_id == 13): ?>
                            <a class="btn btn-success" href="<?php echo e(route('internet.returns.create')); ?>">Create Removed Record</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="returnedInternetSystemsTable" class="table table-striped my-2">
                        <thead>
                            <tr>
                                <th>System</th>
                                <th>Community</th>
                                <th>Component</th>
                                <th>Date Returned</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">

    $(function () {

        var table = $('.data-table-internet-system').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo e(route('internet-system.index')); ?>",
                data: function (d) {
                    d.search = $('input[type="search"]').val()
                }
            },
            columns: [
                {data: 'system_name', name: 'system_name'},
                {data: 'community_name', name: 'community_name'},
                {data: 'name', name: 'name'},
                {data: 'start_year', name: 'start_year'},
                {data: 'action'}
            ]
        });
    });


    // View record edit page

    $('#internetAllSystemsTable').on('click', '.updateInternetSystem',function() {
        var id = $(this).data('id');
        var url = window.location.href; 
        url = url +'/'+ id +'/edit';
        // AJAX request
        $.ajax({
            url: 'internet-system/' + id + '/editpage',
            type: 'get',
            dataType: 'json',
            success: function(response) {
                window.open(url, "_self"); 
            }
        });
    });


    // View cabinet record edit page
    
    $('#internetAllSystemsTable').on('click', '.cabinetInternetSystem', function () {
        
        var id = $(this).data('id');
        var url = window.location.origin + '/internet-system/' + id + '/cabinet';
        window.location.href = url;
    });



    // View record details

    $('#internetAllSystemsTable').on('click', '.viewInternetSystem',function() {
        var id = $(this).data('id');
        var url = window.location.href; 
        url = url +'/'+ id;


        // AJAX request
        $.ajax({
            url: 'internet-system/' + id + '/showPage',
            type: 'get',
            dataType: 'json',
            success: function(response) {

                window.open(url, "_self"); 
            }
        });
    });


    // Delete record
    
    $('#internetAllSystemsTable').on('click', '.deleteInternetSystem',function() {
        var id = $(this).data('id');


        Swal.fire({
            icon: 'warning',
            title: 'Are you sure you want to delete this system?',
            showDenyButton: true,
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: "<?php echo e(route('deleteInternetSystem')); ?>",
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
                                $('#internetAllSystemsTable').DataTable().draw();
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

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/system/internet/index.blade.php ENDPATH**/ ?>