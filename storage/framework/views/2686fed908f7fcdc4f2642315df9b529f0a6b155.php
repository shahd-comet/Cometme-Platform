<style>
    label, input {
        display: block;
    }

    label, table {
        margin-top: 20px;
    } 
</style>  

<div id="createCommunityCamera" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content"> 
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                    Create New Installed Camera
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            <div class="modal-body"> 
                <form method="POST" enctype='multipart/form-data' id="cameraForm"
                    action="<?php echo e(url('camera')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="row" style="margin-top:12px">
                        <h6>Select Community or Repository</h6>
                    </div>
                    <div id="community_id_error" style="color: red;"></div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Community</label>
                                <select class="selectpicker form-control" id="communityCamera"
                                    data-live-search="true" name="community_id" required>
                                    <option disabled selected>Choose one...</option>
                                    <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($community->id); ?>">
                                        <?php echo e($community->english_name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div> 
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Repository</label>
                                <select class="selectpicker form-control" id="RepositoryCamera"
                                    data-live-search="true" name="repository_id" required>
                                    <option disabled selected>Choose one...</option>
                                    <?php $__currentLoopData = $repositories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $repository): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($repository->id); ?>">
                                        <?php echo e($repository->name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div> 
                        <div class="col-xl-6 col-lg-6 col-md-6" id="compoundField">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Compound</label>
                                <select id="compound_id_select" name="compound_id" class="selectpicker form-control" data-live-search="true">
                                    <option value="" selected disabled>Choose Compound...</option>
                                    <?php $__currentLoopData = $compounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($compound->id); ?>" data-community="<?php echo e($compound->community_id); ?>">
                                            <?php echo e($compound->english_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Date Of Installation</label>
                                <input type="date" name="date" class="form-control" required>
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Responsible?</label>
                                <select name="household_id" id="householdResponsible"
                                    class="selectpicker form-control" data-live-search="true">
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Ci4</label>
                                <input type="number" name="ci4" class="form-control" 
                                    placeholder="Ci4" min="0">
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Electricity cable length in meters</label>
                                <input type="number" name="electricity_cable_number" class="form-control" 
                                    placeholder="Electricity cable length in meters" min="0" step="0.1">
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Camera Accessories Number</label>
                                <input type="text" name="camera_accessories_number" class="form-control" 
                                    placeholder="Camera Accessories Number">
                            </fieldset>
                        </div>
                    </div>

                    <hr>
                    <div class="row" style="margin-top:12px">
                        <h6>Cameras</h6>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                            <table class="table table-bordered" id="dynamicAddRemoveCamera">
                                <tr>
                                    <th>Camera Model</th>
                                    <th># of Camera</th>
                                    <th>SD Card Number</th>
                                    <th>Camera Base Number</th>
                                    <th>Internet cable length in meters</th>
                                    <th>Options</th>
                                </tr>
                                <tr> 
                                    <td>
                                        <select class="selectpicker form-control" 
                                            data-live-search="true" name="camera_id[]" required>
                                            <option disabled selected>Choose one...</option>
                                            <?php $__currentLoopData = $cameras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $camera): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($camera->id); ?>">
                                                <?php echo e($camera->model); ?>

                                            </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div id="camera_id_error" style="color: red;"></div>
                                    </td>
                                    <td>
                                        <input type="text" name="addMoreInputFieldsCameraNumber[0][subject]" 
                                        placeholder="# of Camera" class="target_point form-control" 
                                        data-id="0" required/>
                                    </td>
                                    <td>
                                        <input type="number" name="addMoreInputFieldsSdCard[0][subject]" 
                                        placeholder="SD Card Number" class="target_point form-control" 
                                        data-id="0"/>
                                    </td>
                                    <td>
                                        <input type="text" name="addMoreInputFieldsCameraBaseNumber[0][subject]" 
                                        placeholder="Camera Base Number" class="target_point form-control" 
                                        data-id="0"/>
                                    </td>
                                    <td>
                                        <input type="number" name="addMoreInputFieldsInternetCableNumber[0][subject]" 
                                        placeholder="Internet cable length in meters" class="target_point form-control" 
                                        data-id="0" min="0" step="0.1"/>
                                    </td>
                                    <td>
                                        <button type="button" name="add" id="addCameraForCommunityButton" 
                                        class="btn btn-outline-primary">
                                            Add More
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row" style="margin-top:12px">
                        <h6>NVR Cameras</h6>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                            <table class="table table-bordered" id="dynamicAddRemoveNvr">
                                <tr>
                                    <th>NVR Model</th>
                                    <th># of NVR</th>
                                    <th>IP Address</th>
                                    <th>Options</th>
                                </tr>
                                <tr> 
                                    <td>
                                        <select class="selectpicker form-control"
                                            data-live-search="true" name="nvr_id[]">
                                            <option disabled selected>Choose one...</option>
                                            <?php $__currentLoopData = $nvrCameras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nvrCamera): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($nvrCamera->id); ?>">
                                                <?php echo e($nvrCamera->model); ?>

                                            </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <div id="nvr_id_error" style="color: red;"></div>
                                    </td>
                                    <td>
                                        <input type="text" name="addMoreInputFieldsNvrNumber[0][subject]" 
                                        placeholder="# of NVR" class="target_point form-control" 
                                        data-id="0" />
                                    </td>
                                    <td>
                                        <input type="text" name="addMoreInputFieldsNvrIpAddress[0][subject]" 
                                        placeholder="IP Address" class="target_point form-control" 
                                        data-id="0" />
                                    </td>
                                    <td>
                                        <button type="button" name="add" id="addNvrForCommunityButton" 
                                        class="btn btn-outline-primary">
                                            Add More
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                  
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Notes</label>
                                <textarea name="notes" class="form-control" 
                                   style="resize:none" cols="20" rows="3"></textarea>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Upload photos</label>
                            <input type="file" name="photos[]"
                                class="btn btn-primary me-2 mb-4 block w-full mt-1 rounded-md"
                                accept="image/png, image/jpeg, image/jpg, image/gif" multiple/>
                        </fieldset>
                        <p class="mb-0">Allowed JPG, JPEG, GIF or PNG.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function() {

        $(document).on('change', '#communityCamera', function () {

            $('#householdResponsible').empty();

            community_id = $(this).val();
            $.ajax({
                url: "progress-household/household/get_by_community/" +  community_id,
                method: 'GET',
                success: function(data) {
                    var select = $('#householdResponsible'); 

                    select.html(data.html);
                    select.selectpicker('refresh');
                }
            });
            // Load compounds for selected community via AJAX
            $.ajax({
                url: "compounds/by-community/" + community_id,
                method: 'GET',
                success: function(compounds) {
                    var select = $('#compound_id_select');
                    var wrapper = $('#compoundField');
                    if (!Array.isArray(compounds) || compounds.length === 0) {
                        // no compounds: hide field and clear options
                        select.html('<option value="" selected disabled>Choose Compound...</option>');
                        select.prop('disabled', true);
                        // refresh selectpicker so UI updates
                        try { select.selectpicker('refresh'); } catch(e) {}
                        wrapper.hide();
                    } else {
                        var options = '<option value="" selected disabled>Choose Compound...</option>';
                        for (var i = 0; i < compounds.length; i++) {
                            var c = compounds[i];
                            options += '<option value="' + c.id + '">' + c.english_name + '</option>';
                        }
                        select.html(options);
                        select.prop('disabled', false);
                        wrapper.show();
                        // refresh selectpicker so options are rendered properly
                        try { select.selectpicker('refresh'); } catch(e) {}
                        if (compounds.length === 1) {
                            select.val(compounds[0].id);
                            try { select.selectpicker('refresh'); } catch(e) {}
                        }
                    }
                },
                error: function() {
                    // on error, hide compound field to avoid confusion
                    $('#compound_id_select').html('<option value="" selected disabled>Choose Compound...</option>').prop('disabled', true);
                    $('#compoundField').hide();
                }
            });
        });

        // Add More Cameras
        var j = 0;
        const cameras = <?php echo json_encode($cameras); ?>;
        $("#addCameraForCommunityButton").click(function () {
            ++j;

            let options = '<option disabled selected>Choose one...</option>';
            for (const cameraId in cameras) {
                const camera = cameras[cameraId];
                options += '<option value="' + camera.id + '">' + camera.model + '</option>';
            }                  

            $("#dynamicAddRemoveCamera").append('<tr><td><select class="selectpicker form-control"' + 
                'data-live-search="true" name="camera_id[]">' + options +
                '</select></td><td>' +
                '<input required type="text"' +
                'name="addMoreInputFieldsCameraNumber[][subject]" placeholder="# of Camera"' +
                'class="target_point form-control" data-id="'+ j +'" /></td><td>' +
                '<input type="number"' +
                'name="addMoreInputFieldsSdCard[][subject]" placeholder="SD Card Number"' +
                'class="target_point form-control" data-id="'+ j +'" /></td><td>' +
                '<input type="text"' +
                'name="addMoreInputFieldsCameraBaseNumber[][subject]" placeholder="Camera Base Number"' +
                'class="target_point form-control" data-id="'+ j +'" /></td><td>' +
                '<input type="number"' +
                'name="addMoreInputFieldsInternetCableNumber[][subject]" placeholder="Internet cable length in meters"' +
                'class="target_point form-control" data-id="'+ j +'" min="0" step="0.1"/></td><td><button type="button"' +
                'class="btn btn-outline-danger remove-input-field-target-points">Delete</button></td>' +
                '</tr>'
            );

            $(".selectpicker").selectpicker('refresh');
        });
        $(document).on('click', '.remove-input-field-target-points', function () {
            $(this).parents('tr').remove();
        });


        // Add More NVRs
        var i = 0;
        const nvrCameras = <?php echo json_encode($nvrCameras); ?>;
        $("#addNvrForCommunityButton").click(function () {
            ++i;

            let options = '<option disabled selected>Choose one...</option>';
            for (const nvrId in nvrCameras) {
                const nvr = nvrCameras[nvrId];
                options += '<option value="' + nvr.id + '">' + nvr.model + '</option>';
            }                  

            $("#dynamicAddRemoveNvr").append('<tr><td><select class="selectpicker form-control"' + 
                'data-live-search="true" name="nvr_id[]">' + options +
                '</select></td><td>' +
                '<input required type="text"' +
                'name="addMoreInputFieldsNvrNumber[][subject]" placeholder="# of NVR"' +
                'class="target_point form-control" data-id="'+ i +'" /></td><td><input type="text"'+ 
                'name="addMoreInputFieldsNvrIpAddress[][subject]" required placeholder="IP Address"'+
                'data-id="'+ i +'" class="target_point form-control"></td><td><button type="button"' +
                'class="btn btn-outline-danger remove-input-field-nvr">Delete</button></td>' +
                '</tr>'
            );

            $(".selectpicker").selectpicker('refresh');
        });
        $(document).on('click', '.remove-input-field-nvr', function () {
            $(this).parents('tr').remove();
        });
    });

    $(document).ready(function() {

        $('#cameraForm').on('submit', function (event) {

            var communityValue = $('#communityCamera').val();
            var repositoryValue = $('#RepositoryCamera').val();
            var cameraIds = $('select[name="camera_id[]"]').map(function() { 
                return $(this).val(); 
            }).get();
            // var nvrIds = $('select[name="nvr_id[]"]').map(function() { 
            //     return $(this).val(); 
            // }).get();

            if (communityValue == null && repositoryValue == null) {

                $('#community_id_error').html('Please select a community or repository!');
                return false;
            } else {

                $('#community_id_error').empty();
            }

            if (cameraIds.length === 0 || cameraIds.indexOf(null) !== -1) {

                $('#camera_id_error').html('Please select at least one camera!');
                return false;
            } else {

                $('#camera_id_error').empty();
            }

           
            $('#nvr_id_error').empty();

            $(this).addClass('was-validated'); 
            
            this.submit();
        });
    });
</script>

<?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/services/camera/create.blade.php ENDPATH**/ ?>