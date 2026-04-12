    

    <?php $__env->startSection('title', 'Agriculture Users'); ?>

    <?php $__env->startSection('vendor-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/sweetalert2/sweetalert2.css')); ?>" />
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('vendor-script'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/moment/moment.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/select2/select2.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/sweetalert2/sweetalert2.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/extended-ui-sweetalert2.js')); ?>"></script>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('page-script'); ?>
    <script src="<?php echo e(asset('assets/js/tables-datatables-basic.js')); ?>"></script>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>

    <h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">All </span> Agriculture Users
    </h4>

    <?php if(session()->has('message')): ?>
        <div class="row">
            <div class="alert alert-success">
                <?php echo e(session()->get('message')); ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if(session()->has('error')): ?>
        <div class="row">
            <div class="alert alert-danger">
                <?php echo e(session()->get('error')); ?>

            </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <div class="card my-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                            Auth::guard('user')->user()->user_type_id == 2 ||
                            Auth::guard('user')->user()->user_type_id == 14): ?>
                            <a type="button" class="btn btn-success me-2" 
                                href="<?php echo e(url('argiculture-user', 'create')); ?>">
                                <i class="fa-solid fa-plus fs-5"></i> Create New Agriculture User
                            </a>
                        <?php endif; ?>
                    </div>
                                                            <div class="mb-2">
                                                <label class="form-label">Cycle Year</label>
                                                <select id="cycleYearSelect" name="cycle_year" class="form-select">
                                                <option value="">All</option>
                                                <?php if(isset($agricultureSystemCycles)): ?>
                                                    <?php $__currentLoopData = $agricultureSystemCycles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label">Export Type</label>
                                            <select id="exportTypeSelect" name="export_type" class="form-select">
                                                <option value="survey">Agriculture Holders Report</option>
                                                <option value="project_progress">Agriculture Project Progress</option>
                                            </select>
                                        </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-info" id="exportBtn">
                            <i class="fa-solid fa-download fs-5"></i> Export
                        </button>
                        <button type="button" class="btn btn-primary" id="downloadAllQrBtn" title="Download All QR Codes">
                            <i class="fa-solid fa-file-archive fs-5"></i> Download All QR Codes
                        </button>
                        <!-- New Button to download Smartboard Data only -->
                        <button type="button" class="btn btn-secondary" id="exportSmartBtn" title="Download Smartboard Data">
                            <i class="fa-solid fa-desktop fs-5"></i> Smartboard Data Export
                        </button>
                    </div>

                        <script>
                        document.getElementById('exportSmartBtn').addEventListener('click', function () {
                            // Redirect to the smartboard export route to trigger Excel download
                            window.location.href = "<?php echo e(route('argiculture-user.export-smartboard')); ?>";
                        });
                        </script>
                </div>


                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="agricultureUserTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="requested-tab" data-bs-toggle="tab" data-bs-target="#requested-holders" type="button" role="tab" aria-controls="requested-holders" aria-selected="true">
                            <i class="fas fa-clock me-2"></i> All Requested Holders
                            <span class="badge  ms-2" style="background-color: #d6f7fa; color: #00cfdd;"><?php echo e($requestedHolders->count()); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirmed-holders" type="button" role="tab" aria-controls="confirmed-holders" aria-selected="false">
                            <i class="fas fa-check-circle me-2"></i> All Confirmed
                            <span class="badge ms-2" style="background-color: #bcbdbe; color: #28a745;"><?php echo e($confirmedHolders->count()); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="progress-tab" data-bs-toggle="tab" data-bs-target="#progress-holders" type="button" role="tab" aria-controls="progress-holders" aria-selected="false">
                            <i class="fas fa-spinner me-2"></i> All In Progress
                            <span class="badge ms-2" style="background-color: #e7ebef; color: #69809a;"><?php echo e($progressHolders->count()); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-holders" type="button" role="tab" aria-controls="completed-holders" aria-selected="false">
                            <i class="fas fa-check-double me-2"></i> All Completed
                            <span class="badge ms-2" style="background-color: #dff9ec; color: #3fdb8d;"><?php echo e($completedHolders->count()); ?></span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="agricultureUserTabContent">
                    <!-- All Requested Holders Tab -->
                    <div class="tab-pane fade show active" id="requested-holders" role="tabpanel" aria-labelledby="requested-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex gap-2">
                                <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                                    Auth::guard('user')->user()->user_type_id == 2 ||
                                    Auth::guard('user')->user()->user_type_id == 14): ?>
                                    <button type="button" class="btn btn-warning" id="importRequestedBtn">
                                        <i class="fa-solid fa-upload fs-5"></i> Import Requested Holders
                                    </button>
                                <?php endif; ?>
                            </div>

                        </div>
                        <table id="requestedHoldersTable" class="table table-striped my-2">
                            <thead>
                                <tr>
                                    <th class="text-center">Household Name</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Agriculture System</th>
                                    <th class="text-center">Request Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $requestedHolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($holder->household->english_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($holder->community->english_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($holder->agricultureSystems->count() > 0): ?>
                                            <div class="fw-medium">
                                                <?php echo e($holder->agricultureSystems->pluck('name')->filter()->join(', ') ?: 'Unknown Systems'); ?>

                                            </div>
                                            <small class="text-muted"><?php echo e($holder->size_of_herds); ?> sheep • <?php echo e($holder->azolla_unit); ?> units</small>
                                        <?php else: ?>
                                            <span class="text-muted">No systems assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($holder->requested_date): ?>
                                            <?php echo e(is_string($holder->requested_date) ? $holder->requested_date : $holder->requested_date->format('Y-m-d')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('argiculture-user.show', $holder->id)); ?>" class="text-decoration-none me-2" title="View Details">
                                            <i class="fas fa-eye text-primary fs-5"></i>
                                        </a>
                                        <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                                            Auth::guard('user')->user()->user_type_id == 2 ||
                                            Auth::guard('user')->user()->user_type_id == 14): ?>
                                        <a href="<?php echo e(route('argiculture-user.edit', $holder->id)); ?>" class="text-decoration-none me-2" title="Edit">
                                            <i class="fa-solid fa-edit text-warning fs-5"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none me-2" title="Approve" onclick="approveHolder(<?php echo e($holder->id); ?>)">
                                            <i class="fas fa-check text-success fs-5"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none" title="Reject" onclick="rejectHolder(<?php echo e($holder->id); ?>)">
                                            <i class="fas fa-times text-danger fs-5"></i>
                                        </a>
                                        <!-- <a href="<?php echo e(route('argiculture-user.destroy', $holder->id)); ?>" class="text-decoration-none ms-2" title="Delete" onclick='event.preventDefault(); deleteHolder(<?php echo e($holder->id); ?>, <?php echo json_encode($holder->household->english_name ?? 'N/A'); ?>)'>
                                            <i class="fas fa-trash text-danger fs-5"></i>
                                        </a> -->
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        No requested holders found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- All Confirmed Tab -->
                    <div class="tab-pane fade" id="confirmed-holders" role="tabpanel" aria-labelledby="confirmed-tab">
                        <table id="confirmedHoldersTable" class="table table-striped my-2">
                            <thead>
                                <tr>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Agriculture System</th>
                                    <th class="text-center">Confirmation Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $confirmedHolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($holder->household->english_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($holder->community->english_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($holder->agricultureSystems->count() > 0): ?>
                                            <div class="fw-medium text-success">
                                                <?php echo e($holder->agricultureSystems->pluck('name')->filter()->join(', ') ?: 'Unknown Systems'); ?>

                                            </div>
                                            <small class="text-muted"><?php echo e($holder->size_of_herds); ?> sheep • <?php echo e($holder->azolla_unit); ?> units</small>
                                        <?php else: ?>
                                            <span class="text-muted">No systems assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($holder->requested_date): ?>
                                            <?php echo e(is_string($holder->requested_date) ? $holder->requested_date : $holder->requested_date->format('Y-m-d')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('argiculture-user.show', $holder->id)); ?>" class="text-decoration-none me-2" title="View Details">
                                            <i class="fa-solid fa-eye text-primary fs-5"></i>
                                        </a>
                                        <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                                            Auth::guard('user')->user()->user_type_id == 2 ||
                                            Auth::guard('user')->user()->user_type_id == 14): ?>
                                        <a href="<?php echo e(route('argiculture-user.edit', $holder->id)); ?>" class="text-decoration-none me-2" title="Edit">
                                            <i class="fa-solid fa-edit text-warning fs-5"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none" title="Move to Progress" data-action="move-to-progress" data-holder-id="<?php echo e($holder->id); ?>" data-holder-name="<?php echo e($holder->household->english_name ?? 'N/A'); ?>">
                                            <i class="fa-solid fa-arrow-right text-info fs-5"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none ms-2" title="Revert to Requested" data-action="revert-to-requested" data-holder-id="<?php echo e($holder->id); ?>" data-holder-name="<?php echo e($holder->household->english_name ?? 'N/A'); ?>">
                                            <i class="fa-solid fa-arrow-rotate-left text-secondary fs-5"></i>
                                        </a>
                                        <a href="#" class="text-decoration-none ms-2" title="Delete" data-action="delete-holder" data-holder-id="<?php echo e($holder->id); ?>" data-holder-name="<?php echo e($holder->household->english_name ?? 'N/A'); ?>">
                                            <i class="fas fa-trash text-danger fs-5"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                        No confirmed holders found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- All In Progress Tab -->
                    <div class="tab-pane fade" id="progress-holders" role="tabpanel" aria-labelledby="progress-tab">
                        <table id="progressHoldersTable" class="table table-striped my-2">
                            <thead>
                                <tr>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Agriculture System</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $progressHolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($holder->household->english_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($holder->community->english_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($holder->agricultureSystems->count() > 0): ?>
                                            <div class="fw-medium text-warning">
                                                <?php echo e($holder->agricultureSystems->pluck('name')->filter()->join(', ') ?: 'Unknown Systems'); ?>

                                            </div>
                                            <small class="text-muted"><?php echo e($holder->size_of_herds); ?> sheep • <?php echo e($holder->azolla_unit); ?> units</small>
                                        <?php else: ?>
                                            <span class="text-muted">No systems assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('argiculture-user.show', $holder->id)); ?>" class="text-decoration-none me-2" title="View Details">
                                            <i class="fa-solid fa-eye text-primary fs-5"></i>
                                        </a>
                                        <!-- New icon for QR code generation -->
                                        <a href="#" class="text-decoration-none me-2" title="Generate QR" onclick="generateQRCode(<?php echo e($holder->id); ?>)">
                                            <i class="fa-solid fa-qrcode text-secondary fs-5"></i>
                                        </a>
                                        <?php if(Auth::guard('user')->user()->user_type_id == 1 || 
                                            Auth::guard('user')->user()->user_type_id == 2 ||
                                            Auth::guard('user')->user()->user_type_id == 14): ?>
                                        <a href="<?php echo e(route('argiculture-user.edit', $holder->id)); ?>" class="text-decoration-none me-2" title="Edit">
                                            <i class="fa-solid fa-edit text-warning fs-5"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-success" title="Mark Complete" data-action="mark-complete" data-holder-id="<?php echo e($holder->id); ?>" data-holder-name="<?php echo e($holder->household->english_name ?? 'N/A'); ?>">
                                            <i class="fa-solid fa-check-double"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="fas fa-spinner fa-2x mb-2 d-block"></i>
                                        No holders in progress found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- All Completed Tab -->
                    <div class="tab-pane fade" id="completed-holders" role="tabpanel" aria-labelledby="completed-tab">
                        <table id="completedHoldersTable" class="table table-striped my-2">
                            <thead>
                                <tr>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Community</th>
                                    <th class="text-center">Agriculture System</th>
                                    <th class="text-center">Completion Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $completedHolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($holder->household->english_name ?? 'N/A'); ?></td>
                                    <td><?php echo e($holder->community->english_name ?? 'N/A'); ?></td>
                                    <td>
                                        <?php if($holder->agricultureSystems->count() > 0): ?>
                                            <div class="fw-medium text-success">
                                                <?php echo e($holder->agricultureSystems->pluck('name')->filter()->join(', ') ?: 'Unknown Systems'); ?>

                                            </div>
                                            <small class="text-muted"><?php echo e($holder->size_of_herds); ?> sheep • <?php echo e($holder->azolla_unit); ?> units</small>
                                        <?php else: ?>
                                            <span class="text-muted">No systems assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($holder->completed_date): ?>
                                            <?php echo e(is_string($holder->completed_date) ? $holder->completed_date : $holder->completed_date->format('Y-m-d')); ?>

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('argiculture-user.show', $holder->id)); ?>" class="text-decoration-none me-2" title="View Details">
                                            <i class="fa-solid fa-eye text-primary fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="fas fa-check-double fa-2x mb-2 d-block"></i>
                                        No completed holders found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    /**
     * Get CSRF token from meta tag
     */
    function getCSRFToken() {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        return metaToken ? metaToken.getAttribute('content') : '';
    }

    /**
     * Make AJAX request with CSRF token
     */
    function makeAjaxRequest(url, method = 'POST', onSuccess = null, onError = null) {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', getCSRFToken());
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (xhr.status === 200) {
                        if (onSuccess) onSuccess(response);
                    } else {
                        if (onError) onError(response);
                    }
                } catch (e) {
                    if (onError) onError({ message: 'Invalid response format' });
                }
            }
        };

        xhr.send();
    }

    /**
     * Approve holder - change status to confirmed (status_id = 2)
     */
    function approveHolder(holderId) {
        Swal.fire({
            title: 'Confirm Approval',
            text: 'Are you sure you want to approve this agriculture holder?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                makeAjaxRequest(
                    '/argiculture-user/approve/' + holderId,
                    'POST',
                    function(response) {
                        if (response.success) {
                            Swal.fire('Approved', response.message || 'Agriculture holder approved.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message || 'Approval failed.', 'error');
                        }
                    },
                    function(error) {
                        const message = error.message || 'An error occurred while approving the holder.';
                        Swal.fire('Error', message, 'error');
                    }
                );
            }
        });
    }

    /**
     * Reject holder - change status to rejected
     */
    function rejectHolder(holderId) {
        Swal.fire({
            title: 'Confirm Rejection',
            text: 'Are you sure you want to reject this agriculture holder?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                makeAjaxRequest(
                    '/argiculture-user/reject/' + holderId,
                    'POST',
                    function(response) {
                        if (response.success) {
                            Swal.fire('Rejected', response.message || 'Agriculture holder rejected.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message || 'Rejection failed.', 'error');
                        }
                    },
                    function(error) {
                        const message = error.message || 'An error occurred while rejecting the holder.';
                        Swal.fire('Error', message, 'error');
                    }
                );
            }
        });
    }

    /**
     * Move holder to progress - change status to in progress
     */
    function moveToProgress(holderId) {
        Swal.fire({
            title: 'Move to Progress',
            text: 'Are you sure you want to move this holder to progress?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                makeAjaxRequest(
                    '/argiculture-user/move-to-progress/' + holderId,
                    'POST',
                    function(response) {
                        if (response.success) {
                            Swal.fire('Moved', response.message || 'Holder moved to progress.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message || 'Move failed.', 'error');
                        }
                    },
                    function(error) {
                        const message = error.message || 'An error occurred while moving the holder to progress.';
                        Swal.fire('Error', message, 'error');
                    }
                );
            }
        });
    }

    /**
     * Mark holder as complete - change status to completed
     */
    function markComplete(holderId) {
        Swal.fire({
            title: 'Mark as Complete',
            text: 'Are you sure you want to mark this holder as complete?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-danger'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                makeAjaxRequest(
                    '/argiculture-user/mark-complete/' + holderId,
                    'POST',
                    function(response) {
                        if (response.success) {
                            Swal.fire('Completed', response.message || 'Holder marked as completed.', 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message || 'Operation failed.', 'error');
                        }
                    },
                    function(error) {
                        const message = error.message || 'An error occurred while marking the holder as complete.';
                        Swal.fire('Error', message, 'error');
                    }
                );
            }
        });
    }

    /**
     * Delete (archive) a holder
     */
    function deleteHolder(holderId, holderName = null) {
        const displayName = holderName || 'this holder';
        Swal.fire({
            title: 'Delete Holder',
            text: `Are you sure you want to delete ${displayName}? This action archives the record.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-danger me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                makeAjaxRequest(
                    '/argiculture-user/' + holderId,
                    'DELETE',
                    function(response) {
                        if (response.success) {
                            const okMsg = response.message || `${displayName} deleted.`;
                            Swal.fire('Deleted', okMsg, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message || 'Delete failed.', 'error');
                        }
                    },
                    function(error) {
                        const message = error.message || 'An error occurred while deleting the holder.';
                        Swal.fire('Error', message, 'error');
                    }
                );
            }
        });
    }

    /**
     * Revert a confirmed holder back to Requested
     */
    function revertToRequested(holderId, holderName = null) {
        const displayName = holderName || 'this holder';
        Swal.fire({
            title: 'Revert to Requested',
            text: `Move ${displayName} back to Requested?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, revert',
            cancelButtonText: 'No',
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                makeAjaxRequest(
                    '/argiculture-user/revert-to-requested/' + holderId,
                    'POST',
                    function(response) {
                        if (response.success) {
                            Swal.fire('Reverted', response.message || `${displayName} moved back to Requested.`, 'success')
                                .then(() => location.reload());
                        } else {
                            Swal.fire('Error', response.message || 'Operation failed.', 'error');
                        }
                    },
                    function(error) {
                        const message = error.message || 'An error occurred while reverting the holder.';
                        Swal.fire('Error', message, 'error');
                    }
                );
            }
        });
    }
    </script>
    <!-- New Scripts  -->
    <script>
    function generateQRCode(holderId) {
        // Call JSON endpoint to check/generate QR and then show preview / download via Swal
        const endpoint = '/argiculture-user/qrcode-json/' + holderId;

        fetch(endpoint, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCSRFToken()
            },
            credentials: 'same-origin'
        })
        .then(async (response) => {
            if (!response.ok) {
                const text = await response.text().catch(() => null);
                console.error('QR JSON request failed', response.status, text);
                Swal.fire('Error', text || 'Unable to retrieve QR code (server error).', 'error');
                return null;
            }
            try {
                return await response.json();
            } catch (e) {
                const text = await response.text().catch(() => null);
                console.error('QR JSON parse error', e, text);
                Swal.fire('Error', 'Invalid server response when requesting QR code.', 'error');
                return null;
            }
        })
        .then(data => {
            if (!data) return;
            if (!data.success) {
                console.error('QR JSON returned success=false', data);
                Swal.fire('Error', data.message || 'Unable to retrieve QR code.', 'error');
                return;
            }

            // Prefer inline data_url for preview (works without storage symlink), then jpeg_url, then public url
            const imageDataUrl = data.data_url || data.jpeg_url || data.url;
            const isJpeg = !!data.jpeg_url || (data.data_url && data.data_url.startsWith('data:image/jpeg'));
            let title = data.exists ? 'QR Code (Existing)' : 'QR Code (Generated)';

            const html = `
                <div class="text-center">
                    <img src="${imageDataUrl}" alt="QR Code" style="max-width:260px; width:100%; height:auto; background:#fff;" />
                    <p class="mt-2 text-muted">Comet ID: <strong>${data.comet_id || holderId}</strong></p>
                </div>
            `;

            Swal.fire({
                title: title,
                html: html,
                showCancelButton: true,
                confirmButtonText: 'Download JPEG',
                cancelButtonText: 'Close',
                customClass: {
                    confirmButton: 'btn btn-primary me-2',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Prepare safe filename using holder name when available
                    const rawName = (data.holder_name || holderId).toString();
                    const safeName = rawName.replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_\-]/g, '');
                    const a = document.createElement('a');
                    // Prefer downloadable JPEG URL, then inline data URL, then public URL
                    a.href = data.jpeg_url || data.data_url || data.url;
                    const suggestedFilename = isJpeg ? `${safeName}_qrcode.jpg` : `${safeName}_qrcode.svg`;
                    a.setAttribute('download', suggestedFilename);
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                }
            });
        })
        .catch(err => {
            Swal.fire('Error', 'An error occurred while requesting the QR code.', 'error');
            console.error(err);
        });
    }

    document.getElementById('exportBtn').addEventListener('click', function () {
        // Read selected options and pass as query parameters
        const cycleYearEl = document.getElementById('cycleYearSelect');
        const exportTypeEl = document.getElementById('exportTypeSelect');
        const cycleYear = cycleYearEl ? cycleYearEl.value : '';
        const exportType = exportTypeEl ? exportTypeEl.value : '';

        const params = new URLSearchParams();
        if (cycleYear) params.append('cycle_year', cycleYear);
        if (exportType) params.append('export_type', exportType);

        const url = "<?php echo e(route('argiculture-user.export')); ?>" + (params.toString() ? ('?' + params.toString()) : '');
        window.location.href = url;
    });

    document.getElementById('downloadAllQrBtn').addEventListener('click', function () {
        // Navigate to the download route to trigger the ZIP download
        window.location.href = "<?php echo e(route('argiculture-user.download-all-qrcodes')); ?>";
    });
    </script>

    <script>
    // Delegated handler for elements using data-action to avoid inline JS injection
    document.addEventListener('click', function (e) {
        const el = e.target.closest('[data-action]');
        if (!el) return;
        e.preventDefault();
        const action = el.getAttribute('data-action');
        const id = el.getAttribute('data-holder-id');
        const name = el.getAttribute('data-holder-name');

        switch (action) {
            case 'move-to-progress':
                moveToProgress(id);
                break;
            case 'revert-to-requested':
                revertToRequested(id, name);
                break;
            case 'delete-holder':
                deleteHolder(id, name);
                break;
            case 'mark-complete':
                markComplete(id, name);
                break;
        }
    });
    </script>

    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/agriculture/user/index.blade.php ENDPATH**/ ?>