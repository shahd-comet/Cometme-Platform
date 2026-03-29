<div class="card border-success mb-4">
    <div class="card-header bg-success text-white">
        <strong>Grid Integration System</strong> 
        <small class="ml-2">(This section is for updating Grid Integration details)</small>
    </div>

    <div class="card-body" style="background-color:#f3fff5;">

        <div class="row">
            <div class="col-xl-4">
                <label>Integration Large</label>
                <input type="number" name="grid_integration_large"
                       value="{{ $grid->grid_integration_large ?? '' }}"
                       class="form-control">
            </div>

            <div class="col-xl-4">
                <label>Large Date</label>
                <input type="date" name="large_date"
                       value="{{ $grid->large_date ?? '' }}"
                       class="form-control">
            </div>

            <div class="col-xl-4">
                <label>Integration Small</label>
                <input type="number" name="grid_integration_small"
                       value="{{ $grid->grid_integration_small ?? '' }}"
                       class="form-control">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-xl-4">
                <label>Small Date</label>
                <input type="date" name="small_date"
                       value="{{ $grid->small_date ?? '' }}"
                       class="form-control">
            </div>

            <div class="col-xl-4">
                <label>Request Date</label>
                <input type="date" name="request_date"
                       value="{{ $grid->request_date ?? $allWaterHolder->request_date }}"
                       class="form-control">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-4">
                <label>Delivery</label>
                <select name="is_delivery_grid" class="form-control">
                    <option disabled selected>{{ $grid->is_delivery ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="col-xl-4">
                <label>Paid</label>
                <select name="is_paid_grid" class="form-control">
                    <option disabled selected>{{ $grid->is_paid ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="NA">NA</option>
                </select>
            </div>

            <div class="col-xl-4">
                <label>Complete</label>
                <select name="is_complete_grid" class="form-control">
                    <option disabled selected>{{ $grid->is_complete ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

    </div>
</div>