

<?php $__env->startSection('title', 'All Meter Histories'); ?>

<?php echo $__env->make('layouts.all', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('page-style'); ?>
<style>
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0 !important;
        border: none !important;
        border-radius: 4px;
        background: transparent;
        box-shadow: none !important;
        outline: none !important;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .action-icon:focus,
    .action-icon:active {
        outline: none !important;
        box-shadow: none !important;
        border: none !important;
    }

    .action-icon i {
        font-size: 14px;
        line-height: 1;
    }

    .view-icon {
        color: #1e90ff !important;
    }

    .edit-icon {
        color: #28a745 !important;
    }

    .delete-icon {
        color: #dc3545 !important;
    }

    .meter-number-link {
        cursor: pointer;
        text-decoration: underline;
    }

    /* Pagination tweaks */
    .meter-pagination-wrap .pagination {
        margin: 0;
    }
    .meter-pagination-wrap .pagination .page-link {
        border-radius: 6px;
        min-width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 10px;
    }
    .meter-pagination-wrap .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    @media (max-width: 768px) {
        .action-icon {
            width: 28px;
            height: 28px;
        }

        .action-icon i {
            font-size: 12px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $householdsNotFoundCount = $meterHistories->filter(function ($history) {
            return !$history->main_holder_name && !$history->is_public_structure;
        })->count();
    ?>

    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Meter History / </span>All
    </h4>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>

        <div class="d-flex gap-2">
            <a href="<?php echo e(route('meter-history.import.form')); ?>" class="btn btn-success">
                <i class="fas fa-upload me-2"></i>Import Meter History
            </a>

            <a href="<?php echo e(asset('METER_HISTORY_SAMPLE_TEMPLATE.md')); ?>" class="btn btn-info" download>
                <i class="fas fa-download me-2"></i>Download Sample Template
            </a>

            <a href="<?php echo e(route('meter-history.new')); ?>" class="btn btn-primary">
                <i class="bx bx-plus me-2"></i>Create New Meter History
            </a>
        </div>
    </div>

    <div class="container">
        <div class="card my-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="py-3 breadcrumb-wrapper mb-0">
                        <span class="text-muted fw-light">All </span>Imported Meter Histories
                    </h5>

                    <div class="alert alert-warning d-flex align-items-center mb-0 px-3 py-2" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>
                            <strong><?php echo e($householdsNotFoundCount); ?></strong>
                            records show "Household Not Found" on this page
                        </span>
                    </div>
                </div>

                <!-- Search & Filter Section -->
                <div class="global-search-section mb-3">
                    <div class="search-header mb-2 d-flex justify-content-between align-items-center gap-2">
                        <h5 class="mb-0"><i class="bx bx-search me-2 text-primary"></i>Search Meter Histories</h5>
                        <button type="button"
                                class="btn btn-outline-secondary btn-sm"
                                title="Clear all filters"
                                onclick="window.location.href='<?php echo e(route('meter-history.all')); ?>'">
                            <i class="bx bx-reset me-1"></i>Clear Filters
                        </button>
                    </div>
                    <div class="search-content">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search by Meter Number</label>
                                <form id="meterHistorySearchForm" method="GET" action="<?php echo e(route('meter-history.all')); ?>">
                                    <input type="hidden" name="community_id" value="<?php echo e($filters['community_id'] ?? ''); ?>">
                                    <input type="hidden" name="status_id" value="<?php echo e($filters['status_id'] ?? ''); ?>">
                                    <input type="hidden" name="reason_id" value="<?php echo e($filters['reason_id'] ?? ''); ?>">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" id="globalMeterSearch" name="meter_number" class="form-control" placeholder="Enter meter number..." value="<?php echo e($filters['meter_number'] ?? ''); ?>" title="Press Enter or click Search to find meter history">
                                        <button class="btn btn-outline-primary" id="searchMeterBtn" title="Search for meter history" type="submit">
                                            <i class="bx bx-search me-1"></i>Search
                                        </button>
                                    </div>
                                </form>
                                <small class="form-text text-muted"><i class="bx bx-info-circle me-1"></i>Enter a meter number and press Enter or click Search</small>
                            </div>

                            <!-- Filters: Community | Meter Status | Reason -->
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Community</label>
                                        <select id="globalCommunityFilter" class="selectpicker form-control" data-live-search="true" onchange="(function(){var f=document.getElementById('meterHistorySearchForm');f.querySelector('input[name=community_id]').value=this.value;f.submit();}).call(this);">
                                            <option value="">All Communities</option>
                                            <?php $__currentLoopData = \App\Models\Community::orderBy('english_name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $community): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($community->id); ?>" <?php echo e((isset($filters['community_id']) && $filters['community_id'] == $community->id) ? 'selected' : ''); ?>>
                                                    <?php echo e($community->english_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Meter Status</label>
                                        <select id="globalStatusFilter" class="selectpicker form-control" data-live-search="true" onchange="(function(){var f=document.getElementById('meterHistorySearchForm');f.querySelector('input[name=status_id]').value=this.value;f.submit();}).call(this);">
                                            <option value="">All Statuses</option>
                                            <?php $__currentLoopData = \App\Models\MeterHistoryStatuses::orderBy('english_name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($status->id); ?>" <?php echo e((isset($filters['status_id']) && $filters['status_id'] == $status->id) ? 'selected' : ''); ?>>
                                                    <?php echo e($status->english_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Reason</label>
                                        <select id="globalReasonFilter" class="selectpicker form-control" data-live-search="true" onchange="(function(){var f=document.getElementById('meterHistorySearchForm');f.querySelector('input[name=reason_id]').value=this.value;f.submit();}).call(this);">
                                            <option value="">All Reasons</option>
                                            <?php $__currentLoopData = \App\Models\MeterHistoryReason::orderBy('english_name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($reason->id); ?>" <?php echo e((isset($filters['reason_id']) && $filters['reason_id'] == $reason->id) ? 'selected' : ''); ?>>
                                                    <?php echo e($reason->english_name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Meter Number</th>
                                <th>Meter Status</th>
                                <th>Reason</th>
                                <th>Main Holder</th>
                                <th>Changed Date</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $meterHistories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $meterNumber = $history->old_meter_number ?? ($history->mainEnergyMeter->meter_number ?? '');
                                    $statusName = $history->status->english_name ?? $history->status->arabic_name ?? '';
                                    $reasonName = $history->reason->english_name ?? $history->reason->arabic_name ?? '';
                                    $holderName = $history->main_holder_name ?? ($history->is_public_structure ? 'Public Structure' : 'Household Not Found');
                                ?>

                                <tr>
                                    <td>
                                        <a href="#"
                                           class="meter-number-link text-primary js-meter-history-link"
                                           data-meter-number="<?php echo e($meterNumber); ?>">
                                            <?php echo e($meterNumber); ?>

                                        </a>
                                    </td>

                                    <td><?php echo e($statusName); ?></td>
                                    <td><?php echo e($reasonName); ?></td>

                                    <td>
                                        <?php echo e($holderName); ?>

                                        <?php if($history->is_public_structure): ?>
                                            <small class="text-muted">(Public Structure)</small>
                                        <?php endif; ?>
                                    </td>

                                    <td><?php echo e($history->date); ?></td>

                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="<?php echo e(route('meter-history.show', $meterNumber ?: $history->id)); ?>"
                                               class="action-icon view-icon"
                                               title="View Details"
                                               aria-label="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <button type="button"
                                                    class="action-icon edit-icon js-edit-history-btn"
                                                    data-edit-url="<?php echo e(url('meter-history/' . $history->id . '/edit')); ?>"
                                                    title="Edit"
                                                    aria-label="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button type="button"
                                                    class="action-icon delete-icon js-delete-history-btn"
                                                    data-history-id="<?php echo e($history->id); ?>"
                                                    title="Delete"
                                                    aria-label="Delete"
                                                    onclick="return typeof confirmDeleteHistory === 'function' ? confirmDeleteHistory(<?php echo e($history->id); ?>, this) : (function(){ alert('Delete function not available'); return false; })()">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No meter history records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <small class="text-muted">
                            <?php if($meterHistories->total()): ?>
                                Showing <?php echo e($meterHistories->firstItem()); ?> to <?php echo e($meterHistories->lastItem()); ?> of <?php echo e($meterHistories->total()); ?> records
                            <?php else: ?>
                                No records
                            <?php endif; ?>
                        </small>
                    </div>

                    <div class="meter-pagination-wrap">
                        <nav aria-label="Meter history pagination">
                            <?php echo e($meterHistories->onEachSide(1)->links('pagination::bootstrap-5')); ?>

                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('components.meter-history-complete', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    console.log('Meter history all.blade page-script loaded');
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = '<?php echo e(csrf_token()); ?>';

    function openHistoryModal(meterNumber) {
        if (!meterNumber) return;

        if (typeof openMeterHistory === 'function') {
            openMeterHistory(meterNumber);
        } else {
            console.warn('openMeterHistory function is not defined');
        }
    }

    function redirectToEdit(editUrl) {
        if (!editUrl) return;
        window.location.href = editUrl;
    }

    document.addEventListener('click', function (e) {
        const meterLink = e.target.closest('.js-meter-history-link');
        if (meterLink) {
            e.preventDefault();
            openHistoryModal(meterLink.getAttribute('data-meter-number'));
            return;
        }

        const editBtn = e.target.closest('.js-edit-history-btn');
        if (editBtn) {
            e.preventDefault();
            const editUrl = editBtn.getAttribute('data-edit-url');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Edit this history record?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, edit',
                    cancelButtonText: 'Cancel'
                }).then(function (result) {
                    if (result.isConfirmed) {
                        redirectToEdit(editUrl);
                    }
                });
            } else {
                if (confirm('Edit this history record?')) {
                    redirectToEdit(editUrl);
                }
            }

            return;
        }

    });
});
    
    // Household filter removed — no dynamic population required.

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/meter-history/all.blade.php ENDPATH**/ ?>