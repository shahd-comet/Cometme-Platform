/**
 * Meter History Modal Handler
 * Reusable script for showing meter history in a modal
 */

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

    if (!window.__meterHistorySwalPromise) {
        window.__meterHistorySwalPromise = new Promise(function(resolve, reject) {
            try {
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                script.async = true;
                script.onload = function() { resolve(); };
                script.onerror = function() { reject(new Error('Failed to load SweetAlert2')); };
                document.head.appendChild(script);
            } catch (e) {
                reject(e);
            }
        });
    }

    window.__meterHistorySwalPromise
        .then(function() {
            if (typeof window.Swal !== 'undefined' && window.Swal && typeof window.Swal.fire === 'function') {
                window.Swal.fire({
                    title: title || 'Notice',
                    text: message || '',
                    icon: icon || 'info',
                    confirmButtonText: 'OK'
                });
            } else {
                alert((title ? (title + ': ') : '') + (message || ''));
            }
        })
        .catch(function() {
            alert((title ? (title + ': ') : '') + (message || ''));
        });
}

// Define the function immediately (global scope)
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

    // Always hide modal immediately to avoid showing a previous meter's modal
    // while we wait for the AJAX response.
    $('#meterHistoryModal').modal('hide');

    // Do NOT show the modal until we know there's history to display.
    // This prevents showing "Meter History - <number>" / loading UI when response is empty.
    console.log('Fetching meter history for:', meterNumber);

    // Make AJAX request to get meter history
    $.ajax({
        url: '/meter-history/by-meter/' + meterNumber,
        type: 'GET',
        success: function(response) {
            console.log('AJAX Success:', response);
            if (response && response.empty) {
                $('#meterHistoryModal').modal('hide');
                showAppAlert('No history', 'No history yet', 'info');
            } else {
                // Set modal title and content only when we have data.
                $('#meterHistoryModalLabel').text('Meter History - ' + meterNumber);
                $('#meterHistoryModalContent').html(response);
                $('#meterHistoryModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading meter history:', error);
            showAppAlert('Error', 'Failed to load meter history. Please try again.', 'error');
        }
    });
    
    return false;
}

// Make it globally available
window.openMeterHistory = openMeterHistory;

// Document ready function to attach event handlers
$(document).ready(function() {
    console.log('Meter history modal script loaded');
    
    // Handler for meter number links with class 'meter-number-link'
    $(document).on('click', '.meter-number-link', function(e) {
        console.log('meter-number-link clicked via jQuery');
        e.preventDefault();
        var meterNumber = $(this).data('meter-number');
        console.log('Meter number from data-meter-number:', meterNumber);
        openMeterHistory(meterNumber);
        return false;
    });

    // Handler for meter number links with class 'show-meter-history' 
    $(document).on('click', '.show-meter-history', function(e) {
        console.log('show-meter-history clicked via jQuery');
        e.preventDefault();
        var meterNumber = $(this).data('meter');
        console.log('Meter number from data-meter:', meterNumber);
        openMeterHistory(meterNumber);
        return false;
    });

    // Handler for any element with data-action="show-meter-history"
    $(document).on('click', '[data-action="show-meter-history"]', function(e) {
        console.log('data-action="show-meter-history" clicked via jQuery');
        e.preventDefault();
        var meterNumber = $(this).data('meter-number') || $(this).data('meter');
        console.log('Meter number from data attributes:', meterNumber);
        openMeterHistory(meterNumber);
        return false;
    });

});

console.log('Meter history script file loaded');