@extends('layouts/layoutMaster')

@section('title', 'edit vending history')

@include('layouts.all')

<style>
    label, input {

        display: block;
    }
    label {

        margin-top: 20px;
    }
</style>

@section('content')

<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Edit </span> 
        {{$vendingHistory->Vendor->english_name}} - 
    <span class="text-muted fw-light">Visit Information </span> 
</h4>

<div class="card">
    <div class="card-content collapse show">
        <div class="card-body">
            <form method="POST" action="{{route('vending-history.update', $vendingHistory->id)}}"
                enctype="multipart/form-data" >
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Vending Point</label>
                            <input type="text" class="form-control" disabled
                            value="{{$vendingHistory->Vendor->english_name}}">
                        </fieldset>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Service</label>
                            <input type="text" disabled class="form-control" 
                            value="{{$vendingHistory->VendorService->ServiceType->service_name}}">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Visit By</label>
                            <select class="selectpicker form-control" name="user_id" data-live-search="true">
                                @if($vendingHistory->user_id)
                                <option value="{{$vendingHistory->User->name}}" disabled selected>
                                    {{$vendingHistory->User->name}}
                                </option>
                                @else
                                <option selected disabled>Choose one..</option>
                                @endif
                                
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">
                                        {{$user->name}}
                                    </option>
                                @endforeach  
                            </select>
                        </fieldset>
                    </div> 
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Visit Date</label>
                            <input type="date" name="visit_date" class="form-control" 
                                value="{{$vendingHistory->visit_date}}">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Collecting Date From</label>
                            <input type="date" name="collecting_date_from" class="form-control" 
                                value="{{$vendingHistory->collecting_date_from}}">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Collecting Date To</label>
                            <input type="date" name="collecting_date_to" class="form-control" 
                                value="{{$vendingHistory->collecting_date_to}}">
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Total Amount</label>
                            <input type="number" name="total_amount_due" class="form-control" id="totalAmount" 
                                step="0.01" value="{{$vendingHistory->total_amount_due}}">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Collecting Amount</label>
                            <input type="number" name="amount_collected" class="form-control" id="amountCollected" 
                                step="0.01" value="{{$vendingHistory->amount_collected}}">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Remaining Amount</label>
                            <input type="number" name="remaining_balance" class="form-control" id="remainingBalance" 
                                step="0.01" readonly value="{{$vendingHistory->remaining_balance}}">
                        </fieldset>
                    </div>
                </div>

  
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Notes</label>
                            <textarea name="notes" class="form-control" 
                                style="resize:none" cols="20" rows="3">
                                {{$vendingHistory->notes}}
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


<script>

    $(document).ready(function() {

        function calculateRemaining() {

            let total = parseFloat($('#totalAmount').val()) || 0;
            let collected = parseFloat($('#amountCollected').val()) || 0;
            let remaining = total - collected;
            $('#remainingBalance').val(remaining.toFixed(2));
        }

        $('#totalAmount, #amountCollected').on('input', function () {

            calculateRemaining();
        });
    });
</script>

@endsection