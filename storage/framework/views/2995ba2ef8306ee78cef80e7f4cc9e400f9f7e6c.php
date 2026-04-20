

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

                <hr>
                <label class="text-info mb-3">Vendor UserNames & Served Communities</label>
                
                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <?php
                        $serviceUsers = $vendorData[$service->id] ?? collect();
                    ?>

                    <div class="col-md-12 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">

                                <!-- SERVICE CHECKBOX -->
                                <div class="form-check mb-3">
                                    <input type="checkbox"
                                        class="form-check-input service-checkbox"
                                        name="service_type_id[]"
                                        value="<?php echo e($service->id); ?>"
                                        data-service-id="<?php echo e($service->id); ?>"
                                        <?php echo e($serviceUsers->count() ? 'checked' : ''); ?>>
                                    
                                    <label class="form-check-label">
                                        <?php echo e($service->service_name); ?>

                                    </label>
                                </div>

                                <!-- USERS -->
                                <div id="usersContainer<?php echo e($service->id); ?>">

                                    <?php if($serviceUsers->count()): ?>

                                        <?php $__currentLoopData = $serviceUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <?php
                                                $communitiesList = $vendorCommunities[$service->id][$user->vendor_user_name_id] ?? collect();
                                            ?>

                                            <div class="border p-2 mb-3">

                                                <label>Vendor Username</label>

                                                <select name="vendor_user_name_id[<?php echo e($service->id); ?>][]"
                                                    class="selectpicker form-control"
                                                    data-live-search="true">

                                                    <?php $__currentLoopData = $vendorUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($u->id); ?>"
                                                            <?php echo e($u->id == $user->vendor_user_name_id ? 'selected' : ''); ?>>
                                                            <?php echo e($u->name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>

                                                <label class="mt-2">Communities</label>

                                                <select name="served_communities[<?php echo e($service->id); ?>][<?php echo e($user->vendor_user_name_id); ?>][]"
                                                    class="selectpicker form-control community-select"
                                                    multiple
                                                    data-service="<?php echo e($service->id); ?>"
                                                    data-user="<?php echo e($user->vendor_user_name_id); ?>">

                                                    <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($community->id); ?>"
                                                            <?php echo e($communitiesList->pluck('community_id')->contains($community->id) ? 'selected' : ''); ?>>
                                                            <?php echo e($community->english_name); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>

                                                <div class="selected-communities mt-2"
                                                    id="tags<?php echo e($service->id); ?><?php echo e($user->vendor_user_name_id); ?>">
                                                </div>

                                            </div>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <?php else: ?>

                                        <!-- EMPTY STATE -->
                                        <div class="text-muted">
                                            No usernames added yet. Enable service to add one.
                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>
                        </div>
                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <script type="text/template" id="usernameTemplate">
                    <div class="border p-2 mb-3 username-block">
                        <label>Vendor Username</label>
                        <select class="selectpicker form-control username-select" data-live-search="true">
                            <option disabled selected>Choose username</option>
                            <?php $__currentLoopData = $vendorUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <label class="mt-2">Communities</label>
                        <select class="selectpicker form-control community-select" multiple data-live-search="true">
                            <option disabled selected>Choose communities</option>
                            <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>"><?php echo e($c->english_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>

                        <div class="selected-communities mt-2"></div>
                    </div>
                </script>

                <div id="usersContainer1"></div>


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


    function renderTags(serviceId, userId) {

        const select = $(`select[name="served_communities[${serviceId}][${userId}][]"]`);
        const container = $(`#tags${serviceId}${userId}`);

        container.empty();

        let selected = select.val() || [];

        if (!Array.isArray(selected)) {
            selected = [selected];
        }

        selected.forEach(id => {

            const text = select.find(`option[value="${id}"]`).text();

            container.append(`
                <div class="community-item">
                    ${text}
                    <button type="button"
                            class="btn btn-sm btn-danger remove-community"
                            data-service="${serviceId}"
                            data-user="${userId}"
                            data-id="${id}">
                        ×
                    </button>
                </div>
            `);
        });
    }

    // when dropdown changes
    $(document).on('changed.bs.select change', '.community-select', function () {

        const serviceId = $(this).data('service');
        const userId = $(this).data('user');

        renderTags(serviceId, userId);
    });

    // REMOVE BUTTON FIX
    $(document).on('click', '.remove-community', function () {

        const serviceId = $(this).data('service');
        const userId = $(this).data('user');
        const id = String($(this).data('id'));

        const select = $(`select[name="served_communities[${serviceId}][${userId}][]"]`);

        let values = select.val() || [];

        if (!Array.isArray(values)) {
            values = [values];
        }

        // IMPORTANT FIX: compare as strings
        values = values.map(String).filter(v => v !== id);

        select.val(values);

        // refresh bootstrap-select (CRITICAL)
        select.selectpicker('refresh');

        renderTags(serviceId, userId);
    });

    // INIT ON LOAD
    $(document).ready(function () {

        $('.community-select').each(function () {

            const serviceId = $(this).data('service');
            const userId = $(this).data('user');

            renderTags(serviceId, userId);
        });

    });

    // Enable/disable select based on checkbox
    $(document).on('change', '.service-checkbox', function () {
        const serviceId = $(this).data('service-id');
        const container = $('#usersContainer' + serviceId);

        container.empty();

        if ($(this).is(':checked')) {
            let templateHtml = $('#usernameTemplate').html();
            let newRow = $(templateHtml);

            newRow.addClass('username-block');

            // Set names dynamically
            newRow.find('.username-select')
                .attr('name', `vendor_user_name_id[${serviceId}][]`);

            newRow.find('.community-select')
                .attr('name', `served_communities[${serviceId}][new][]`)
                .attr('data-service', serviceId)
                .attr('data-user', 'new');

            container.append(newRow);

            newRow.find('.selectpicker').each(function () {
                $(this).selectpicker();
            });
        }
    });




</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/vendor/edit.blade.php ENDPATH**/ ?>