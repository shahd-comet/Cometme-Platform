

<?php $__env->startSection('title', 'edit vending point'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<style>

    label, input {

        display: block;
    }

    label {

        margin-top: 20px;
    }

    .selected-communities {
        display: flex;
        flex-wrap: wrap; /* allows wrapping if too many items */
        gap: 5px; /* space between items */
    }

    .community-item {
        display: flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 5px;
        background-color: #f1f1f1;
        margin: 0; /* remove vertical margin, gap is handled by flex */
        font-size: 0.9em;
    }
    .community-item button {
        margin-left: 5px; /* space between text and delete button */
        padding: 0 5px;
    }
</style>

<?php $__env->startSection('content'); ?>

<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Edit </span> 
        <?php echo e($vendingPoint->english_name); ?> - 
    <span class="text-muted fw-light">Information </span> 
</h4>


<div class="card">
    <div class="card-content collapse show">
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('vending-point.update', $vendingPoint->id)); ?>"
                enctype="multipart/form-data" >
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>English Name</label>
                            <input type="text" name="english_name" 
                            class="form-control" value="<?php echo e($vendingPoint->english_name); ?>">
                        </fieldset>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Arabic Name</label>
                            <input type="text" name="arabic_name" 
                            class="form-control" value="<?php echo e($vendingPoint->arabic_name); ?>">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" 
                                value="<?php echo e($vendingPoint->phone_number); ?>">
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Additional Phone Number</label>
                            <input type="text" name="additional_phone_number" class="form-control"
                                value="<?php echo e($vendingPoint->additional_phone_number); ?>">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Community/Town</label>
                            <select name="community_town" id="communityTownPlace" 
                                class="selectpicker form-control" required>
                                <?php if($vendingPoint->community_id): ?>
                                    <option disabled selected>Community</option>
                                <?php else: ?> <?php if($vendingPoint->town_id): ?>
                                    <option disabled selected>Town</option>
                                <?php endif; ?>
                                <?php endif; ?>
                                    
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
                                <?php if($vendingPoint->community_id): ?>
                                    <option disabled selected><?php echo e($vendingPoint->Community->english_name); ?></option>
                                    <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($community->id); ?>"><?php echo e($community->english_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?> <?php if($vendingPoint->town_id): ?>
                                    <option disabled selected><?php echo e($vendingPoint->Town->english_name); ?></option>
                                    <?php $__currentLoopData = $towns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $town): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($town->id); ?>"><?php echo e($town->english_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                <?php endif; ?>
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
                                <?php if($vendingPoint->vendor_region_id): ?>
                                <option disabled selected><?php echo e($vendingPoint->VendorRegion->english_name); ?></option>
                                <?php $__currentLoopData = $vendorRegions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorRegion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendorRegion->id); ?>"><?php echo e($vendorRegion->english_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                <option disabled selected>Choose one...</option>
                                <?php $__currentLoopData = $vendorRegions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorRegion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vendorRegion->id); ?>"><?php echo e($vendorRegion->english_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </fieldset>
                        <div id="vendor_region_id_error" style="color: red;"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Notes</label>
                            <textarea name="notes" class="form-control" 
                                style="resize:none" cols="20" rows="3">
                                <?php echo e($vendingPoint->notes); ?>

                            </textarea>
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

    // This function is for showing the place (town or community)
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



    // When clicking "Add New Username"
    $(document).on('click', '.open-add-username-modal', function() {

        let serviceId = $(this).data('service-id');
        $('#modalServiceId').val(serviceId); 
        $('#newVendorUsername').val('');     
        $('#addVendorUsernameModal').modal('show');
    });

    $('#addVendorUsernameForm').on('submit', function(e) {
        e.preventDefault();

        let serviceId = $('#modalServiceId').val();
        let username = $('#newVendorUsername').val();

        // Create a temporary ID for the new username
        let tempId = 'temp_' + Date.now();

        // Append the new username to the corresponding select
        let $select = $(`.vendor-user-entry select[name='vendor_user_name_id[${serviceId}][]']`).last();
        $select.append(`<option value="${tempId}" selected>${username}</option>`);
        $select.selectpicker('refresh');

        // Close modal
        $('#addVendorUsernameModal').modal('hide');

        // Optionally, store the temporary new username in a hidden field so it can be processed on the backend
        if($('#newVendorUsernames').length === 0) {
            // Create container if it doesn't exist
            $('form').append('<input type="hidden" id="newVendorUsernames" name="new_vendor_usernames" value="">');
        }

        let existing = $('#newVendorUsernames').val();
        let newEntry = existing ? JSON.parse(existing) : {};
        if(!newEntry[serviceId]) newEntry[serviceId] = [];
        newEntry[serviceId].push({id: tempId, name: username});
        $('#newVendorUsernames').val(JSON.stringify(newEntry));
    });


    // Delete Community 
    $(document).on('click', '.remove-community', function () {

        var $ele = $(this).closest('.community-item');
        var id = $ele.data('id'); 

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure you want to delete this served community?',
            showDenyButton: true,
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: "<?php echo e(route('deleteServedCommunity')); ?>",
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
                                $ele.fadeOut(1000, function () {
                                    $ele.remove();
                                });
                            });
                        } 
                    }
                });
            } else if (result.isDenied) {

                Swal.fire('Changes are not saved', '', 'info')
            }
        });
    });

    // Add a new vendor username with served communities
    $(document).on('click', '.add-vendor-btn', function() {

        let serviceId = $(this).data('service-id');
        let container = $('.existing-user-container[data-service-id="'+serviceId+'"]');
        let tempUserId = 'new_' + Date.now();

        let newEntry = `
        <div class="vendor-user-entry border rounded p-2 mb-2 box-shadow" style="background-color: #d4edda;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div style="flex:1; margin-right:10px;">
                    <select name="vendor_user_name_id[${serviceId}][]" class="selectpicker form-control" data-live-search="true">
                        <option disabled selected>Select vendor username</option>
                        <?php $__currentLoopData = $vendorUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendorUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($vendorUser->id); ?>"><?php echo e($vendorUser->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-vendor-user">Delete New Username</button>
            </div>
            <div>
                <label class="mb-1">Served Communities</label>
                <div class="d-flex gap-2 mb-2">
                    <select class="selectpicker form-control community-select" multiple data-service-id="${serviceId}" data-user-id="${tempUserId}">
                        <option disabled selected>Select community</option>
                        <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($community->id); ?>"><?php echo e($community->english_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="selected-communities"></div>
            </div>
        </div>`;

        let $entry = $(newEntry);
        container.append($entry);
        $('.selectpicker').selectpicker('refresh');

        // Animate the background color to fade out to normal
        $entry.animate({ backgroundColor: "#ffffff" }, 20000);
    });

    // 3. Add community to a username
    $(document).on('click', '.add-community-btn', function() {
        let parent = $(this).closest('.vendor-user-entry');
        let select = parent.find('.community-select');
        let selected = select.val();

        if(selected) {
            selected.forEach(function(val){
                // Prevent duplicates
                if(parent.find(`.selected-communities input[value="${val}"]`).length === 0){
                    let text = select.find('option[value="'+val+'"]').text();
                    let userId = select.data('user-id');
                    let serviceId = select.data('service-id');

                    let html = `
                    <div class="community-item d-flex justify-content-between align-items-center mb-1 border rounded px-2 py-1">
                        <span>${text}</span>
                        <button type="button" class="btn btn-sm btn-danger remove-community">Delete</button>
                        <input type="hidden" name="served_communities[${serviceId}][${userId}][]" value="${val}">
                    </div>`;
                    parent.find('.selected-communities').append(html);
                }
            });
            select.val(null).selectpicker('refresh');
        }
    });

</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/vendor/edit.blade.php ENDPATH**/ ?>