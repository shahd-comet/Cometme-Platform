@extends('layouts/layoutMaster')

@section('title', 'Returned Cameras')

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
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseReturnedVisualData" 
        role="button" aria-expanded="false" aria-controls="collapseReturnedVisualData">
        <i class="bx bx-show-alt"></i>
        Visualize Data
    </a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseReturnedExport" aria-expanded="false" 
        aria-controls="collapseReturnedExport">
        <i class="bx bx-export"></i>
        Export Data
    </button>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseReturnedVisualData collapseReturnedExport">
        <i class="bx bx-expand-alt"></i>
        Toggle All
    </button>
</p> 

<div class="collapse multi-collapse mb-4" id="collapseReturnedVisualData">
    {{-- placeholder for charts --}}
</div>

<h4 class="py-3 breadcrumb-wrapper mb-4">
    All <span class="text-muted fw-light">Returned Cameras</span>
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
                        <option disabled selected>Choose Sub Region</option>
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
                    <label>Date of Return</label>
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
                <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createReturned">
                    Create Returned Record
                </button>
                @include('services.camera.returned.create')
            @endif

            <table id="returnedTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Community</th>
                        <th>Compound</th>
                        <th>Date</th>
                        <th># Cameras</th>
                        <th>Camera Type</th>
                        <th>NVR</th>
                        <th># NVRs</th>
                        <th>Notes</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let table;

    function initTable() {
        if ($.fn.DataTable.isDataTable('#returnedTable')) {
            $('#returnedTable').DataTable().clear().destroy();
        }

        table = $('#returnedTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('camera-returned.index') }}",
                data: function (d) {
                    d.region_filter = $('#filterByRegion').val();
                    d.community_filter = $('#filterByCommunity').val();
                    d.date_filter = $('#filterByDate').val();
                }
            },
                columns: [
                { data: 'community', name: 'community' },
                { data: 'compound', name: 'compound' },
                { data: 'date_of_return', name: 'date_of_return' },
                { data: 'number_of_cameras', name: 'number_of_cameras' },
                { data: 'camera_type', name: 'camera_type' },
                { data: 'nvr', name: 'nvr' },
                { data: 'number_of_nvr', name: 'number_of_nvr' },
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

        // Delete record
        $('#returnedTable').on('click', '.deleteReturned', function () {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this record?',
                showDenyButton: true,
                confirmButtonText: 'Confirm',
                denyButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('camera-returned.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id
                        },
                        success: function (response) {
                            if (response.success == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'Okay!'
                                }).then(() => {
                                    $('#returnedTable').DataTable().draw();
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
                    Swal.fire('Changes are not saved', '', 'info');
                }
            });
        });
    });
</script>

@endsection
