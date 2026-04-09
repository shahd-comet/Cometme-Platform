

<?php $__env->startSection('title', 'Public Structures'); ?>

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

<p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapsePublicExport" aria-expanded="false" 
        aria-controls="collapsePublicExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button> 
</p>

<div class="collapse multi-collapse container mb-4" id="collapsePublicExport">
    <div class="row">
        <div class="container mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                    Export Public Structures Report 
                                    <i class='fa-solid fa-file-excel text-info'></i>
                                </h5>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <fieldset class="form-group">
                                    <button class="" id="clearPublicFiltersButton">
                                    <i class='fa-solid fa-eraser'></i>
                                        Clear Filters
                                    </button>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <form method="POST" enctype='multipart/form-data' 
                        action="<?php echo e(route('public-structure.export')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <select name="region"
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
                                        <select name="public" class="selectpicker form-control" 
                                            data-live-search="true">
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
  <span class="text-muted fw-light">All </span> Public Structures
</h4>

<?php if(session()->has('message')): ?>
    <div class="row">
        <div class="alert alert-success">
            <?php echo e(session()->get('message')); ?>

        </div>
    </div>
<?php endif; ?>

<?php echo $__env->make('public.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container">
    <div class="card my-2">
        <div class="card-header">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Community</label>
                        <select class="selectpicker form-control" 
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
                        <label class='col-md-12 control-label'>Filter By Category</label>
                        <select name="region_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByCategory">
                            <option disabled selected>Choose one...</option>
                            <?php $__currentLoopData = $publicCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $publicCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($publicCategory->id); ?>"><?php echo e($publicCategory->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Main/Shared Public</label>
                        <select name="main_shared" class="selectpicker form-control" 
                            data-live-search="true" id="filterByMainPublic">
                            <option disabled selected>Choose one...</option>
                            <option value="Yes">Main Public</option>
                            <option value="No">Shared Public</option>
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
                Auth::guard('user')->user()->user_type_id == 3 ||
                Auth::guard('user')->user()->user_type_id == 4 ||
                Auth::guard('user')->user()->user_type_id == 5 ||
                Auth::guard('user')->user()->user_type_id == 6 ||
                Auth::guard('user')->user()->role_id == 21): ?>
                <div>
                    <button type="button" class="btn btn-success" 
                        data-bs-toggle="modal" data-bs-target="#createPublicStructure">
                        Add Public Structure
                    </button>
                    <?php echo $__env->make('public.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            <?php endif; ?>
            <table id="publicStructureTable" class="table table-striped data-table-public-structure my-2">
                <thead>
                    <tr>
                        <th class="text-center">English Name</th>
                        <th class="text-center">Arabic Name</th>
                        <th class="text-center">Community</th>
                        <th class="text-center">Meter Number</th>
                        <th class="text-center">Options</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $__env->make('components.meter-history-complete', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<script type="text/javascript">

    var table;
    function DataTableContent() {

        table = $('.data-table-public-structure').DataTable({
            processing: true,
            serverSide: true, 
            ajax: {
                url: "<?php echo e(route('public-structure.index')); ?>",
                data: function (d) {
                    d.search = $('input[type="search"]').val();
                    d.filter = $('#filterByCommunity').val();
                    d.second_filter = $('#filterByCategory').val();
                    d.third_filter = $('#filterByMainPublic').val();
                }
            },
            dom: 'Blfrtip',
            columns: [
                {data: 'english_name', name: 'english_name'},
                {data: 'arabic_name', name: 'arabic_name'},
                {data: 'community_name', name: 'community_name'},
                {
                    data: 'meter_number',
                    name: 'meter_number',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            // clickable meter number that opens modal
                            if(data != null) return '<a href="#" class="show-meter-history" data-meter="' + data + '">' + data + '</a>';
                        }
                        return data;
                    }
                },
                {data: 'action'}
            ]
        });
    }

    $(function () {

        DataTableContent();
        
        $('#filterByCommunity').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByCategory').on('change', function() {
            table.ajax.reload(); 
        });
        $('#filterByMainPublic').on('change', function() {
            table.ajax.reload(); 
        });

        // Clear Filter
        $('#clearFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            if ($.fn.DataTable.isDataTable('.data-table-public-structure')) {
                $('.data-table-public-structure').DataTable().destroy();
            }
            DataTableContent();
        });

        // View record details
        $('#publicStructureTable').on('click', '.viewPublicStructure', function() {
            var id = $(this).data('id');
        
            // AJAX request
            $.ajax({
                url: 'public-structure/' + id,
                type: 'get',
                dataType: 'json', 
                success: function(response) {

                    $('#publicStructureModalTitle').html('');
                    $('#publicStructureModalTitle').html(response['publicStructure'].english_name);
                    $('#englishNamePublic').html('');
                    $('#englishNamePublic').html(response['publicStructure'].english_name);
                    $('#arabicNamePublic').html('');
                    $('#arabicNamePublic').html(response['publicStructure'].arabic_name);
                    $('#communityName').html('');
                    $('#communityName').html(response['community'].english_name);
                    $('#publicNotes').html('');
                    $('#publicNotes').html(response['publicStructure'].notes);
                    $('#publicStatus').html('');
                    $('#publicStatus').html(response['status'].status);

                    $("#kindergartenDetails").css("visibility", "none");
                    $("#kindergartenDetails").css('display', 'none');

                    $("#schoolDetails").css("visibility", "none");
                    $("#schoolDetails").css('display', 'none');
                    $('#compoundName').html('');
                    if(response['compound']) {
                        
                        $('#compoundName').html('');
                        $('#compoundName').html(response['compound'].english_name);
                    }
                    if(response['schoolPublic']) {

                        $("#schoolDetails").css("visibility", "visible");
                        $("#schoolDetails").css('display', 'block');
                        $('#totalSchoolStudents').html('');
                        $('#totalSchoolStudents').html(response['schoolPublic'].number_of_boys + response['schoolPublic'].number_of_girls);
                        $('#schoolBoys').html('');
                        $('#schoolBoys').html(response['schoolPublic'].number_of_boys);
                        $('#schoolGirls').html('');
                        $('#schoolGirls').html(response['schoolPublic'].number_of_girls);
                        $('#gradeFrom').html('');
                        $('#gradeFrom').html(response['schoolPublic'].grade_from);
                        $('#gradeTo').html('');
                        $('#gradeTo').html(response['schoolPublic'].grade_to);
                    }
 
                    if(response['publicStructure'].public_structure_category_id1 == 5 ||
                        response['publicStructure'].public_structure_category_id2 == 5 ||
                        response['publicStructure'].public_structure_category_id3 == 5)  {

                            $("#kindergartenDetails").css("visibility", "visible");
                            $("#kindergartenDetails").css('display', 'block');
                            $('#totalKindergartenStudents').html('');
                            $('#totalKindergartenStudents').html(response['publicStructure'].kindergarten_students);
                            $('#kindergartenBoys').html('');
                            $('#kindergartenBoys').html(response['publicStructure'].kindergarten_male);
                            $('#kindergartenGirls').html('');
                            $('#kindergartenGirls').html(response['publicStructure'].kindergarten_female);
                    }
                }
            });
        });
         
        // Clear Filters for Export
        $('#clearPublicFiltersButton').on('click', function() {

            $('.selectpicker').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
        });

        // View edit page
        $('#publicStructureTable').on('click', '.updatePublicStructure',function() {
            var id = $(this).data('id');
            var url = window.location.href; 
           
            url = url +'/'+ id +'/edit';
            window.open(url, "_self"); 
        });

        // Delete record
        $('#publicStructureTable').on('click', '.deletePublicStructure',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this public?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "<?php echo e(route('deletePublicStructure')); ?>",
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
                                    $('#publicStructureTable').DataTable().draw();
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

        // Open meter history when clicking the meter-number anchor
        $('#publicStructureTable').on('click', 'a.meter-number-link', function(e) {
            
            e.preventDefault();
            var meterNumber = ($(this).data('meter-number') || $(this).text() || '').toString().trim();
            meterNumber = meterNumber.replace(/\\s+/g, '');
            if (!meterNumber) return;
            if (typeof openMeterHistory === 'function') {
                openMeterHistory(meterNumber);
            } else {
                console.warn('openMeterHistory is not available');
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/public/index.blade.php ENDPATH**/ ?>