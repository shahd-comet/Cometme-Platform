@extends('layouts/layoutMaster')

@section('title', 'edit reactivated holder')

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
    {{ $reactivatedHolder->AllEnergyMeter->Household->english_name 
        ?? $reactivatedHolder->AllEnergyMeter->PublicStructure->english_name 
    }}
    <span class="text-muted fw-light">Information </span> 
</h4>

<div class="card">
    <div class="card-content collapse show">
        <div class="card-body">
            <form method="POST" action="{{route('deactivated-holder.update', $reactivatedHolder->id)}}"
             enctype="multipart/form-data" >
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Community</label>
                            <select class="selectpicker form-control" name="community_id" disabled>
                                @if($reactivatedHolder->AllEnergyMeter->Community)
                                    <option value="{{$reactivatedHolder->AllEnergyMeter->Community->id}}">
                                        {{$reactivatedHolder->AllEnergyMeter->Community->english_name}}
                                    </option>
                                @endif                                
                            </select>
                        </fieldset>
                    </div> 
                    @php
                        $isHousehold = $reactivatedHolder->AllEnergyMeter->Household->id;
                        $label = $isHousehold ? 'User' : 'Public';
                        $selectedName = $isHousehold
                            ? $reactivatedHolder->AllEnergyMeter->Household->english_name
                            : $reactivatedHolder->AllEnergyMeter->PublicStructure->english_name;
                    @endphp

                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>{{ $label }}</label>

                            <select class="selectpicker form-control" name="all_energy_meter_id" data-live-search="true" disabled>
                                <option value="{{ $selectedName }}" disabled selected>
                                    {{ $selectedName }}
                                </option>
                            </select>
                        </fieldset>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Visit Date</label>
                            <input type="date" name="visit_date" value="{{$reactivatedHolder->visit_date}}"
                                class="form-control">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Submitted By</label>
                            <select class="selectpicker form-control" name="user_id">
                                @if($reactivatedHolder->User)
                                    <option value="{{$reactivatedHolder->User->id}}">
                                        {{$reactivatedHolder->User->name}}
                                    </option>
                                    
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>      
                                @endforeach   
                                @else
                                    <option selected disabled>Choose one...</option>   
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>      
                                @endforeach      
                                @endif                 
                            </select>
                        </fieldset>
                    </div> 
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Deactivation after the war?</label>
                            <select name="deactivated_after_war" class="form-control">
                                @if($reactivatedHolder->deactivated_after_war)
                                    <option value="{{$reactivatedHolder->deactivated_after_war}}">
                                        {{$reactivatedHolder->deactivated_after_war}}
                                    </option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                @else
                                    <option disabled selected>Choose one...</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                @endif  
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Is Paid?</label>
                            <select name="is_paid" class="form-control">
                                @if($reactivatedHolder->is_paid)
                                    <option value="{{$reactivatedHolder->is_paid}}">
                                        {{$reactivatedHolder->is_paid}}
                                    </option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="Free">Free</option>
                                @else
                                    <option disabled selected>Choose one...</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="Free">Free</option>
                                @endif  
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Interested in reactivation?</label>
                            <select name="is_return" class="form-control">
                                @if($reactivatedHolder->is_return)
                                    <option value="{{$reactivatedHolder->is_return}}">
                                        {{$reactivatedHolder->is_return}}
                                    </option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                @else
                                    <option disabled selected>Choose one...</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                @endif  
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Reactivation Date</label>
                            <input type="date" name="reactivation_date" value="{{$reactivatedHolder->reactivation_date}}"
                                class="form-control">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Fine Paid?</label>
                            <input type="float" name="paid_amount" value="{{$reactivatedHolder->paid_amount}}"
                            class="form-control">
                        </fieldset>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>System Status</label>
                            <select name="system_status" class="form-control">
                                @if($reactivatedHolder->system_status)
                                    <option value="{{$reactivatedHolder->system_status}}">
                                        {{$reactivatedHolder->system_status}}
                                    </option>
                                    <option value="Good">Good</option>
                                    <option value="Bad">Bad</option>
                                    <option value="Meduim">Meduim</option>
                                @else
                                    <option disabled selected>Choose one...</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                    <option value="Free">Free</option>
                                @endif  
                            </select>
                        </fieldset>
                    </div>
                    
                    <div class="col-xl-8 col-lg-8 col-md-8 mb-1">
                        <fieldset class="form-group">
                            <label class='col-md-12 control-label'>Notes</label>
                            <textarea name="notes" class="form-control" 
                                style="resize:none" cols="20" rows="3">
                            {{$reactivatedHolder->notes}}
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

@endsection