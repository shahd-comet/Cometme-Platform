@php
  $pricingModal = true;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Vending Point')

@include('layouts.all')

@section('content')
 
<p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseVendorExport" aria-expanded="false" 
        aria-controls="collapseVendorExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseEnergyUserVisualData collapseVendorExport">
        <i class="menu-icon tf-icons bx bx-expand-alt"></i>
        Toggle All
    </button> 
</p> 
 

<div class="collapse multi-collapse container mb-4" id="collapseVendorExport">
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                Export Vending Point Report 
                                    <i class='fa-solid fa-file-excel text-info'></i>
                                </h5>
                            </div>
                            <div class="col-xl-2 col-lg-2 col-md-2">
                                <fieldset class="form-group">
                                    <button class="" id="clearEnergyHolderFiltersButton">
                                    <i class='fa-solid fa-eraser'></i>
                                        Clear Filters
                                    </button>
                                </fieldset>
                            </div>
                        </div> 
                    </div>
                    <form method="POST" enctype='multipart/form-data' id="exportFromEnergyHolder"
                        action="{{ route('vending-point.export') }}">
                        @csrf
                        <div class="card-body"> 
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Region</label>
                                        <select name="region_id"
                                            class="selectpicker form-control" data-live-search="true">
                                            <option disabled selected>Search Region</option>
                                            @foreach($vendorRegions as $vendorRegion)
                                                <option value="{{$vendorRegion->id}}">
                                                    {{$vendorRegion->english_name}}
                                                </option>
                                            @endforeach
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Community</label>
                                        <select name="community_id"
                                            class="selectpicker form-control" data-live-search="true">
                                            <option disabled selected>Search Community</option>
                                            @foreach($communities as $community)
                                                <option value="{{$community->id}}">
                                                    {{$community->english_name}}
                                                </option>
                                            @endforeach
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Town</label>
                                        <select name="town_id" class="selectpicker form-control"
                                            data-live-search="true" >
                                            <option disabled selected>Search Town</option>
                                            @foreach($towns as $town)
                                                <option value="{{$town->id}}">
                                                    {{$town->english_name}}
                                                </option>
                                            @endforeach
                                        </select> 
                                    </fieldset>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Service</label>
                                        <select name="service_id" class="selectpicker form-control"
                                            data-live-search="true" >
                                            <option disabled selected>Search Service</option>
                                            @foreach($services as $service)
                                                <option value="{{$service->id}}">
                                                    {{$service->service_name}}
                                                </option>
                                            @endforeach
                                        </select> 
                                    </fieldset>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <fieldset class="form-group">
                                        <label class='col-md-12 control-label'>Vendor</label>
                                        <select name="vendor_id" class="selectpicker form-control"
                                            data-live-search="true" >
                                            <option disabled selected>Search Vendor</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{$vendor->id}}">
                                                    {{$vendor->english_name}}
                                                </option>
                                            @endforeach
                                        </select> 
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <label class='col-md-12 control-label'>Download Excel</label>
                                    <button class="btn btn-info" type="submit" id="exportVendingHistoryButton">
                                        <i class='fa-solid fa-file-excel'></i>
                                        Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
        </div> 
    </div> 
</div>


<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Vending </span> Points and Visiting
</h4>

@if(session()->has('message'))
    <div class="row">
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    </div>
@endif


<div class="container">
    <div class="card my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter by Region</label>
                        <select name="vendor_region" class="selectpicker form-control"
                            data-live-search="true" id="filterByRegion">
                            <option disabled selected>Search Region</option>
                            @foreach($vendorRegions as $vendorRegion)
                                <option value="{{$vendorRegion->id}}">
                                    {{$vendorRegion->english_name}}
                                </option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter by Community</label>
                        <select name="vendor_community" class="selectpicker form-control"
                            data-live-search="true" id="filterByCommunity">
                            <option disabled selected>Search Community</option>
                            @foreach($communities as $community)
                                <option value="{{$community->id}}">
                                    {{$community->english_name}}
                                </option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter by Town</label>
                        <select name="vendor_town" class="selectpicker form-control"
                            data-live-search="true" id="filterBytown">
                            <option disabled selected>Search Town</option>
                            @foreach($towns as $town)
                                <option value="{{$town->id}}">
                                    {{$town->english_name}}
                                </option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter by Service</label>
                        <select name="service" class="selectpicker form-control"
                            data-live-search="true" id="filterByService">
                            <option disabled selected>Search Service</option>
                            @foreach($services as $service)
                                <option value="{{$service->id}}">
                                    {{$service->service_name}}
                                </option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
            </div>
                
            <div class="row">
                <!-- <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter by Vendor</label>
                        <select name="vendor_id" class="selectpicker form-control"
                            data-live-search="true" id="filterByVendor">
                            <option disabled selected>Search Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{$vendor->id}}">
                                    {{$vendor->english_name}}
                                </option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div> -->
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Clear All Filters</label>
                        <button class="btn btn-dark" id="clearFiltersButton">
                            <i class='fa-solid fa-eraser'></i>
                            Clear Filters
                        </button>
                    </fieldset>
                </div>
            </div>
               
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role="tablist" style="padding-top:25px">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#vending-history" role="tab">
                        <i class='fas fa-industry me-2'></i>
                        Visiting Follow up
                        <span id="vendingHistoryCount" class="badge ms-2" style="background-color: #d6f7fa; color: #00cfdd;">
                     
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#all-vendors" role="tab">
                        <i class='fas fa-store me-2'></i> 
                        All Vendors
                        <span id="vendorsCount" class="badge ms-2" style="background-color: #d6f7fa; color: #00cfdd;">
                     
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3">
                <!-- Vending History Tab -->
                <div class="tab-pane fade show active" id="vending-history" role="tabpanel"
                    aria-labelledby="vendingHistory-tab">
                    @if(Auth::guard('user')->user()->user_type_id == 1 ||
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 3 ||
                        Auth::guard('user')->user()->user_type_id == 4 )
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4">
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#createNewVendingHistory">
                                    Create New Vending History		
                                </button>
                                @include('vendor.history.create')
                            </div>

                            <div class="col-xl-6 col-lg-6 col-md-6">
                                <form action="{{ route('vending-history.import') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row align-items-center">
                                        <!-- File Input -->
                                        <div class="col-4">
                                            <input type="file" name="excel_file" class="form-control" id="excel_file" required>
                                            @error('excel_file')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div> 

                                        <!-- Button -->
                                        <div class="col-8">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fa-solid fa-upload"></i> Import Collecting Money File
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    @endif
                    <table id="vendingHistoryTable" class="table table-striped data-table-vending-history my-2">
                        <thead>
                            <tr>
                                <th class="text-center">Vendor Name</th>
                                <th class="text-center">Service</th>
                                <th class="text-center">Visit Date</th>
                                <th class="text-center">From Date</th>
                                <th class="text-center">To Date</th>
                                <th class="text-center">Total Money</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- All Vendors Tab -->
                <div class="tab-pane fade" id="all-vendors" role="tabpanel" aria-labelledby="vendors-tab">
                    @if(Auth::guard('user')->user()->user_type_id == 1 ||
                        Auth::guard('user')->user()->user_type_id == 2 ||
                        Auth::guard('user')->user()->user_type_id == 3 ||
                        Auth::guard('user')->user()->user_type_id == 4 )
                        <div>
                            <a type="button" class="btn btn-success" target="_blank"
                                href="{{url('vending-point', 'create')}}" >
                                Create New Vending Point	
                            </a>
                        </div>
                    @endif
                    <table id="vendingPointTable" class="table table-striped data-table-vending-point my-2">
                        <thead>
                            <tr>
                                <th class="text-center">English Name</th>
                                <th class="text-center">Arabic Name</th>
                                <th class="text-center">Region</th>
                                <th class="text-center">Phone Number</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Services</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('vendor.show')
@include('vendor.history.show')

<script>
 
    $('#exportVendingHistoryButton').on('submit', function (event) {

        event.preventDefault(); 

        let valid = true;

        var fileTypeValue = $('#fileType').val();

        if (fileTypeValue == null) {

            $('#file_type_error').html('Please select a type!'); 
            return false;
        } else  if (fileTypeValue != null) {

            $('#file_type_error').empty();
        }

        $('#file_type_error').empty();

        if (valid) {

            $(this).addClass('was-validated');
            this.submit(); 
        }
    });

    // Update the countable values for water 
    function updateCountValue() {

        $.ajax({
            url: "{{ route('vending.counts') }}",
            type: "GET",
            success: function(response) {

                $("#vendingHistoryCount").text(response.vendingHistoryCount);
                $("#vendorsCount").text(response.vendorsCount);
            }
        });
    }

    // Clear Filters for Export
    $('#clearEnergyHolderFiltersButton').on('click', function() {

        $('.selectpicker').prop('selectedIndex', 0);
        $('.selectpicker').selectpicker('refresh');
    });

    $(function () {

        // keep track of initialized tables
        var tables = {};

        function initVendingHistoryTable() {

            if (tables.vendingHistory) return;
            tables.vendingHistory = $('.data-table-vending-history').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('vending-history.index') }}",
                    data: function (d) {
                        d.region_filter = $('#filterByRegion').val();
                        d.community_filter = $('#filterByCommunity').val();
                        d.town_filter = $('#filterBytown').val();
                        d.service_filter = $('#filterByService').val();
                        d.vendor_filter = $('#filterByVendor').val();
                    }
                },
                columns: [
                    {data: 'vendor', name: 'vendor'},
                    {data: 'service_name', name: 'service_name'},
                    {data: 'visit_date', name: 'visit_date'},
                    {data: 'collecting_date_from', name: 'collecting_date_from'},
                    {data: 'collecting_date_to', name: 'collecting_date_to'},
                    {data: 'total_amount_due', name: 'total_amount_due'},
                    {data: 'action'} 
                ]
            });
        }

        function initVendorsTable() {

            if (tables.vendors) return;
            tables.vendors = $('.data-table-vending-point').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('vending-point.index') }}",
                    data: function (d) {
                        d.region_filter = $('#filterByRegion').val();
                        d.community_filter = $('#filterByCommunity').val();
                        d.town_filter = $('#filterBytown').val();
                        d.service_filter = $('#filterByService').val();
                        d.vendor_filter = $('#filterByVendor').val();
                    }
                },
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'arabic_name', name: 'arabic_name'},
                    {data: 'region', name: 'region'},
                    {data: 'phone_number', name: 'phone_number'},
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data) {
                            if (data === 'Active') {
                                return `<span class="badge bg-success">
                                            <i class="fa fa-check-circle"></i> Active
                                        </span>`;
                            } else {
                                return `<span class="badge bg-danger">
                                            <i class="fa fa-times-circle"></i> Not Active
                                        </span>`;
                            }
                        }
                    },
                    {data: 'services', name: 'services'},
                    {data: 'action'},
                ]
            });
        }


        initVendingHistoryTable();
        updateCountValue();

        // This function called after deletion
        function resetDataForAllTables() {

            $('#vendingHistoryTable').DataTable().draw();
            $('#vendingPointTable').DataTable().draw();
            updateCountValue();
        }

        // On tab shown, lazy-init the target table
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr('href');
            if (target == '#all-vendors') initVendorsTable();
            if (target == '#vending-history') initVendingHistoryTable();

            if ($('.selectpicker').length && typeof $('.selectpicker').selectpicker === 'function') {

                $('.selectpicker').selectpicker('refresh');
            }
        });

        // Reload initialized tables when any filter changes
        function reloadInitializedTables() {

            if (tables.vendors) tables.vendors.ajax.reload();
            if (tables.vendingHistory) tables.vendingHistory.ajax.reload();
            updateCountValue();
        }

        $('#filterBytown, #filterByCommunity, #filterByRegion, #filterByService').on('change', function () {

            reloadInitializedTables();
            updateCountValue();
        });

        // Clear filters
        $(document).on('click', '#clearFiltersButton', function () {

            $('#filterByRegion').prop('selectedIndex', 0);
            $('#filterByCommunity').prop('selectedIndex', 0);
            $('#filterBytown').prop('selectedIndex', 0);
            $('#filterByService').prop('selectedIndex', 0);
            $('#filterByVendor').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');

            reloadInitializedTables();
            updateCountValue();
        });


        // Delete vending point
        $('#vendingPointTable').on('click', '.deleteVendor',function() {

            var id = $(this).data('id');
            Swal.fire({ 
                icon: 'warning',
                title: 'Are you sure you want to delete this vendor?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteVendor') }}",
                        type: 'get',
                        data: {id: id},
                        success: function(response) {
                            if(response.success == 1) {

                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'Okay!'
                                }).then((result) => {
                                    reloadInitializedTables();
                                });
                            } else {

                                alert("Invalid ID.");
                            }
                        }
                    });
                } else if (result.isDenied) {

                    Swal.fire('Changes are not saved', '', 'info')
                }
            });
        });

        // View record details for the vending point
        $('#vendingPointTable').on('click', '.detailsVendorButton',function() {

            var id = $(this).data('id');

            // AJAX request
            $.ajax({
                url: 'vending-point/' + id,
                type: 'get',
                dataType: 'json', 
                success: function(response) { 

                    $('#vendorModalTitle').html(" ");
                    $('#englishNameVendingPoint').html(" ");
                    $('#arabicNameVendingPoint').html(" ");
                    $('#vendorModalTitle').html(response['vendor'].english_name);
                    $('#englishNameVendingPoint').html(response['vendor'].english_name);
                    $('#arabicNameVendingPoint').html(response['vendor'].arabic_name);

                    $('#phoneNumberVendingPoint').html(" ");
                    $('#phoneNumberVendingPoint').html(response['vendor'].phone_number);
                    $('#additionalphoneNumberVendingPoint').html(" ");
                    $('#additionalphoneNumberVendingPoint').html(response['vendor'].additional_phone_number);
                    $('#regionVendingPoint').html(" ");
                    if(response['vendorRegion']) $('#regionVendingPoint').html(response['vendorRegion'].english_name);
                    $('#locationVendingPoint').html(" ");
                    if(response['community']) $('#locationVendingPoint').html(response['community'].english_name); 
                    else if(response['town']) $('#locationVendingPoint').html(response['town'].english_name);

                    $('#notesVendingPoint').html(" ");
                    $('#notesVendingPoint').html(response['vendor'].notes);
                    $('#servicesVendingPoint').html(" ");

                    let container = $("#communitiesVendingPoint");
                    container.html(''); 

                    if (response.vendorCommunities && response.vendorCommunities.length > 0) {

                        let grouped = {};

                        // group by service
                        response.vendorCommunities.forEach(item => {
                            if (!grouped[item.service_name]) {
                                grouped[item.service_name] = [];
                            }
                            grouped[item.service_name].push(item.english_name);
                        });

                        // render
                        for (let service_name in grouped) {
                            container.append(
                                `<div class="mb-2">
                                    <strong>${service_name}:</strong> ${grouped[service_name].join(', ')}
                                </div>`
                            );
                        }
                    }

                    $('#usernameVendingPoint').html(" ");
                    if(response['vendorServices'] != []) {

                        let addedServices = new Set();

                        // Loop through your response
                        for (let i = 0; i < response['vendorServices'].length; i++) {
                            let serviceName = response['vendorServices'][i].service_name;

                            if (!addedServices.has(serviceName)) {
                                $("#servicesVendingPoint").append('<li>' + serviceName + '</li>');
                                addedServices.add(serviceName);
                            }
                        }
                        for (var i = 0; i < response['vendorServices'].length; i++) {

                            $("#usernameVendingPoint").append(
                            '<ul><li>'+ response['vendorServices'][i].service_name + ' : ' + 
                            response['vendorServices'][i].name + '</li></ul>' );  
                        }
                    }
                }
            });
        }); 

        // View update
        $('#vendingPointTable').on('click', '.updateVendor',function() {

            var id = $(this).data('id');
            var url = '/vending-point/' + id + '/edit';
    
            window.open(url, "_blank"); 
        });



        // Delete vending point for the vending history
        $('#vendingHistoryTable').on('click', '.deleteAllVendingHistory',function() {

            var id = $(this).data('id');
            Swal.fire({ 
                icon: 'warning',
                title: 'Are you sure you want to delete this vending history?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteVendingHistory') }}",
                        type: 'get',
                        data: {id: id},
                        success: function(response) {
                            if(response.success == 1) {

                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'Okay!'
                                }).then((result) => {
                                    reloadInitializedTables();
                                });
                            } else {

                                alert("Invalid ID.");
                            }
                        }
                    });
                } else if (result.isDenied) {

                    Swal.fire('Changes are not saved', '', 'info')
                }
            });
        });

        // View record details for the vending history
        $('#vendingHistoryTable').on('click', '.viewVendingHistory',function() {

            var id = $(this).data('id');

            // AJAX request
            $.ajax({
                url: 'vending-history/' + id,
                type: 'get',
                dataType: 'json', 
                success: function(response) { 

                    $('#vendingHistoryModalTitle').html(" ");
                    $('#vendingPointEnglishName').html(" ");
                    $('#vendingPointArabicName').html(" ");
                    $('#vendingHistoryModalTitle').html(response['vendor'].english_name);
                    $('#vendingPointEnglishName').html(response['vendor'].english_name);
                    $('#vendingPointArabicName').html(response['vendor'].arabic_name);

                    $('#vendingPointPhoneNumber').html(" ");
                    $('#vendingPointPhoneNumber').html(response['vendor'].phone_number);
                    $('#vendingPointAdditionalphoneNumber').html(" ");
                    $('#vendingPointAdditionalphoneNumber').html(response['vendor'].additional_phone_number);
                    $('#vendingPointUsername').html(" ");
                    if(response['vendorUserName']) $('#vendingPointUsername').html(response['vendorUserName'].name);
                    $('#vendingPointRegion').html(" ");
                    if(response['vendorRegion']) $('#vendingPointRegion').html(response['vendorRegion'].english_name);
                    $('#vendingPointLocation').html(" ");
                    if(response['community']) $('#vendingPointLocation').html(response['community'].english_name); 
                    else if(response['town']) $('#vendingPointLocation').html(response['town'].english_name);

                    $('#vendingPointVisitDate').html(" ");
                    $('#vendingPointVisitDate').html(response['vendingHistory'].visit_date);
                    $('#vendingPointVisitFrom').html(" ");
                    $('#vendingPointVisitFrom').html(response['vendingHistory'].collecting_date_from);
                    $('#vendingPointVisitTo').html(" ");
                    $('#vendingPointVisitTo').html(response['vendingHistory'].collecting_date_to);
                    $('#vendingPointTotalAmount').html(" ");
                    $('#vendingPointTotalAmount').html(response['vendingHistory'].total_amount_due);
                    $('#vendingPointAmountCollected').html(" ");
                    $('#vendingPointAmountCollected').html(response['vendingHistory'].amount_collected);
                    $('#vendingPointRemainingBalance').html(" ");
                    $('#vendingPointRemainingBalance').html(response['vendingHistory'].remaining_balance);
                    $('#vendingPointVisitBy').html(" ");
                    $('#vendingPointVisitBy').html(response['user'].name);
                    $('#vendingHistoryNotes').html(" ");
                    $('#vendingHistoryNotes').html(response['vendingHistory'].notes);
                }
            });
        });

        // View update for the vending history
        $('#vendingHistoryTable').on('click', '.updateAllVendingHistory',function() {

            var id = $(this).data('id');
            var url = '/vending-history/' + id + '/edit';
    
            window.open(url, "_blank"); 
        });
    });
</script>
@endsection