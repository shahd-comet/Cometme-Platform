<div class="card border-info mb-4">
    <div class="card-header bg-info text-white">
        <strong>H2O System</strong> 
        <small class="ml-2">This section is for updating H2O details</small>
    </div>

    <div class="card-body" style="background-color:#f0f9ff;">

        <div class="row">
            <div class="col-xl-4">
                <label>Number of H2O</label>
                <input type="number" name="number_of_h20" value="{{ $h2o->number_of_h20 ?? '' }}" class="form-control">
            </div>

            <div class="col-xl-4">
                <label>H2O Status</label>
                <select name="h2o_status_id" class="form-control">
                    @if(isset($h2o->H2oStatus))
                        <option selected disabled>{{$h2o->H2oStatus->status}}</option>
                    @else
                        <option disabled selected>Choose...</option>
                    @endif

                    @foreach($h2oStatuses as $status)
                        <option value="{{$status->id}}">{{$status->status}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-4">
                <label>Number of BSF</label>
                <input type="number" name="number_of_bsf" value="{{ $h2o->number_of_bsf ?? '' }}" class="form-control">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-xl-4">
                <label>BSF Status</label>
                <select name="bsf_status_id" class="form-control">
                    @if(isset($h2o->BsfStatus))
                        <option selected disabled>{{$h2o->BsfStatus->name}}</option>
                    @else
                        <option disabled selected>Choose...</option>
                    @endif

                    @foreach($bsfStatuses as $status)
                        <option value="{{$status->id}}">{{$status->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xl-4">
                <label>Request Date</label>
                <input type="date" name="h2o_request_date"
                       value="{{ $h2o->h2o_request_date ?? $allWaterHolder->request_date }}"
                       class="form-control">
            </div>

            <div class="col-xl-4">
                <label>Installation Year</label>
                <input type="number" name="installation_year"
                       value="{{ $h2o->installation_year ?? '' }}"
                       class="form-control">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-xl-4">
                <label>Delivery</label>
                <select name="is_delivery_h2o" class="form-control">
                    <option disabled selected>{{ $h2o->is_delivery ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>

            <div class="col-xl-4">
                <label>Paid</label>
                <select name="is_paid_h2o" class="form-control">
                    <option disabled selected>{{ $h2o->is_paid ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                    <option value="NA">NA</option>
                </select>
            </div>

            <div class="col-xl-4">
                <label>Complete</label>
                <select name="is_complete_h2o" class="form-control">
                    <option disabled selected>{{ $h2o->is_complete ?? 'Choose...' }}</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        </div>

    </div>
</div>