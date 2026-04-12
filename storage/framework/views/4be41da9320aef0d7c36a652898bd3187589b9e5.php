

<?php $__env->startSection('title', 'Create Vendor'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<style>

    label, input {

        display: block;
    }

    label, table {

        margin-top: 20px;
    }
</style>

<?php $__env->startSection('content'); ?> 

<h4 class="py-3 breadcrumb-wrapper mb-4">

  <span class="text-muted fw-light">Add </span> New Vending Point

</h4> 

<div class="card">
    <div class="card-content collapse show">
        <div class="card-body">
            <form method="POST" enctype='multipart/form-data' id="vendingPointForm"
                action="<?php echo e(url('vending-point')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>English Name</label>
                            <input type="text" name="english_name" 
                            class="form-control" required>
                        </fieldset>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Arabic Name</label>
                            <input type="text" name="arabic_name" 
                            class="form-control" required>
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Phone Number</label>
                            <input type="number" name="phone_number" class="form-control" required>
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Additional Phone Number</label>
                            <input type="number" name="additional_phone_number" class="form-control">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Community/Town</label>
                            <select name="community_town" id="communityTownPlace" 
                                class="selectpicker form-control" required>
                                <option disabled selected>Choose one...</option>
                                <option value="community">Community</option> 
                                <option value="town">Town</option>
                            </select>
                        </fieldset>
                        <div id="community_town_error" style="color: red;"></div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Vending Point Place</label>
                            <select name="community_town_id" id="communityTownVendingPoint" 
                                class="selectpicker form-control" 
                                data-live-search="true">
                            </select>
                        </fieldset>
                        <div id="community_town_id_error" style="color: red;"></div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Region</label>
                            <select name="vendor_region_id" id="vendorRegion" 
                                class="selectpicker form-control" 
                                data-live-search="true">
                                <option disabled selected>Choose one...</option>
                                <?php $__currentLoopData = $vendorRegions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorRegion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendorRegion->id); ?>"><?php echo e($vendorRegion->english_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </fieldset>
                        <div id="vendor_region_id_error" style="color: red;"></div>
                    </div>
                </div>

                <hr>
                <label class="text-info" style="margin-bottom:15px">Vendor UserNames & Served Communities</label>

                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-12 mb-4">
                    <div class="card h-100 shadow-sm"> 
                        <div class="card-body">
                            <!-- Checkbox + Add Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input service-checkbox" type="checkbox" 
                                        id="serviceCheck<?php echo e($service->id); ?>" name="service_type_id[]"
                                        data-service-id="<?php echo e($service->id); ?>"  value="<?php echo e($service->id); ?>">
                                    <label class="form-check-label mb-0" for="serviceCheck<?php echo e($service->id); ?>">
                                        Activate Vendor UserName for:
                                        <span class="text-info"><?php echo e($service->service_name); ?></span>
                                    </label>
                                </div>
                                <button type="button" disabled
                                        class="btn btn-sm btn-success add-vendor-btn"
                                        data-service-id="<?php echo e($service->id); ?>">
                                    Add a new username
                                </button>
                            </div>

                            <!-- Row for Username + Served Communities -->
                            <div class="row g-2">
                                <!-- Vendor Username -->
                                <div class="col-md-6">
                                    <label class="form-label mb-1">Vendor UserName</label>
                                    <select name="vendor_user_name_id[<?php echo e($service->id); ?>][]" 
                                            class="vendingPointUsername selectpicker form-control" 
                                            data-live-search="true" 
                                            id="vendingPointUsername<?php echo e($service->id); ?>" 
                                            title="Select vendor usernames" 
                                            disabled>
                                        <?php $__currentLoopData = $vendorUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($vendorUser->id); ?>">
                                                <?php echo e($vendorUser->name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="vendor-username-error" style="color: red;"></div>
                                </div>

                                <!-- Served Communities -->
                                <div class="col-md-6">
                                    <label class="form-label mb-1">Served Communities</label>
                                    <select name="served_communities[<?php echo e($service->id); ?>][]" 
                                            class="selectpicker form-control" 
                                            data-live-search="true" 
                                            multiple 
                                            disabled>
                                        <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($community->id); ?>">
                                                <?php echo e($community->english_name); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="served-communities-error" style="color: red;"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Notes</label>
                            <textarea name="notes" class="form-control" 
                                style="resize:none" cols="20" rows="3"></textarea>
                        </fieldset>
                    </div>
                </div>

                <div class="row" style="margin-top:20px">
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <button type="submit" class="btn btn-primary">
                            Save changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<?php echo $__env->make('vendor.create-vendor-username', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>

    $(document).on('change', '#communityTownPlace', function () {

        communityTown = $('#communityTownPlace').val();
        $.ajax({
            url: "/vendor/community_town/" + communityTown,
            method: 'GET',
            success: function(data) {

                var select = $('#communityTownVendingPoint');
                select.prop('disabled', false); 
                select.html(data.html);
                select.selectpicker('refresh');
            }
        });
    });

    // Enable/disable select based on checkbox
    $(document).on('change', '.service-checkbox', function() {

        const serviceId = $(this).data('service-id');
        const usernameSelect = $('#vendingPointUsername' + serviceId);
        const communitiesSelect = $('select[name="served_communities[' + serviceId + '][]"]');
        const addBtn = $('.add-vendor-btn[data-service-id="' + serviceId + '"]');

        if ($(this).is(':checked')) {

            usernameSelect.prop('disabled', false);
            communitiesSelect.prop('disabled', false);
            addBtn.prop('disabled', false);
        } else {

            usernameSelect.prop('disabled', true).val(null).selectpicker('refresh');
            communitiesSelect.prop('disabled', true).val(null).selectpicker('refresh');
            addBtn.prop('disabled', true);
            $(this).closest('.card-body').find('.vendor-username-error').text('');
            $(this).closest('.card-body').find('.served-communities-error').text('');
        }

        usernameSelect.selectpicker('refresh');
        communitiesSelect.selectpicker('refresh');
    });

    $(document).on('click', '.add-vendor-btn', function() {

        const serviceId = $(this).data('service-id');
        $('#modalServiceId').val(serviceId);
        $('#newVendorUsername').val('');
        $('#addVendorUsernameModal').modal('show');
    });

    // This for adding a new vendor username
    $('#addVendorUsernameForm').on('submit', function(e) {
        e.preventDefault();

        const serviceId = $('#modalServiceId').val();
        const newUsername = $('#newVendorUsername').val().trim();

        if (!newUsername) {
            alert('Please enter a username.');
            return;
        }

        // Get the select element for this service
        const select = $('#vendingPointUsername' + serviceId);

        // Check if option already exists
        if (select.find("option[value='" + newUsername + "']").length === 0) {
            const newOption = new Option(newUsername, newUsername, true, true);
            select.append(newOption);
        }

        // Get currently selected values; force array
        let selectedValues = select.val();
        if (!Array.isArray(selectedValues)) {
            selectedValues = selectedValues ? [selectedValues] : [];
        }

        // Add new username
        selectedValues.push(newUsername);
        select.val(selectedValues);

        // Refresh Bootstrap Select
        select.selectpicker('refresh');

        // Close modal
        $('#addVendorUsernameModal').modal('hide');
    });
    
    $(document).ready(function() {

        $('#vendingPointForm').on('submit', function (event) {

            var vendorRegion = $('#vendorRegion').val();
            var communityTownValue = $('#communityTownPlace').val();
            var communityTownVendingPoint = $('#communityTownVendingPoint').val();

            if (communityTownValue == null) {

                $('#community_town_error').html('Please select an option!'); 
                return false;
            } else if (communityTownValue != null){

                $('#community_town_error').empty();
            }

            if (communityTownVendingPoint == null) {

                $('#community_town_id_error').html('Please select an option!'); 

                return false;

            } else if (communityTownVendingPoint != null){

                $('#community_town_id_error').empty();
            }


            if (vendorRegion == null) {

                $('#vendor_region_id_error').html('Please select a region!'); 

                return false;
            } else if (vendorRegion != null){

                $('#vendor_region_id_error').empty();
            }


            let valid = true;

            $('.service-checkbox').each(function() {

                const serviceId = $(this).data('service-id');
                const usernameSelect = $('#vendingPointUsername' + serviceId);
                const communitiesSelect = $('select[name="served_communities[' + serviceId + '][]"]');
                const card = $(this).closest('.card-body');

                if ($(this).is(':checked')) {
                    if (!usernameSelect.val() || usernameSelect.val().length === 0) {
                        valid = false;
                        card.find('.vendor-username-error').text('Please select at least one username.');
                    } else {
                        card.find('.vendor-username-error').text('');
                    }

                    if (!communitiesSelect.val() || communitiesSelect.val().length === 0) {
                        valid = false;
                        card.find('.served-communities-error').text('Please select at least one community.');
                    } else {
                        card.find('.served-communities-error').text('');
                    }
                } else {
                    card.find('.vendor-username-error').text('');
                    card.find('.served-communities-error').text('');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Please fill all required fields for the checked services.');
            }
        

            $(this).addClass('was-validated');  
            $('#vendor_region_id_error').empty();  
            $('#vendor_user_name_id_error').empty();
            $('#community_town_error').empty();
            $('#community_town_id_error').empty();
            $('#vendor_username_id_error').empty();

            this.submit();
        });
    });

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/vendor/create.blade.php ENDPATH**/ ?>