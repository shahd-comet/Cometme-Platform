<style>
    label, input {
        display: block;
    } 

    label, table {
        margin-top: 20px;
    }
</style>


<div id="createSubCommunityHousehold" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Sub Community Household</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button> 
            </div>
            <form method="POST" enctype='multipart/form-data' id="subCommunityHouseholdForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Community</label>
                                <select name="community_id" id="communityChanges" 
                                    class="selectpicker form-control" required
                                    data-live-search="true" data-parsley-required="true">
                                    <option disabled selected>Choose one...</option>
                                    <?php $__currentLoopData = $communities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($community->id); ?>">
                                        <?php echo e($community->english_name); ?>
                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                            <div id="community_error" style="color: red;"></div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Household</label>
                                <select name="household_id[]" id="selectedHousehold" 
                                    class="selectpicker form-control" required 
                                    data-parsley-required="true" multiple
                                    disabled data-live-search="true" >
                                    <option disabled selected>Choose one...</option>
                                </select>
                            </fieldset>
                            <div id="household_id_error" style="color: red;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Sub Community</label>
                                <select name="sub_community_id" id="selectedSubCommunity" 
                                    class="selectpicker form-control" required>
                                    <option disabled selected>Choose one...</option>
                                    <?php $__currentLoopData = $subCommunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subCommunity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subCommunity->id); ?>">
                                        <?php echo e($subCommunity->english_name); ?>
                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </fieldset>
                            <div id="sub_community_id_error" style="color: red;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
     
    $(document).on('change', '#communityChanges', function () {
        community_id = $(this).val();
   
        $.ajax({
            url: "progress-household/household/get_by_community/" + community_id,
            method: 'GET',
            success: function(data) {

                var select = $('#selectedHousehold');
                select.prop('disabled', false); 

                select.html(data.html);
                select.selectpicker('refresh');
            }
        });

        $.ajax({
            url: "sub-community/get_by_community/" + community_id,
            method: 'GET',
            success: function(data) {

                var select = $('#selectedSubCommunity');
                select.prop('disabled', false); 

                select.html(data.html);
                select.selectpicker('refresh');
            }
        });
    });

    $(document).ready(function () {
 
        $('#subCommunityHouseholdForm').on('submit', function (event) {

            var communityMain = $('#communityChanges').val();
            var householdValue = $('#selectedHousehold').val();
            var subCommunityValue = $('#selectedSubCommunity').val();

            if (communityMain == null) {

                $('#community_error').html('Please select a community!'); 
                return false;
            } else if (communityMain != null) {

                $('#community_error').empty();
            }

            if (!householdValue || householdValue.length === 0) {

                $('#household_id_error').html('Please select at least one household!');
                return false;
            } else {

                $('#household_id_error').empty();
            }

            if (subCommunityValue == null) {

                $('#sub_community_id_error').html('Please select a sub community!'); 
                return false;
            } else if (subCommunityValue != null) {

                $('#sub_community_id_error').empty();
            }

            $('#selectedHousehold').prop('disabled', false);
            
            $(this).addClass('was-validated');  
            $('#community_error').empty(); 
            $('#household_id_error').empty();
            $('#sub_community_id_error').empty();

            $.ajax({ 
                url: "sub-community-household",
                method: 'POST',
                success: function(data) {
                
                }
            });
        });
    });
</script><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/admin/community/sub/create.blade.php ENDPATH**/ ?>