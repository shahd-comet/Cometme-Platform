@php
  $pricingModal = true;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'edit activist holder')

@include('layouts.all')

<style>
    label, input{ 
    display: block;
}
label {
    margin-top: 20px;
}
</style>

@section('content')
<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Edit </span> {{$townHolder->english_name}}
    <span class="text-muted fw-light">Information </span> 
</h4>

<div class="card">
    <div class="card-content collapse show">
        <div class="card-body">
            <form method="POST" action="{{route('activist-holder.update', $townHolder->id)}}"
             enctype="multipart/form-data" >
                @csrf
                @method('PATCH')
                    <div class="row">
                        <h5>General Details</h5>
                    </div>
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>English Name</label>
                                <input type="text" name="english_name" 
                                class="form-control" value="{{$townHolder->english_name}}">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Arabic Name</label>
                                <input type="text" name="arabic_name" class="form-control"
                                value="{{$townHolder->arabic_name}}">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Community</label>
                                <select name="community_id"
                                    class="selectpicker form-control" data-live-search="true"required>
                                    <option disabled selected>{{$townHolder->Community->english_name}}</option>
                                    @foreach($communities as $community)
                                    <option value="{{$community->id}}">
                                        {{$community->english_name}}
                                    </option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Phone Number</label>
                                <input type="text" name="phone_number" 
                                value="{{$townHolder->phone_number}}" class="form-control">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Has Internet?</label>
                                <select name="has_internet"
                                    class="selectpicker form-control" data-live-search="true">
                                    <option value="1" {{ $townHolder->has_internet == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $townHolder->has_internet == 0 ? 'selected' : '' }}>No</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Has Refrigerator?</label>
                                <select name="has_refrigerator"
                                    class="selectpicker form-control" data-live-search="true">
                                    <option value="1" {{ $townHolder->has_refrigerator == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ $townHolder->has_refrigerator == 0 ? 'selected' : '' }}>No</option>
                                </select>
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