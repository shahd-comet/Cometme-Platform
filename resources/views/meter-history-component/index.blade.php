@extends('layouts/layoutMaster')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Meter History Components</h4>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <!-- Statuses Section -->
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">Meter History Statuses</h5>
                    <div class="card-body">
                        <!-- Add Status Form -->
                        <form method="POST" action="{{ route('meter-history.store-status') }}" class="mb-4">
                            @csrf
                            <div class="mb-3 d-flex ">
                                <div class="m-2">
                                    <label for="status_name" class="form-label">English Name</label>
                                    <input type="text" class="form-control" id="status_name" name="english_name" required>
                                </div>
                                <div class="m-2">
                                    <label for="status_name" class="form-label">Arabic Name</label>
                                    <input type="text" class="form-control" id="status_name" name="arabic_name">

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Status</button>
                        </form>

                        <!-- Status List -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>English Name</th>
                                        <th>Arabic Name</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statuses as $status)
                                        <tr>
                                            <td>{{ $status->english_name }}</td>
                                            <td>{{ $status->arabic_name }}</td>
                                            <td>
                                                <a href="#" class="editStatus" data-bs-toggle="modal"
                                                    data-bs-target="#editStatusModal{{ $status->id }}">
                                                    <i class="fa-solid fa-pen-to-square text-info"></i>
                                                </a>


                                                <a href="{{ route('meter-history.delete-status', $status) }}"
                                                    class="deleteMeterHistoryStatus" data-id="{{ $status->id }}">
                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                </a>

                                                <form id="delete-form-{{ $status->id }}"
                                                    action="{{ route('meter-history.delete-status', $status) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                            </td>
                                        </tr>
                                        <!-- Edit Status Modal -->
                                        <div class="modal fade" id="editStatusModal{{ $status->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Status</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('meter-history.update-status', $status) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">English Name</label>
                                                                <input type="text" class="form-control" name="english_name"
                                                                    value="{{ $status->english_name }}" required>
                                                                <label class="form-label">Arabic Name</label>
                                                                <input type="text" class="form-control" name="arabic_name"
                                                                    value="{{ $status->arabic_name }}" >
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reasons Section -->
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">Meter History Reasons</h5>
                    <div class="card-body">
                        <!-- Add Reason Form -->
                        <form method="POST" action="{{ route('meter-history.store-reason') }}" class="mb-4">
                            @csrf
                            <div class="mb-3 d-flex">
                                <div class="m-2">
                                    <label for="reason_name" class="form-label">English Name</label>
                                    <input type="text" class="form-control" id="reason_name" name="english_name" required>

                                </div>
                                <div class="m-2">
                                    <label for="reason_name" class="form-label">Arabic Name</label>
                                    <input type="text" class="form-control" id="reason_name" name="arabic_name" >

                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Add Reason</button>
                        </form>

                        <!-- Reason List -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>English Name</th>
                                        <th>Arabic Name</th>
                                        <th >Options</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reasons as $reason)
                                        <tr>
                                            <td>{{ $reason->english_name }}</td>
                                            <td>{{ $reason->arabic_name }}</td>
                                            <td >
                                                <a href="#" class="editReason" data-bs-toggle="modal"
                                                    data-bs-target="#editReasonModal{{ $reason->id }}">
                                                    <i class="fa-solid fa-pen-to-square text-info"></i>
                                                </a>


                                                <a href="{{ route('meter-history.delete-reason', $reason) }}"
                                                    class="deleteMeterHistoryReason" data-id="{{ $reason->id }}">
                                                    <i class="fa-solid fa-trash text-danger"></i>
                                                </a>

                                                <form id="delete-form-{{ $reason->id }}"
                                                    action="{{ route('meter-history.delete-reason', $reason) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                            </td>
                                        </tr>
                                        <!-- Edit Reason Modal -->
                                        <div class="modal fade" id="editReasonModal{{ $reason->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Reason</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('meter-history.update-reason', $reason) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">

                                                                <label class="form-label">English Name</label>
                                                                <input type="text" class="form-control" name="english_name"
                                                                    value="{{ $reason->english_name }}" required>
                                                                    
                                                                <label class="form-label">Arabic Name</label>
                                                                <input type="text" class="form-control" name="arabic_name"
                                                                    value="{{ $reason->arabic_name }}" >
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 CDN and delete confirmation handler -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = document.querySelectorAll('.deleteMeterHistoryStatus, .deleteMeterHistoryReason');
            selector.forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const form = document.getElementById('delete-form-' + id);
                    const type = this.classList.contains('deleteMeterHistoryStatus') ? 'status' : 'reason';
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (form) form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection