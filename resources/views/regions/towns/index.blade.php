@php
  $pricingModal = true;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'towns')

@include('layouts.all')

@section(section: 'content')

<style>
/* Ensure SweetAlert appears above Bootstrap modals */
.swal2-container {
    z-index: 9999 !important;
}
.swal2-popup {
    z-index: 10000 !important;
}
</style>

<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">All </span> Towns
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
            <div class="mb-3">
                @if(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2)
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTownModal">
                    Create New Town
                </button>
                <button type="button" class="btn btn-info ms-2" id="exportTownsBtn" title="Export Towns">
                    <i class="fa-solid fa-download fs-5"></i> Export Towns
                </button>
                @endif
            </div>
            <table id="townTable" class="table table-striped data-table-towns my-2">
                <thead>
                    <tr>
                        <th>English Name</th>
                        <th>Arabic Name</th>
                        <th>Region</th>
                        @if(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2)
                        <th>Options</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Town Modal -->
<div class="modal fade" id="createTownModal" tabindex="-1" aria-labelledby="createTownModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createTownModalLabel">Create New Town</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="createTownForm">
                    <div class="mb-3">
                        <label for="createEnglishName" class="form-label">English Name</label>
                        <input type="text" class="form-control" id="createEnglishName" name="english_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="createArabicName" class="form-label">Arabic Name</label>
                        <input type="text" class="form-control" id="createArabicName" name="arabic_name">
                    </div>
                    <div class="mb-3">
                        <label for="createRegionId" class="form-label">Region</label>
                        <select class="form-control" id="createRegionId" name="region_id" required>
                            <option value="">Select Region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->english_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="createTown()">Create Town</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Town Modal -->
<div class="modal fade" id="editTownModal" tabindex="-1" aria-labelledby="editTownModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTownModalLabel">Edit Town</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTownForm">
                    <input type="hidden" id="editTownId" name="town_id">
                    <div class="mb-3">
                        <label for="editEnglishName" class="form-label">English Name</label>
                        <input type="text" class="form-control" id="editEnglishName" name="english_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editArabicName" class="form-label">Arabic Name</label>
                        <input type="text" class="form-control" id="editArabicName" name="arabic_name">
                    </div>
                    <div class="mb-3">
                        <label for="editRegionId" class="form-label">Region</label>
                        <select class="form-control" id="editRegionId" name="region_id" required>
                            <option value="">Select Region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->english_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateTown()">Update Town</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(function () {

        // DataTable
        var table = $('.data-table-towns').DataTable({
            processing: false,
            serverSide: false,
            data: @json($towns ?? []), // Use the towns data passed from controller
            columns: [
                {data: 'english_name', name: 'english_name'},
                {data: 'arabic_name', name: 'arabic_name', 
                 render: function(data, type, row) {
                     return data || 'N/A'; // Handle null arabic names
                 }
                },
                {data: 'region.english_name', name: 'region.english_name',
                 render: function(data, type, row) {
                     return data || (row.region ? row.region.english_name : 'N/A'); // Handle region names
                 }
                }@if(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2),
                {data: null, name: 'actions',
                 render: function(data, type, row) {
                     var actions = '';
                     actions += '<i class="fas fa-edit text-success me-3" onclick="editTown(' + row.id + ')" title="Edit" style="cursor: pointer; font-size: 16px;"></i>';
                     actions += '<i class="fas fa-trash text-danger" onclick="deleteTown(' + row.id + ')" title="Delete" style="cursor: pointer; font-size: 16px;"></i>';
                     return actions;
                 }
                }@endif
            ],
            
        });
    });

    // Edit function
    function editTown(id) {
        // Check if user has permission to edit
        @if(!(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2))
        Swal.fire({
            icon: 'error',
            title: 'Access Denied!',
            text: 'You do not have permission to edit towns!',
            confirmButtonText: 'Okay'
        });
        return;
        @endif

        // Find the town data from the table
        var tableData = $('#townTable').DataTable().data();
        var townData = null;
        
        for (var i = 0; i < tableData.length; i++) {
            if (tableData[i].id == id) {
                townData = tableData[i];
                break;
            }
        }
        
        if (townData) {
            // Populate the edit modal with town data
            $('#editTownId').val(townData.id);
            $('#editEnglishName').val(townData.english_name);
            $('#editArabicName').val(townData.arabic_name || '');
            $('#editRegionId').val(townData.region_id || (townData.region ? townData.region.id : ''));
            
            // Show the edit modal
            $('#editTownModal').modal('show');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Town not found!',
                confirmButtonText: 'Okay'
            });
        }
    }

    // Delete function
    function deleteTown(id) {
        // Check if user has permission to delete
        @if(!(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2))
        Swal.fire({
            icon: 'error',
            title: 'Access Denied!',
            text: 'You do not have permission to delete towns!',
            confirmButtonText: 'Okay'
        });
        return;
        @endif

        Swal.fire({
            icon: 'warning',
            title: 'Are you sure you want to delete this town?',
            showDenyButton: true,
            confirmButtonText: 'Confirm',
            denyButtonText: 'Cancel'
        }).then((result) => {
            if(result.isConfirmed) {
                // AJAX call to delete town
                $.ajax({
                    url: "{{ route('deleteTown') }}",
                    method: 'GET',
                    data: { id: id },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Town deleted successfully!',
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: 'Okay!'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Failed to delete town',
                                confirmButtonText: 'Okay'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = 'Failed to delete town';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonText: 'Okay'
                        });
                    }
                });
            }
        });
    }

    // Create Town function
    function createTown() {
        // Check if user has permission to create
        @if(!(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2))
        Swal.fire({
            icon: 'error',
            title: 'Access Denied!',
            text: 'You do not have permission to create towns!',
            confirmButtonText: 'Okay'
        });
        return;
        @endif

        console.log('createTown function called');
        var englishName = $('#createEnglishName').val();
        var arabicName = $('#createArabicName').val();
        var regionId = $('#createRegionId').val();

        console.log('Form values:', {
            englishName: englishName,
            arabicName: arabicName,
            regionId: regionId
        });

        if (!englishName) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error!',
                text: 'English name is required!',
                confirmButtonText: 'Okay'
            });
            return;
        }

        if (!regionId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error!',
                text: 'Region is required!',
                confirmButtonText: 'Okay'
            });
            return;
        }

        // AJAX call to create town
        $.ajax({
            url: "{{ route('towns.store') }}",
            method: 'POST',
            data: {
                english_name: englishName,
                arabic_name: arabicName,
                region_id: regionId,
                _token: "{{ csrf_token() }}"
            },
            beforeSend: function() {
                console.log('Creating town...', {
                    url: "{{ route('towns.store') }}",
                    english_name: englishName,
                    arabic_name: arabicName,
                    region_id: regionId,
                    _token: "{{ csrf_token() }}"
                });
            },
            success: function(response) {
                console.log('Town creation response:', response);
                if (response.success) {
                    // Close modal and reset form
                    $('#createTownModal').modal('hide');
                    $('#createTownForm')[0].reset();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Town Created Successfully!',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Okay!'
                    }).then((result) => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to create town',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Town creation failed:', {
                    status: status,
                    error: error,
                    response: xhr.responseJSON,
                    responseText: xhr.responseText,
                    statusCode: xhr.status,
                    headers: xhr.getAllResponseHeaders()
                });
                
                var errorMessage = 'Failed to create town';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join(', ');
                } else if (xhr.responseText) {
                    errorMessage = 'Server Error: ' + xhr.responseText.substring(0, 200);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonText: 'Okay'
                });
            }
        });
    }

    // Update Town function
    function updateTown() {
        // Check if user has permission to update
        @if(!(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2))
        Swal.fire({
            icon: 'error',
            title: 'Access Denied!',
            text: 'You do not have permission to update towns!',
            confirmButtonText: 'Okay'
        });
        return;
        @endif

        var townId = $('#editTownId').val();
        var englishName = $('#editEnglishName').val();
        var arabicName = $('#editArabicName').val();
        var regionId = $('#editRegionId').val();

        if (!englishName) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error!',
                text: 'English name is required!',
                confirmButtonText: 'Okay'
            });
            return;
        }

        if (!regionId) {
            Swal.fire({
                icon: 'warning',
                title: 'Validation Error!',
                text: 'Region is required!',
                confirmButtonText: 'Okay'
            });
            return;
        }

        if (!townId) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Town ID is missing!',
                confirmButtonText: 'Okay'
            });
            return;
        }

        // AJAX call to update town
        $.ajax({
            url: "/towns/" + townId,
            method: 'PUT',
            data: {
                english_name: englishName,
                arabic_name: arabicName,
                region_id: regionId,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    // Close modal and reset form
                    $('#editTownModal').modal('hide');
                    $('#editTownForm')[0].reset();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Town Updated Successfully!',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Okay!'
                    }).then((result) => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'Failed to update town',
                        confirmButtonText: 'Okay'
                    });
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = 'Failed to update town';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors).flat().join(', ');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage,
                    confirmButtonText: 'Okay'
                });
            }
        });
    }
        // Export towns handler
    @if(Auth::guard('user')->user()->user_type_id == 1 || Auth::guard('user')->user()->user_type_id == 2)
    document.addEventListener('DOMContentLoaded', function() {
        var exportBtn = document.getElementById('exportTownsBtn');
        if (exportBtn) {
            exportBtn.addEventListener('click', function() {
                window.location.href = "{{ url('api/town/export') }}";
            });
        }
    });
    @endif
</script>

@endsection