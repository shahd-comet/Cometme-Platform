<style>
    label, input {
    display: block;
}

label, table {
    margin-top: 20px;
}
</style>

<div id="updateElectricityGridCompound<?php echo e($compoundsElecticityRoom->id); ?>" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                    Update Electricity Room/Grid
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div> 
            <div class="modal-body">
                <form method="POST" enctype='multipart/form-data' action="<?php echo e(url('room-grid')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <input type="hidden" name="compound_id" value="<?php echo e($compoundsElecticityRoom->id); ?>">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group"> 
                                <label class='col-md-12 control-label'>Electricity Room</label>
                                <select name="electricity_room" class="form-control">
                                    <option disabled selected><?php echo e($compoundsElecticityRoom->electricity_room); ?></option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Grid</label>
                                <select name="grid" class="form-control">
                                    <option disabled selected><?php echo e($compoundsElecticityRoom->grid); ?></option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </fieldset>
                        </div>
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
 

</script><?php /**PATH C:\Users\user\Desktop\public_html\comet-me\resources\views/actions/admin/AC/room_compound.blade.php ENDPATH**/ ?>