@extends('layouts/layoutMaster')

@section('title', 'Camera Replacements')

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
    <a class="btn btn-primary" data-toggle="collapse" href="#collapseReplacementVisualData" 
        role="button" aria-expanded="false" aria-controls="collapseReplacementVisualData">
        <i class="bx bx-show-alt"></i>
        Visualize Data
    </a>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseReplacementExport" aria-expanded="false" 
        aria-controls="collapseReplacementExport">
        <i class="bx bx-export"></i>
        Export Data
    </button>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target=".multi-collapse" aria-expanded="false" 
        aria-controls="collapseReplacementVisualData collapseReplacementExport">
        <i class="bx bx-expand-alt"></i>
        Toggle All
    </button>
</p> 

<div class="collapse multi-collapse mb-4" id="collapseReplacementVisualData">
    {{-- يمكن وضع رسم بياني هنا لاحقًا --}}
</div>

<div class="collapse multi-collapse container mb-4" id="collapseReplacementExport">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xl-10">
                            <h5>
                                Export Replacements Report
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
                <form method="POST" action="{{ route('replacements.export') }}">
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
                        <div class="col-md-4">
                            <label>Filter by Incident Type</label>
                            <select id="filterByIncident" class="selectpicker form-control" data-live-search="true">
                                <option disabled selected>Choose Incident Type</option>
                                @foreach($cameraReplacementIncidents as $incident)
                                    <option value="{{ $incident->id }}">{{ $incident->english_name }}</option>
                                @endforeach
                            </select>
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
    All <span class="text-muted fw-light">Camera Replacements</span>
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
                    <label>Date of Replacement</label>
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
                <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#createReplacement">
                    Create New One
                </button>
                @include('services.camera.replacements.create')
            @endif

            <table id="replacementTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Community</th>
                        <th>Compound</th>
                        <th>Date</th>
                        <th># Damaged</th>
                        <th># New</th>
                        <th># NVRs</th>
                        <th>Incident Type</th>
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

<script>
    let table;

    function initTable() {
        if ($.fn.DataTable.isDataTable('#replacementTable')) {
            $('#replacementTable').DataTable().clear().destroy();
        }

        table = $('#replacementTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('replacements.index') }}",
                data: function (d) {
                    d.region_filter = $('#filterByRegion').val();
                    d.community_filter = $('#filterByCommunity').val();
                    d.date_filter = $('#filterByDate').val();
                    d.incident_filter = $('#filterByIncident').val();
                }
            },
            columns: [
                { data: 'community', name: 'community' },
                { data: 'compound', name: 'compound' },
                { data: 'date_of_replacement', name: 'date_of_replacement' },
                { data: 'damaged_camera_count', name: 'damaged_camera_count' },
                { data: 'new_camera_count', name: 'new_camera_count' },
                { data: 'number_of_nvr', name: 'number_of_nvr' },
                { data: 'incident_type', name: 'incident_type' },
                { data: 'donors', name: 'donors' },
                { data: 'notes', name: 'notes' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    $(function () {
        initTable();

        $('#filterByRegion, #filterByCommunity, #filterByDate, #filterByIncident').on('change', function () {
            table.ajax.reload();
        });

        $('#clearFilters').on('click', function () {
            $('#filterByRegion, #filterByCommunity').prop('selectedIndex', 0).selectpicker('refresh');
            $('#filterByDate').val('');
            $('#filterByIncident').prop('selectedIndex', 0).selectpicker('refresh');
            table.ajax.reload();
        });

        $('#clearExportFilters').on('click', function () {
            $('select[name="sub_region"], select[name="community"]').prop('selectedIndex', 0).selectpicker('refresh');
            $('input[name="date"]').val('');
            $('#filterByIncident').prop('selectedIndex', 0).selectpicker('refresh');
        });

        $(document).on('click', '.updateCamera', function () {
            const id = $(this).data('id');
            window.location.href = "{{ url('replacements') }}/" + id + "/edit";
        });

        // Delete record
$('#replacementTable').on('click', '.deleteCamera', function () {
    var id = $(this).data('id');

    Swal.fire({
        icon: 'warning',
        title: 'Are you sure you want to delete this camera replacement?',
        showDenyButton: true,
        confirmButtonText: 'Confirm',
        denyButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('replacements.delete') }}",
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
                            $('#replacementTable').DataTable().draw();
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
