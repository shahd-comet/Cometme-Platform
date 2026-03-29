<!-- Modal HTML -->
<div class="modal fade" id="meterHistoryModal" tabindex="-1" role="dialog" aria-labelledby="meterHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meterHistoryModalLabel">Meter History</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="meterHistoryModalContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading meter details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 is required for consistent alerts in this modal component -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
if (typeof window.openMeterHistory === 'undefined') {

    function showAppAlert(title, message, icon) {
        if (typeof window.Swal !== 'undefined' && window.Swal && typeof window.Swal.fire === 'function') {
            window.Swal.fire({
                title: title || 'Notice',
                text: message || '',
                icon: icon || 'info',
                confirmButtonText: 'OK'
            });
            return;
        }

        alert((title ? (title + ': ') : '') + (message || ''));
    }

    function openMeterHistory(meterNumber) {
        console.log('openMeterHistory called with:', meterNumber);

        if (!meterNumber) {
            showAppAlert('Meter history', 'Meter number not available', 'warning');
            return false;
        }

        // Wait for jQuery to be available
        if (typeof $ === 'undefined') {
            console.log('jQuery not ready, waiting...');
            setTimeout(function() { openMeterHistory(meterNumber); }, 100);
            return false;
        }

        // Check if modal exists
        if ($('#meterHistoryModal').length === 0) {
            showAppAlert('Meter history', 'Modal not found in DOM', 'error');
            return false;
        }

        // Hide immediately to avoid flicker from a previous opened modal
        $('#meterHistoryModal').modal('hide');

        console.log('Fetching meter history for:', meterNumber);

        //  AJAX request to get meter history
        $.ajax({
            url: '/meter-history/by-meter/' + meterNumber,
            type: 'GET',
            success: function(response) {
                console.log('AJAX Success:', response);
                if (response && response.empty) {
                    $('#meterHistoryModal').modal('hide');
                    showAppAlert('No History', 'No History yet', 'info');
                } else {
                    // Set modal title and content only when we have data.
                    $('#meterHistoryModalLabel').text('Meter History - ' + meterNumber);
                    $('#meterHistoryModalContent').html(response);
                    $('#meterHistoryModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading meter history:', error);
                $('#meterHistoryModal').modal('hide');
                $('#meterHistoryModalContent').html('<div class="alert alert-danger">Failed to load meter history. Please try again.</div>');
                showAppAlert('Error', 'Failed to load meter history. Please try again.', 'error');
            }
        });

        return false;
    }

    // Make it globally available
    window.openMeterHistory = openMeterHistory;

    // jQuery event handlers for different link types
    $(document).ready(function() {
        console.log('Meter history component loaded with inline script');

        // Handler for meter number links with class 'meter-number-link'
        $(document).on('click', '.meter-number-link', function(e) {
            e.preventDefault();
            var meterNumber = $(this).data('meter-number');
            if (meterNumber) {
                openMeterHistory(meterNumber);
            }
            return false;
        });

        // Handler for meter number links with class 'show-meter-history' 
        $(document).on('click', '.show-meter-history', function(e) {
            e.preventDefault();
            var meterNumber = $(this).data('meter');
            if (meterNumber) {
                openMeterHistory(meterNumber);
            }
            return false;
        });
    });

    console.log('openMeterHistory function defined via component');
}

function confirmDeleteHistory(historyId, btn) {
    try {
        console.log('confirmDeleteHistory called for id:', historyId);
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

        var doDelete = function() {
            var old = null;
            if (btn) {
                btn.disabled = true;
                old = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }

            fetch('/meter-history/' + historyId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success) {
                    console.log('Deleted:', data);
                    if (typeof Swal !== 'undefined') Swal.fire('Deleted', data.message || 'Record deleted', 'success');
                    if (btn) {
                        var row = btn.closest('tr');
                        if (row) row.remove();
                    }
                } else {
                    console.log('Delete failed', data);
                    if (typeof Swal !== 'undefined') Swal.fire('Error', data.message || 'Failed to delete', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = old;
                    }
                }
            }).catch(function(err){
                console.error('Delete error', err);
                if (typeof Swal !== 'undefined') Swal.fire('Error', 'An error occurred while deleting the record', 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = old;
                }
            });
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) doDelete();
            });
        } else {
            if (confirm('Are you sure you want to delete this history record?')) doDelete();
        }

        return false;
    } catch (e) {
        console.error('confirmDeleteHistory thrown', e);
        return false;
    }
}

</script>