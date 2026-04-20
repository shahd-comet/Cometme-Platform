<?php

    $pricingModal = true;

    $isFbs = false;

    if (isset($energyUser->energy_system_type_id) && $energyUser->energy_system_type_id == 2) {

        $isFbs = true;

    }

?>







<?php $__env->startSection('title', 'edit energy user'); ?>



<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<style>

    label, input {



        display: block;

    }



    label {



        margin-top: 20px;

    }



</style>



<?php $__env->startSection('content'); ?>

<h4 class="py-3 breadcrumb-wrapper mb-4">

    <span class="text-muted fw-light">Edit </span> <?php echo e($energyUser->Household->english_name); ?>


    <span class="text-muted fw-light">Information </span> 

</h4>

 

<?php if(session('error')): ?>

    <div class="alert alert-danger">

        <?php echo e(session('error')); ?>


    </div>

<?php endif; ?>



<div class="card">

    <div class="card-content collapse show">

        <div class="card-body">

            <form method="POST" action="<?php echo e(route('all-meter.update', $energyUser->id)); ?>"

             enctype="multipart/form-data" >

                <?php echo csrf_field(); ?>

                <?php echo method_field('PATCH'); ?>

                

                <div class="row">

                    <h5>General Details</h5>

                </div>

                

                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Community</label>

                            <select id="selectedCommunityFbs" class="selectpicker form-control" name="community_id" data-live-search="true">

                                <option value="<?php echo e($energyUser->Community->id); ?>" selected><?php echo e($energyUser->Community->english_name); ?></option>

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

                            <label class='col-md-12 control-label'>Cycle Year</label>

                            <select name="energy_system_cycle_id" data-live-search="true"

                            class="selectpicker form-control" >

                            <?php if($energyUser->energy_system_cycle_id): ?>

                                <option disabled selected>

                                    <?php echo e($energyUser->EnergySystemCycle->name); ?>


                                </option>

                                <?php $__currentLoopData = $energyCycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energyCycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($energyCycle->id); ?>">

                                    <?php echo e($energyCycle->name); ?>


                                </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php else: ?>

                            <option disabled selected>Choose one...</option>

                                <?php $__currentLoopData = $energyCycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energyCycle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <option value="<?php echo e($energyCycle->id); ?>">

                                    <?php echo e($energyCycle->name); ?>


                                </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php endif; ?>

                            </select>

                        </fieldset>

                    </div>

                </div>



                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Meter Number</label>

                            <input type="text" class="form-control" name="meter_number"

                                value="<?php echo e($energyUser->meter_number); ?>" id="updatedMeterNumber"

                                maxlength="11" oninput="validateMeterNumber()"> 

                                <small id="meterError" class="text-danger" style="display: none;">

                                    Meter number must be 11 digits and not already exist.

                                </small>

                        </fieldset>

                    </div> 

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Daily limit</label> 

                            <input type="text" class="form-control" name="daily_limit"

                                value="<?php echo e($energyUser->daily_limit); ?>"> 

                        </fieldset> 

                    </div>

                </div>



                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Installation date</label>

                            <input type="date" class="form-control" name="installation_date" 

                            value="<?php echo e($energyUser->installation_date); ?>"> 

                        </fieldset>

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Meter Active</label> 

                            <select name='meter_active'  data-live-search="true"

                            class="selectpicker form-control">

                                <option selected disabled>

                                    <?php echo e($energyUser->meter_active); ?>


                                </option>

                                <option value="Yes">Yes</option>

                                <option value="No">No</option>

                            </select> 

                        </fieldset> 

                    </div>

                </div>

                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label' for="region_id">Meter Case</label>

                            <select name='meter_case_id' data-live-search="true"

                            class="selectpicker form-control">

                                <option disabled selected>

                                    <?php echo e($energyUser->MeterCase->meter_case_name_english); ?>


                                </option>

                                <?php $__currentLoopData = $meterCases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meterCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($meterCase->id); ?>">

                                        <?php echo e($meterCase->meter_case_name_english); ?>


                                    </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select> 

                        </fieldset> 

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6 text-info">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Updated date (only for meter case)</label>

                            <input type="date" class="form-control text-info" name="last_update_date" 

                            > 

                        </fieldset>

                    </div> 

                </div>



                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Energy System Type</label> 

                            <select name='energy_system_type_id'  data-live-search="true"

                            class="selectpicker form-control">

                                <option selected disabled>

                                    <?php echo e($energyUser->EnergySystem->EnergySystemType->name); ?>


                                </option>

                                <?php $__currentLoopData = $energySystemTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energySystemType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($energySystemType->id); ?>"><?php echo e($energySystemType->name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select> 

                        </fieldset> 

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Energy System</label> 

                            <select name='energy_system_id'  data-live-search="true"

                            class="selectpicker form-control">

                                <option selected disabled>

                                    <?php echo e($energyUser->EnergySystem->name); ?>


                                </option>

                                <?php $__currentLoopData = $energySystems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energySystem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($energySystem->id); ?>"><?php echo e($energySystem->name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select> 

                        </fieldset> 

                    </div>

  

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Installation Type</label>

                            <select name='installation_type_id'  data-live-search="true"

                            class="selectpicker form-control">

                                <option value="">

                                    <?php echo e($energyUser->InstallationType->type); ?>


                                </option>

                                <?php $__currentLoopData = $installationTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installationType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($installationType->id); ?>">

                                        <?php echo e($installationType->type); ?>


                                    </option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>



                            <?php if($isFbs): ?>

                                <div class="row mt-2" name="sharedUsers" >

                                    <div class="col-12">

                                        <div class="card p-3 mb-3" style="background:#f8f9fa; border:1px solid #e0e0e0;">

                                            <div class="form-check mb-2">

                                                <input class="form-check-input" type="checkbox" value="1" id="isMeterSharedFbs" name="is_meter_shared" <?php echo e(old('is_meter_shared', isset($energyUser->is_meter_shared) ? $energyUser->is_meter_shared : 0) ? 'checked' : ''); ?>>

                                                <span class="text-muted ms-2">this meter is shared with other households</span>

                                            </div>

                                            <div class="row align-items-end shared-meter-row-fbs" style="display:none;">

                                                <div class="col-md-4 mb-2 mb-md-0">

                                                    <label class="form-label">How many shared users?</label>

                                                    <input type="number" id="sharedUsersCountFbs" min="1" class="form-control" placeholder="Enter number of shared households" >

                                                </div>

                                                <div class="col-md-8">

                                                    <label class="form-label">Shared households</label>

                                                    <div id="sharedUsersListFbs" class="mb-2">

                                                        <!-- shared users as list items -->

                                                    </div>



                                                    <label class="form-label">Add a shared household</label>

                                                    <select id="addSharedUserSelectFbs" class="selectpicker form-control" data-live-search="true">

                                                        <option value="" selected disabled>Choose household to add...</option>

                                                    </select>

                                                    <small class="form-text text-muted">Select a household from the dropdown to append it to the list.</small>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            <?php endif; ?>

                        </fieldset> 

                    </div>

                </div> 

                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Ground Connected</label> 

                            <select name='ground_connected'  data-live-search="true"

                            class="selectpicker form-control">

                                <option selected disabled>

                                    <?php echo e($energyUser->ground_connected); ?>


                                </option>

                                <option value="Yes">Yes</option>

                                <option value="No">No</option>

                            </select> 

                        </fieldset> 

                    </div>

                </div> 

                

                <div class="row">

                    <div class="col-xl-12 col-lg-12 col-md-12">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>Notes</label> 

                            <textarea class="form-control" name="notes" style="resize: none;">

                                <?php echo e($energyUser->notes); ?>


                            </textarea>

                        </fieldset> 

                    </div>

                </div> 

                <hr>



                <div class="row">

                    <h5>CI & PH</h5>

                </div>



                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>CI</label> 

                            <select name='electricity_collection_box_id' data-live-search="true"

                            class="selectpicker form-control">

                                <?php if($allEnergyMeterPhase): ?>

                                <option selected disabled>

                                    <?php echo e($allEnergyMeterPhase->ElectricityCollectionBox->name); ?>


                                </option>

                                <?php $__currentLoopData = $electricityCollectionBoxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $electricityCollectionBox): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($electricityCollectionBox->id); ?>"><?php echo e($electricityCollectionBox->name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php else: ?>

                                

                                <option selected disabled>Choose one...</option>

                                <?php $__currentLoopData = $electricityCollectionBoxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $electricityCollectionBox): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($electricityCollectionBox->id); ?>"><?php echo e($electricityCollectionBox->name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php endif; ?>

                            </select> 

                        </fieldset> 

                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6">

                        <fieldset class="form-group">

                            <label class='col-md-12 control-label'>PH (L)</label> 

                            <select name='electricity_phase_id' data-live-search="true"

                            class="selectpicker form-control">

                                <?php if($allEnergyMeterPhase): ?>

                                <option selected disabled>

                                    <?php echo e($allEnergyMeterPhase->ElectricityPhase->name); ?>


                                </option>

                                <?php $__currentLoopData = $electricityPhases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $electricityPhasE): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($electricityPhasE->id); ?>"><?php echo e($electricityPhasE->name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php else: ?>

                                

                                <option selected disabled>Choose one...</option>

                                <?php $__currentLoopData = $electricityPhases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $electricityPhasE): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <option value="<?php echo e($electricityPhasE->id); ?>"><?php echo e($electricityPhasE->name); ?></option>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <?php endif; ?>

                            </select> 

                        </fieldset> 

                    </div>

                </div> 

                

                <hr>
                <div class="row">
                    <h5>Old Donors</h5>
                </div>
                <?php if(count($energyDonors) > 0): ?>

                    <table id="energyDonorsTable" class="table table-striped data-table-energy-donors my-2">
                        
                        <tbody>
                            <?php $__currentLoopData = $energyDonors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energyDonor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr id="energyDonorRow">
                                <td class="text-center">
                                    <?php echo e($energyDonor->Donor->donor_name); ?>

                                </td>
                                <td class="text-center">
                                    <a class="btn deleteEnergyDonor" id="deleteEnergyDonor" data-id="<?php echo e($energyDonor->id); ?>">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Add more donors</label>
                                <select class="selectpicker form-control" 
                                    multiple data-live-search="true" name="donors[]">
                                    <option selected disabled>Choose one...</option>
                                    <?php $__currentLoopData = $moreDonors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moreDonor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($moreDonor->id); ?>">
                                            <?php echo e($moreDonor->donor_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                <?php else: ?> 
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Add Donors</label>
                                <select class="selectpicker form-control" 
                                    multiple data-live-search="true" name="new_donors[]">
                                    <option selected disabled>Choose one...</option>
                                    <?php $__currentLoopData = $donors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($donor->id); ?>"><?php echo e($donor->donor_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                <?php endif; ?>


                <hr>
                <div class="row">
                    <h5>New Donors</h5>
                </div>
                <?php if(count($energyNewDonors) > 0): ?>

                    <table id="energyNewDonorsTable" class="table table-striped data-table-energy-donors my-2">
                        
                        <tbody>
                            <?php $__currentLoopData = $energyNewDonors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $energyNewDonor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr id="energyDonorRow">
                                <td class="text-center">
                                    <?php echo e($energyNewDonor->Donor->donor_name); ?>

                                </td>
                                <td class="text-center">
                                    <a class="btn deleteEnergyNewDonor" id="deleteEnergyNewDonor" data-id="<?php echo e($energyNewDonor->id); ?>">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Add more donors</label>
                                <select class="selectpicker form-control" 
                                    multiple data-live-search="true" name="ndonors[]">
                                    <option selected disabled>Choose one...</option>
                                    <?php $__currentLoopData = $moreNewDonors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moreNewDonor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($moreNewDonor->id); ?>">
                                            <?php echo e($moreNewDonor->donor_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                <?php else: ?> 
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Add Donors</label>
                                <select class="selectpicker form-control" 
                                    multiple data-live-search="true" name="new_ndonors[]">
                                    <option selected disabled>Choose one...</option>
                                    <?php $__currentLoopData = $donors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($donor->id); ?>"><?php echo e($donor->donor_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                <?php endif; ?>



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



<script type="text/javascript">



    // check the critera for the meter number

    function validateMeterNumber() {



        var meterNumber = document.getElementById("updatedMeterNumber").value;

        var errorElement = document.getElementById("meterError");



        // Check if the meter number has exactly 11 digits

        if (meterNumber.length > 11) {



            errorElement.style.display = 'block';

            errorElement.innerText = 'Meter number cannot exceed 11 digits.';

        } else if (meterNumber.length < 11) {



            errorElement.style.display = 'block';

            errorElement.innerText = 'Meter number cannot be less than 11 digits.';

        } else {



            errorElement.style.display = 'none';

        }

    }



    $(function () {

        // delete energy donor

        $('#energyDonorsTable').on('click', '.deleteEnergyDonor',function() {

            var id = $(this).data('id');

            var $ele = $(this).parent().parent();



            Swal.fire({

                icon: 'warning',

                title: 'Are you sure you want to delete this donor?',

                showDenyButton: true,

                confirmButtonText: 'Confirm'

            }).then((result) => {

                if(result.isConfirmed) {

                    $.ajax({

                        url: "<?php echo e(route('deleteEnergyDonor')); ?>",

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

    });


    // delete energy donor
    $('#energyNewDonorsTable').on('click', '.deleteEnergyNewDonor',function() {
        var id = $(this).data('id');
        var $ele = $(this).parent().parent();

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure you want to delete this donor?',
            showDenyButton: true,
            confirmButtonText: 'Confirm'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: "<?php echo e(route('deleteEnergyNewDonor')); ?>",
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
    
</script>



<script src="/js/energy-request.js"></script>



<script>

    // Shared-users list UI for FBS

    (function(){

        var initialShared = <?php echo json_encode($sharedHouseholdIds ?? [], 15, 512) ?>;

        var initialSharedMap = <?php echo json_encode($sharedHouseholdMap ?? [], 15, 512) ?>;



        function updateSharedCount() {

            var cnt = $('#sharedUsersListFbs').find('.shared-user-item').length;

            $('#sharedUsersCountFbs').val(cnt);

        }



        function addSharedListItem(id, name, original) {

            if (!id) return;

            if ($('#sharedUsersListFbs').find('.shared-user-item[data-id="' + id + '"]').length) return;

            var $item = $('<div class="shared-user-item d-flex align-items-start mb-2" data-id="'+id+'"></div>');

            var $left = $('<div class="flex-grow-1"></div>');

            var $name = $('<div class="fw-semibold">' + (name || id) + '</div>');

            // info area to display meter/request/main-holder text

            var $info = $('<div class="shared-user-info small text-muted mt-1"></div>');

            $left.append($name).append($info);



            var $controls = $('<div class="ms-2 text-nowrap"></div>');

            var $remove = $('<button type="button" class="btn btn-sm btn-outline-danger remove-shared-item" title="Remove"><i class="fa fa-times"></i></button>');

            $controls.append($remove);



            // mark original ones so removals create remove_shared_fbs[] on submit

            if (original) $item.attr('data-original', '1');



            // hidden input to submit current shared list

            var $hidden = $('<input type="hidden" name="shared_users_fbs[]" value="'+id+'">');

            $item.append($left).append($controls).append($hidden);



            $('#sharedUsersListFbs').append($item);

            if (typeof $('.selectpicker').selectpicker === 'function') $('.selectpicker').selectpicker('refresh');

            updateSharedCount();



            // fetch and render household info only for newly added items (not originals)

            if (!original) {

                $.ajax({

                    url: '/energy_user/get_by_household/' + id,

                    method: 'GET',

                    success: function(data) {

                        var text = '';

                        if (!data) {

                            text = 'No details available';

                        } else if (data.meter_number && data.meter_number !== '' && data.meter_number !== 'No') {

                            text = '<span class="text-danger">Household has a meter: <strong>' + data.meter_number + '</strong></span>';

                        } else if (data.main_holder) {

                            text = '<span class="text-warning">Previously shared with <strong>' + data.main_holder + '</strong></span>';

                        } else if (data.is_requested) {

                            var referredBy = data.referred_by ? data.referred_by : 'Unknown';

                            text = '<span class="text-info">Requested (referred by <strong>' + referredBy + '</strong>)</span>';

                        } else {

                            var display = (data.meter_number && data.meter_number !== '') ? data.meter_number : 'None';

                            text = '<span class="text-primary">Shared household meter: <strong>' + display + '</strong></span>';

                        }

                        $info.html(text);

                    },

                    error: function() {

                        $info.html('<span class="text-danger">Could not fetch household info.</span>');

                    }

                });

            }

        }



        function removeSharedListItem(id, $element) {

            if ($element && $element.length) {

                var wasOriginal = $element.attr('data-original') === '1';

                if (wasOriginal) {

                    // create an input to tell server to remove this household link

                    var $rm = $('<input type="hidden" name="remove_shared_fbs[]" value="'+id+'">');

                    $('#sharedUsersListFbs').append($rm);

                }

                $element.remove();

                updateSharedCount();

            }

        }



        // initialize existing shared items

        if (initialShared && initialShared.length > 0) {

            $('#isMeterSharedFbs').prop('checked', true);

            $('.shared-meter-row-fbs').show();

            for (var i=0;i<initialShared.length;i++) {

                var hid = initialShared[i];

                var label = initialSharedMap[hid] || hid;

                addSharedListItem(hid, label, true);

            }

        }



        // wire remove handler

        $(document).on('click', '.remove-shared-item', function(e){

            var $parent = $(this).closest('.shared-user-item');

            var id = $parent.data('id');

            removeSharedListItem(id, $parent);

        });



        // populate add-select when community changes or on load

        function refreshAddSelect() {

            var communityId = $('#selectedCommunityFbs').val();

            var mainId = $('#selectedEnergyUserFbs').val();

            fetchHouseholdsIfNeededFbs(communityId, function(optionsHtml){

                if (!optionsHtml) return;

                var $tmp = $('<select></select>').html(optionsHtml);

                var $add = $('#addSharedUserSelectFbs');

                $add.empty();

                $add.append('<option value="" selected disabled>Choose household to add...</option>');

                $tmp.find('option').each(function(){

                    var v = $(this).attr('value');

                    var t = $(this).text();

                    if (!v) return;

                    if (String(v) === String(mainId)) return;

                    // skip already in list

                    if ($('#sharedUsersListFbs').find('.shared-user-item[data-id="'+v+'"]').length) return;

                    $add.append('<option value="'+v+'">'+t+'</option>');

                });

                if (typeof $add.selectpicker === 'function') $add.selectpicker('refresh');

            });

        }



        $('#selectedCommunityFbs').on('change', function(){

            refreshAddSelect();

        });



        // when an item is chosen from add-select, append to list

        $(document).on('change', '#addSharedUserSelectFbs', function(){

            var v = $(this).val();

            var txt = $('#addSharedUserSelectFbs option:selected').text();

            if (!v) return;

            addSharedListItem(v, txt, false);

            // remove option from add-select

            $(this).find('option[value="'+v+'"]').remove();

            if (typeof $(this).selectpicker === 'function') $(this).selectpicker('refresh');

        });



        // if decreased, remove extras from end; if increased, just update value 

        $(document).on('input', '#sharedUsersCountFbs', function(){

            var desired = parseInt($(this).val() || 0);

            if (!desired || desired < 0) return;

            var current = $('#sharedUsersListFbs').find('.shared-user-item').length;

            if (desired < current) {

                var toRemove = current - desired;

                var $items = $('#sharedUsersListFbs').find('.shared-user-item');

                for (var i=0;i<toRemove;i++) {

                    var $el = $($items[$items.length - 1 - i]);

                    var id = $el.data('id');

                    removeSharedListItem(id, $el);

                }

            }

            updateSharedCount();

        });



        $(function(){

            refreshAddSelect();

            $('#isMeterSharedFbs').on('change', function(){

                if ($(this).is(':checked')) {

                    $('.shared-meter-row-fbs').show();

                } else {

                    $('.shared-meter-row-fbs').hide();

                }

            });

        });

    })();

</script>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/users/energy/not_active/edit_energy.blade.php ENDPATH**/ ?>