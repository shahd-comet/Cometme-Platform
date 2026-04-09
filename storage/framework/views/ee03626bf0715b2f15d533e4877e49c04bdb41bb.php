<style>
    label, input, select, textarea {
        display: block;
        width: 100%;
    }

    label, table {
        margin-top: 20px;
    }

    .form-table th, .form-table td {
        vertical-align: middle;
    }
</style>

<div id="createReplacement" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Add New Camera Replacement</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="<?php echo e(route('replacement.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <table class="table table-bordered form-table">
                        <tr>
                            <th>Camera Community</th>
                            <td>
                                <select id="camera_community_id" name="camera_community_id" class="form-select" required>
                                    <option value="" selected disabled>Select Camera Community</option>
                                    <?php $__currentLoopData = $cameraCommunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cc->id); ?>" data-community="<?php echo e($cc->community_id); ?>">
                                            <?php echo e($cc->community->english_name ?? 'Unknown'); ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Compound</th>
                            <td>
                                <select id="compound_id_select" name="compound_id" class="form-select">
                                    <option value="" selected>Choose Compound...</option>
                                    <?php $__currentLoopData = $compounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($compound->id); ?>" data-community="<?php echo e($compound->community_id); ?>">
                                            <?php echo e($compound->english_name); ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Date of Replacement</th>
                            <td><input type="date" id="date_of_replacement" name="date_of_replacement" class="form-control" required></td>
                        </tr>

                        <tr>
                            <th>Number of Damaged Cameras</th>
                            <td><input type="number" id="damaged_camera_count" name="damaged_camera_count" class="form-control" min="0" required></td>
                        </tr>

                        <tr>
                            <th>Number of New Cameras</th>
                            <td><input type="number" id="new_camera_count" name="new_camera_count" class="form-control" min="0" required></td>
                        </tr>

                        <tr>
                            <th>Number of Damaged SD Cards</th>
                            <td><input type="number" id="damaged_sd_card_count" name="damaged_sd_card_count" class="form-control" min="0" placeholder="Enter number of damaged SD cards"></td>
                        </tr>

                        <tr>
                            <th>Number of New SD Cards</th>
                            <td><input type="number" id="new_sd_card_count" name="new_sd_card_count" class="form-control" min="0" placeholder="Enter number of new SD cards"></td>
                        </tr>

                        <tr>
                            <th>Camera Type</th>
                            <td>
                                <select id="camera_id" name="camera_id" class="form-select" required>
                                    <option value="" selected disabled>Select Camera Type</option>
                                    <?php $__currentLoopData = $cameras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $camera): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($camera->id); ?>"><?php echo e($camera->model); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>NVR Camera (optional)</th>
                            <td>
                                <select id="nvr_camera_id" name="nvr_camera_id" class="form-select">
                                    <option value="" selected>None</option>
                                    <?php $__currentLoopData = $nvrCameras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nvr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($nvr->id); ?>"><?php echo e($nvr->model); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Number of NVRs</th>
                            <td><input type="number" id="number_of_nvr" name="number_of_nvr" class="form-control" min="0"></td>
                        </tr>

                        <tr>
                            <th>Incident Type</th>
                            <td>
                                <select id="camera_replacement_incident_id" name="camera_replacement_incident_id" class="form-select">
                                    <option value="" selected disabled>Select Incident Type</option>
                                    <?php $__currentLoopData = $cameraReplacementIncidents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incident): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($incident->id); ?>"><?php echo e($incident->english_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Notes (optional)</th>
                            <td><textarea id="notes" name="notes" class="form-control" rows="3"></textarea></td>
                        </tr>
                        <tr>
                            <th>Donors (optional)</th>
                            <td>
                                <select id="donor_ids" name="donor_ids[]" class="form-select" multiple>
                                    <?php $__currentLoopData = $donors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($donor->id); ?>"><?php echo e($donor->donor_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple donors</small>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Replacement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const communitySelect = document.getElementById('camera_community_id');
    const compoundSelect = document.getElementById('compound_id_select');
    function filterCompounds() {
        const selectedCommunityOption = communitySelect.options[communitySelect.selectedIndex];
        const selectedCommunityId = selectedCommunityOption ? selectedCommunityOption.getAttribute('data-community') : null;
        let found = false;
        Array.from(compoundSelect.options).forEach(option => {
            if (!option.value) return;
            if (option.getAttribute('data-community') === selectedCommunityId) {
                option.style.display = '';
                if (compoundSelect.value == option.value) found = true;
            } else {
                option.style.display = 'none';
            }
        });
        // إذا كان هناك خيار واحد فقط ظاهر، اختره تلقائيًا
        const visibleOptions = Array.from(compoundSelect.options).filter(option => option.style.display !== 'none' && option.value);
        if (!found && visibleOptions.length === 1) {
            compoundSelect.value = visibleOptions[0].value;
        }
        if (!found && visibleOptions.length !== 1) {
            compoundSelect.value = '';
        }
    }
    communitySelect.addEventListener('change', filterCompounds);
    filterCompounds();
});
</script>
<?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/services/camera/replacements/create.blade.php ENDPATH**/ ?>