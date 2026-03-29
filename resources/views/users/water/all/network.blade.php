<div class="card border-primary mb-4">
    <div class="card-header bg-primary text-white">
        <strong>Network System</strong>
        <small class="ml-2">(This section is for updating Network System details)</small>
    </div>

    <div class="card-body" style="background-color:#f2f7ff;">

        <div class="row mt-3">
            <div class="col-xl-4">
                <label>Delivery</label>
                <select name="is_delivery_network" class="form-control">
                    <option disabled selected>{{ $network->is_delivery ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="col-xl-4">
                <label>Complete</label>
                <select name="is_complete_network" class="form-control">
                    <option disabled selected>{{ $network->is_complete ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

    </div>
</div>