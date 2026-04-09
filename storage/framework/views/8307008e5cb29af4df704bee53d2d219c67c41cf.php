<div class="modal fade" id="createReturned" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Returned Camera</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="<?php echo e(route('camera-returned.store')); ?>">
                <?php echo csrf_field(); ?>

                <div class="modal-body">
                    <div class="row">

                        
                        <div class="col-md-6">
                            <label>Camera Community</label>
                            <select id="camera_community_id"
                                    name="camera_community_id"
                                    class="selectpicker form-control"
                                    data-live-search="true"
                                    required>
                                <option value="" disabled selected>Choose Camera Community</option>
                                <?php $__currentLoopData = $cameraCommunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cc->id); ?>" data-community="<?php echo e($cc->community_id); ?>" title="<?php echo e($cc->display_name ?? 'Unknown'); ?>"><?php echo e($cc->display_name ?? 'Unknown'); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!--<div class="col-md-6">-->
                        <!--    <label>Compound</label>-->
                        <!--    <select id="compound_id_select"-->
                        <!--            name="compound_id"-->
                        <!--            class="selectpicker form-control"-->
                        <!--            data-live-search="true">-->
                        <!--        <option value="" selected>Choose Compound</option>-->
                        <!--    </select>-->
                        <!--</div>-->

                        
                        <div class="col-md-6">
                            <label>Repository</label>
                            <select name="repository_id" class="selectpicker form-control" data-live-search="true">
                                <option value="" selected>Choose Repository</option>
                                <?php if(isset($repositories)): ?>
                                    <?php $__currentLoopData = $repositories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $repo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($repo->id); ?>"><?php echo e($repo->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Date of Return</label>
                            <input type="date" name="date_of_return" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Camera Type</label>
                            <select name="camera_id" class="selectpicker form-control" data-live-search="true" required>
                                <option disabled selected>Choose Camera Type</option>
                                <?php $__currentLoopData = $cameras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $camera): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($camera->id); ?>"><?php echo e($camera->model); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label># of Cameras</label>
                            <input type="number" name="number_of_cameras" class="form-control" value="1" min="0" required>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label>NVR Type</label>
                            <select name="nvr_camera_id" class="selectpicker form-control" data-live-search="true">
                                <option disabled selected>Choose NVR</option>
                                <?php $__currentLoopData = $nvrCameras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nvr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($nvr->id); ?>"><?php echo e($nvr->model); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-4 mt-3">
                            <label># of NVRs</label>
                            <input type="number" name="number_of_nvrs" class="form-control" value="0" min="0">
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>SD Card Number</label>
                            <input type="number" name="sd_card_number" class="form-control">
                        </div>

                        <div class="col-md-12 mt-3">
                            <label>Where did the Camera Return ?</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_na" value="0" checked>
                                    <p class="form-check-label" for="status_na">N/A</p>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_commet" value="1">
                                    <p class="form-check-label" for="status_commet">to Comet</p>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_community" value="2">
                                    <p class="form-check-label" for="status_community">to Community</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
$(function () {
    var $communitySelect = $('#camera_community_id');
    var $compoundSelect  = $('#compound_id_select');

    if (!$communitySelect.length) {
        console.warn('camera_community_id not found');
        return;
    }
    if (!$compoundSelect.length) {
        console.warn('compound_id_select not found');
        return;
    }

    function refreshCompound() {
        try { $compoundSelect.selectpicker('refresh'); } catch (e) {}
    }

    function resetCompounds() {
        $compoundSelect.html('<option value="" selected>Choose Compound</option>');
        refreshCompound();
    }

    function loadCompoundsForCommunity(communityId) {
        if (!communityId) {
            resetCompounds();
            return;
        }

        var url = "<?php echo e(url('/compounds/by-community')); ?>/" + encodeURIComponent(communityId);

        console.log('Compounds URL:', url);

        $.ajax({
            url: url,
            method: 'GET',
            cache: false,
            success: function (res) {
                console.log('Compounds response:', res);

                if (typeof res === 'string') {
                    $compoundSelect.html(res);
                } else if (res && typeof res.htmlCompounds !== 'undefined') {
                    $compoundSelect.html(res.htmlCompounds);
                } else if (res && Array.isArray(res)) {
                    var html = '<option value="" selected>Choose Compound</option>';
                    res.forEach(function (c) {
                        html += '<option value="'+c.id+'">'+(c.name || c.english_name || c.arabic_name || 'Compound')+'</option>';
                    });
                    $compoundSelect.html(html);
                } else {
                    resetCompounds();
                }

                refreshCompound();
            },
            error: function (xhr) {
                console.error('Compounds AJAX error:', xhr.status, xhr.responseText);
                resetCompounds();
            }
        });
    }

    $('#createReturned').on('shown.bs.modal', function () {
        try { $('.selectpicker').selectpicker('refresh'); } catch (e) {}
    });

    resetCompounds();

    $communitySelect.on('changed.bs.select change', function () {
        var communityId = $(this).find('option:selected').data('community') || null;
        console.log('Selected communityId:', communityId, 'selected value:', $(this).val());
        loadCompoundsForCommunity(communityId);
    });
});
</script>
<?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/services/camera/returned/create.blade.php ENDPATH**/ ?>