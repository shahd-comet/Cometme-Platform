@extends('layouts/layoutMaster')

@section('title', 'water holder')

@include('layouts.all')

@section('content')

@php
    $holder = $allWaterHolder->household_id
        ? $allWaterHolder->Household
        : $allWaterHolder->PublicStructure;

    $type = $allWaterHolder->household_id ? 'household' : 'structure';

    $h2o = $h2oUser ?? $h2oPublic ?? null;
@endphp

<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">
        {{$holder->english_name ?? ''}}
    </span>
    Information
</h4>

<div class="col-xl-12">
    <div class="card">
        <div class="card-body">
            <ul class="timeline timeline-dashed mt-4">
                <li class="timeline-item timeline-item-success mb-4">
                    <span class="timeline-indicator timeline-indicator-success">
                    <i class="bx {{ $type == 'household' ? 'bx-user' : 'bx-building' }}"></i>
                    </span>
                    <div class="timeline-event">
                        <div class="timeline-header border-bottom mb-3">
                            <h6 class="mb-0">
                            {{$holder->english_name ?? ''}} -  
                                <span class="text-success">Details</span>
                            </h6>
                            <h6 class="mb-0">
                                Community :  
                                <span class="text-success">{{$community->english_name}}</span>
                            </h6>
                        </div>
                        @if($allWaterHolder->household_id)
                        <div class="row">
                            <h6 class="text-success">General Details</h6>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0">
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-male',
                                        'label' => '# of Male',
                                        'value' => $holder->number_of_male
                                    ])      
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-female',
                                        'label' => '# of Female',
                                        'value' => $holder->number_of_female
                                    ])      
                                </ul>
                            </div>
                            <div class="col-lg-6 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0">     
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-group',
                                        'label' => '# of Adults',
                                        'value' => $holder->number_of_adults
                                    ])      
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-face',
                                        'label' => '# of Children',
                                        'value' => $holder->number_of_children
                                    ]) 
                                </ul>
                            </div>
                        </div> <hr>
                        @endif

                        @if($energyUser->isNotEmpty())
                            <div class="row">
                                <h6 class="text-success">Energy Service Details</h6>
                            </div>
                            
                            <div class="row">
                                <div class="col-lg-4 d-flex justify-content-between flex-wrap">
                                    <ul class="p-0 m-0">    
                                        @include('users.water.details.info-item', [
                                            'icon' => 'bx-circle',
                                            'label' => 'Main Holder',
                                            'value' => $energyUser[0]->is_main
                                        ]) 
                                    </ul>
                                </div>
                                <div class="col-lg-4 d-flex justify-content-between flex-wrap">
                                    <ul class="p-0 m-0">    
                                        @include('users.water.details.info-item', [
                                            'icon' => 'bx-calendar',
                                            'label' => 'Energy Date',
                                            'value' => $energyUser[0]->installation_date
                                        ]) 
                                    </ul>
                                </div>
                                <div class="col-lg-4 d-flex justify-content-between flex-wrap">
                                    <ul class="p-0 m-0">    
                                        @include('users.water.details.info-item', [
                                            'icon' => 'bx-barcode',
                                            'label' => 'Meter Number',
                                            'value' => $energyUser[0]->meter_number
                                        ]) 
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </li>
                
                <li class="timeline-item timeline-item-info mb-4">
                    <span class="timeline-indicator timeline-indicator-info">
                        <i class="bx bx-water"></i>
                    </span>
                    <div class="timeline-event">
                        <div>
                            <div class="timeline-header border-bottom mb-3">
                                <h6 class="mb-0">Water - <span class="text-info">Systems</span></h6>
                                <small class="text-muted">Main Holder : 
                                    <span class="text-info">{{$allWaterHolder->is_main}}</span>
                                </small>
                            </div>
                            
                            @if($allWaterHolder->is_main == 'No')
                            <div class="row">
                                <div class="col-lg-6 d-flex justify-content-between flex-wrap">
                                    <ul class="p-0 m-0">
                                        <li class="d-flex mb-4">
                                            <div class="avatar avatar-sm flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded-circle bg-label-info">
                                                    <i class='bx bx-user'></i>
                                                </span>
                                            </div>
                                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <p class="mb-0 lh-1">Main User</p>
                                                <small class="text-muted">
                                                    @if($mainUser)
                                                        {{$mainUser->user_english_name}}
                                                    @else @if($mainGridUser)
                                                        {{$mainGridUser->grid_user_english_name}}
                                                    @else @if($mainH2oPublic)
                                                        {{$mainH2oPublic->H2oPublicStructure->public_structure_name}}
                                                    @endif
                                                    @endif
                                                    @endif
                                                </small>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @endif
                        </div>
                        @if($h2o)
                        <div class="row">
                            <h6><i class="bx bx-water text-info"></i> Old H2O Details</h6>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-calendar',
                                        'label' => 'Request Date',
                                        'value' => $h2o->h2o_request_date,
                                        'color' => 'info'
                                    ]) 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-calendar-week',
                                        'label' => 'Installation Year',
                                        'value' => $h2o->installation_year,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-info-circle',
                                        'label' => 'Number of H2O',
                                        'value' => $h2o->number_of_h20,
                                        'color' => 'info'
                                    ]) 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-circle',
                                        'label' => 'H2O Status',
                                        'value' => $h2oStatus ? $h2oStatus->status : '',
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-info-circle',
                                        'label' => 'Number of BSF',
                                        'value' => $h2o->number_of_bsf,
                                        'color' => 'info'
                                    ]) 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-cloud-snow',
                                        'label' => 'BSF Status',
                                        'value' => $bsfStatus ? $bsfStatus->name : '',
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-exit',
                                        'label' => 'Delivery',
                                        'value' => $h2o->is_delivery,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-shekel',
                                        'label' => 'Paid',
                                        'value' => $h2o->is_paid,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-check',
                                        'label' => 'Complete',
                                        'value' => $h2o->is_complete,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                        </div> 
                        <hr>
                        @endif


                        @if($gridUser)
                        <div class="row">
                            <h6><i class="bx bx-droplet text-info"></i> Grid Integration Details</h6>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-calendar',
                                        'label' => 'Request Date',
                                        'value' => $gridUser->request_date,
                                        'color' => 'info'
                                    ]) 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-expand',
                                        'label' => 'Number of Grid Large',
                                        'value' => $gridUser->grid_integration_large,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-collapse',
                                        'label' => 'Number of Grid Small',
                                        'value' => $gridUser->grid_integration_small,
                                        'color' => 'info'
                                    ]) 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-calendar',
                                        'label' => 'Grid Large Date',
                                        'value' => $gridUser->large_date,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-calendar',
                                        'label' => 'Grid Small Date',
                                        'value' => $gridUser->small_date,
                                        'color' => 'info'
                                    ]) 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-exit',
                                        'label' => 'Delivery',
                                        'value' => $gridUser->is_delivery,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-shekel',
                                        'label' => 'Paid',
                                        'value' => $gridUser->is_paid,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-check',
                                        'label' => 'Complete',
                                        'value' => $gridUser->is_complete,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                        </div> <hr>
                        @endif

                        @if(count($sharedGridUsers) > 0)
                        <div class="row">
                            <h6><i class="bx bx-user text-info"></i> Shared Grid Users</h6>
                        </div>
                        @foreach($sharedGridUsers as $sharedGridUser)
                            <ul>
                                <li class="text-muted">
                                    {{$sharedGridUser->Household->english_name}}
                                </li>
                            </ul>
                        @endforeach
                            <hr>
                        @endif
                        
                        @if($networkUser)
                        <div class="row">
                            <h6><i class="bx bx-water text-info"></i> Network Details</h6>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-exit',
                                        'label' => 'Delivery',
                                        'value' => $networkUser->is_delivery,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                            <div class="col-lg-4 d-flex justify-content-between flex-wrap mb-2">
                                <ul class="p-0 m-0"> 
                                    @include('users.water.details.info-item', [
                                        'icon' => 'bx-check',
                                        'label' => 'Complete',
                                        'value' => $networkUser->is_complete,
                                        'color' => 'info'
                                    ]) 
                                </ul>
                            </div>
                        </div> 
                        <hr>
                        @endif


                        @if(count($sharedH2oUsers) > 0)
                        <div class="row">
                            <h6><i class="bx bx-user text-info"></i> Shared H2O Users</h6>
                        </div>
                        @foreach($sharedH2oUsers as $sharedH2oUser)
                            <ul>
                                <li class="text-muted">
                                    {{$sharedH2oUser->Household->english_name}}
                                </li>
                            </ul>
                        @endforeach
                            <hr>
                        @endif

                        @if(count($sharedH2oPublics) > 0)
                        <div class="row">
                            <h6><i class="bx bx-building text-info"></i> Shared H2O Public Structures</h6>
                        </div>
                        @foreach($sharedH2oPublics as $sharedH2oPublic)
                            <ul>
                                <li class="text-muted">
                                    {{$sharedH2oPublic->PublicStructure->english_name}}
                                </li>
                            </ul>
                        @endforeach
                            <hr>
                        @endif

                        

                        @if(count($allWaterHolderDonors) > 0)
                        <div class="row">
                            <h6><i class="bx bx-shekel text-info"></i> Donors</h6>
                        </div>
                        @foreach($allWaterHolderDonors as $allWaterHolderDonor)
                            <ul>
                                <li class="text-muted">
                                {{$allWaterHolderDonor->donor_name}}
                                </li>
                            </ul>
                        @endforeach
                        @endif

                    </div>
                </li>

                @if(count($waterIncident) > 0)
                <li class="timeline-item timeline-item-danger mb-4">
                    <span class="timeline-indicator timeline-indicator-danger">
                        <i class="bx bx-error"></i>
                    </span>
                    <div class="timeline-event">
                        <div>
                            <div class="timeline-header border-bottom mb-3">
                                <h6 class="mb-0">Incident - <span class="text-danger">Details</span></h6>
                                <small class="text-muted">
                                    <span class="text-danger">Date of Incident:</span>
                                    {{$waterIncident[0]->incident_date}}
                                </small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between flex-wrap flex-sm-row flex-column text-center">
                            <div class="mb-sm-0 mb-2">
                                <p class="mb-0">Type</p>
                                <span class="text-muted">
                                {{$waterIncident[0]->incident}}
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Status</p>
                                <span class="text-muted">
                                {{$waterIncident[0]->incident_status}}
                                </span>
                            </div>
                            <div class="mb-sm-0 mb-2">
                                <p class="mb-0">Response Date</p>
                                <span class="text-muted">
                                {{$waterIncident[0]->response_date}}
                                </span>
                            </div>
                        </div> <br>
                        <div class="d-flex justify-content-between flex-wrap flex-sm-row flex-column text-center">
                            <div class="mb-sm-0 mb-2">
                             
                            </div>
                        </div>
                    </div>
                </li>
                @endif
                <li class="timeline-end-indicator timeline-indicator-success">
                    <i class="bx bx-image"></i>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection