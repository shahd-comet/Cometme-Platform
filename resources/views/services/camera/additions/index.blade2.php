@extends('layouts/layoutMaster')

@section('title', 'Camera Additions')

@include('layouts.all')

@section('content')

<style>
    label, input {
        display: block;
    }

    label, table {
        margin-top: 20px;
    }
</style>

<p>
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseAdditionVisualData" 
        role="button" aria-expanded="false" aria-controls="collapseAdditionVisualData">
        <i class="bx bx-show-alt"></i>
        Visualize Data
    </a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseAdditionExport" aria-expanded="false" 
        aria-controls="collapseAdditionExport">
        <i class="bx bx-export"></i>
        Export Data
    </button>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseAdditionVisualData collapseAdditionExport">
        <i class="bx bx-expand-alt"></i>
        Toggle All
    </button>
</p> 

<div class="collapse multi-collapse mb-4" id="collapseAdditionVisualData">
</div>

<div class="collapse multi-collapse container mb-4" id="collapseAdditionExport">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xl-10">
                            <h5>
                                Export Additions Report
                                <i class='bx bx-file'></i>
                            </h5>
                        </div>
                        <div class="col-xl-2">
                            <button class="btn btn-secondary" id="clearExportFilters">
                                <i class='bx bx-eraser'></i> Clear Filters
                            </button>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('camera-additions.export') }}">
                    @csrf
                    <div class="card-body row">
                        <div class="col-md-4">
                            <select name="sub_region" class="selectpicker form-control" data-live-search="true">
                                <option disabled selected>Choose Sub Region</option>
                                @foreach($subRegions as $region)
                                    <option value="{{ $region->id }}">{{ $region->english_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="community" class="selectpicker form-control" data-live-search="true">
                                <option disabled selected>Choose Community</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="date" class="form-control">
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-info">
                                <i class='bx bx-file'></i> Export Excel
                            </button>
                        </div>
                    </div>
                </form>
            </div>  
        </div>
    </div> 
</div> 

<h4 class="py-3 breadcrumb-wrapper mb-4">
    All <span class="text-muted fw-light">Camera Additions</span>
</h4>

@if(session()->has('message'))
    <div class="row">
        <div class="alert alert-success">{{ session('message') }}</div>
    </div>
@endif 

<div class="container"> 
    <div class="card my-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-4">
                    <label>Filter by Region</label>
                    <select id="filterByRegion" class="selectpicker form-control" data-live-search="true">
                        <option disabled selected>Choose Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->english_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Filter by Community</label>
                    <select id="filterByCommunity" class="selectpicker form-control" data-live-search="true">
                        <option disabled selected>Choose Community</option>
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}">{{ $community->english_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Date of Addition</label>
                    <input type="date" id="filterByDate" class="form-control">
                </div>
            </div>
            <div class="mt-3 text-end">
                <button class="btn btn-dark" id="clearFilters">
                    <i class='bx bx-eraser'></i> Clear Filters
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(in_array(Auth::guard('user')->user()->user_type_id, [1,6,10]))
                <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createAddition">
                    Create New One
                </button>
                @include('services.camera.additions.create')
            @endif

            <table id="additionTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Community</th>
                        <th>Compound</th>
                        <th>Date</th>
                        <th># Cameras</th>
                        <th>SD Card Number</th>
                        <th>Camera Type</th>
                        <th>NVR</th>
                        <th># NVRs</th>
                        <th>Donors</th>
                        <th>Notes</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let table;

    function initTable() {
        if ($.fn.DataTable.isDataTable('#additionTable')) {
            $('#additionTable').DataTable().clear().destroy();
        }

        table = $('#additionTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('camera-additions.index') }}",
                data: function (d) {
                    d.region_filter = $('#filterByRegion').val();
                    d.community_filter = $('#filterByCommunity').val();
                    d.date_filter = $('#filterByDate').val();
                }
            },
            columns: [
                { data: 'community', name: 'community' },
                { data: 'compound', name: 'compound' },
                { data: 'date_of_addition', name: 'date_of_addition' },
                { data: 'number_of_cameras', name: 'number_of_cameras' },
                { data: 'sd_card_number', name: 'sd_card_number' },
                { data: 'camera_type', name: 'camera_type' },
                { data: 'nvr', name: 'nvr' },
                { data: 'number_of_nvr', name: 'number_of_nvr' },
                { data: 'donors', name: 'donors' },
                { data: 'notes', name: 'notes' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    $(function () {
        initTable();

        $('#filterByRegion, #filterByCommunity, #filterByDate').on('change', function () {
            table.ajax.reload();
        });

        $('#clearFilters').on('click', function () {
            $('#filterByRegion, #filterByCommunity').prop('selectedIndex', 0).selectpicker('refresh');
            $('#filterByDate').val('');
            table.ajax.reload();
        });

        $('#clearExportFilters').on('click', function () {
            $('select[name="sub_region"], select[name="community"]').prop('selectedIndex', 0).selectpicker('refresh');
            $('input[name="date"]').val('');
        });

        $(document).on('click', '.updateAddition', function () {
            const id = $(this).data('id');
            window.location.href = "{{ url('camera-additions') }}/" + id + "/edit";
        });

        $(document).on('click', '.deleteAddition', function () {
            const id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this camera addition?',
                showDenyButton: true,
                confirmButtonText: 'Confirm',
                denyButtonText: 'No',
                customClass: {
                    confirmButton: 'btn btn-primary mx-2',
                    denyButton: 'btn btn-danger'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('camera-additions.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function (response) {
                            if (response.success === 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    table.ajax.reload();
                                });
                            } else {
                                Swal.fire('Error', response.msg, 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error', 'An error occurred while deleting.', 'error');
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire('No changes made.', '', 'info');
                }
            });
        });
    });
</script>


@endsection
