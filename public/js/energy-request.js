// JS for FBS shared households meter number display 
(function(){
    window.householdsCacheFbs = window.householdsCacheFbs || {};

    function fetchHouseholdsIfNeededFbs(communityId, cb) {
        if (!communityId) return cb(null);
        if (window.householdsCacheFbs[communityId]) return cb(window.householdsCacheFbs[communityId]);
        $.ajax({
            url: '/household/get_by_community/' + communityId,
            method: 'GET',
            success: function (data) {
                window.householdsCacheFbs[communityId] = data.html;
                cb(data.html);
            },
            error: function () { cb(null); }
        });
    }

    // expose helper so other scripts (blade in-page scripts) can call it
    window.fetchHouseholdsIfNeededFbs = fetchHouseholdsIfNeededFbs;

    function updateFbsSharedControlsState() {
        var mainSelected = $('#selectedEnergyUserFbs').val();
        if ($('#isMeterSharedFbs').is(':checked')) {
            $('.shared-meter-row-fbs').show();
            // enable count when the shared checkbox is checked; generation will validate community/options
            $('#sharedUsersCountFbs').prop('disabled', false);
        } else {
            $('.shared-meter-row-fbs').hide();
            $('#sharedUsersContainerFbs').empty();
            $('#sharedUsersCountFbs').val('').prop('disabled', true);
        }
    }
    $(document).on('change', '#isMeterSharedFbs', updateFbsSharedControlsState);
    $(document).on('change', '#selectedEnergyUserFbs', updateFbsSharedControlsState);

    $(document).on('click', '.reset-shared-user-fbs', function(e) {
        e.preventDefault();
        var idx = $(this).data('idx');
        var select = $('.shared-user-select-fbs[data-shared-idx="' + idx + '"]');
        select.val('');
        if (select.hasClass('selectpicker')) select.selectpicker('refresh');
        select.trigger('change');
    });

    $(document).on('input', '#sharedUsersCountFbs', function () {
        var cnt = parseInt($(this).val() || 0);
        if (!$('#isMeterSharedFbs').is(':checked')) return;
        if (cnt > 0) generateSharedSelectsFbs(cnt);
        else $('#sharedUsersContainerFbs').empty();
    });

    // keep a small cache of removed selections so they can be restored if user increases count again
    window._sharedFbsSaved = window._sharedFbsSaved || [];

    window.generateSharedSelectsFbs = function(count, forceRefresh) {
        var communityId = $('#selectedCommunityFbs').val();
        var container = $('#sharedUsersContainerFbs');
        var currentSelects = container.find('select.shared-user-select-fbs');
        if (!communityId) {
            container.html('<div class="text-danger">Please choose a community first.</div>');
            return;
        }
        if (!count || isNaN(count) || count < 1) return;

        // capture previous state
        var prevSelections = [];
        var prevMeters = {};
        currentSelects.each(function(idx) {
            prevSelections.push($(this).val());
            var meterDiv = $('#meter-number-placeholder-' + idx);
            prevMeters[idx] = meterDiv.html();
        });

        var currentCount = currentSelects.length;

        // If increasing, append new selects (preserve existing). If decreasing, remove extras but cache their values.
        if (count > currentCount) {
            for (var i = currentCount; i < count; i++) {
                var optionsStr = '<option disabled selected>Loading...</option>';
                var selectHtml = '<select name="shared_users_fbs[]" class="selectpicker form-control shared-user-select-fbs mb-2" data-live-search="true" data-size="5" data-shared-idx="'+i+'">' + optionsStr + '</select>';
                var meterHtml = '<div class="meter-number-placeholder" id="meter-number-placeholder-'+i+'"></div>';
                container.append('<div class="shared-user-entry">' + selectHtml + meterHtml + '<div class="shared-user-info" id="shared-user-info-'+i+'" style="display:none"></div></div>');
            }
        } else if (count < currentCount) {
            // cache removed values
            for (var r = currentCount - 1; r >= count; r--) {
                var sel = $(currentSelects[r]);
                var val = sel.val();
                if (val) window._sharedFbsSaved.push(val);
                // remove the DOM element
                sel.closest('.shared-user-entry').remove();
            }
        } else {
            // equal - nothing to change structurally
        }

        // bind change handler for selects (will work after options populate)
        container.find('.shared-user-select-fbs').off('change.info').on('change.info', function(){
            var idx = $(this).data('shared-idx');
            var householdId = $(this).val();
            var meterDiv = $('#meter-number-placeholder-'+idx);
            meterDiv.empty();
            if (!householdId) return;

            $.ajax({
                url: '/energy_user/get_by_household/' + householdId,
                method: 'GET',
                success: function(data) {
                    if (data.comet_id === undefined) {
                        console.log('Shared user comet id: No comet id found ');
                    }
                    var meterNum = data.meter_number;
                    if (meterNum && meterNum !== '' && meterNum !== 'No') {
                        meterDiv.html('<div class="mt-2 small text-danger" style="background:#fff;padding:6px 12px;border-radius:6px;border:1px solid #e0e0e0;display:block;">Household has a meter before ' + meterNum + ' Would you like to install it for a different user? <a href="javascript:void(0)" class="font-weight-bold reset-shared-user-fbs" data-idx="' + idx + '">YES</a></div>');
                    } else if (data.main_holder) {
                        meterDiv.html('<div class="mt-2 small text-warning" style="background:#fff;padding:6px 12px;border-radius:6px;border:1px solid #e0e0e0;display:block;">This user shared with <b>' + data.main_holder + '</b> before.</div>');
                    } else if (data.is_requested) {
                        var referredBy = data.referred_by ? data.referred_by : 'Unknown';
                        meterDiv.html('<div class="mt-2 small text-info" style="background:#fff;padding:6px 12px;border-radius:6px;border:1px solid #e0e0e0;display:block;">This user is requested and referred by <b>' + referredBy + '</b></div>');
                    } else {
                        var display = (meterNum && meterNum !== '') ? meterNum : 'None';
                        meterDiv.html('<div class="mt-2 small text-primary" style="background:#fff;padding:6px 12px;border-radius:6px;border:1px solid #e0e0e0;display:block;">Shared household meter number: <b>' + display + '</b></div>');
                    }
                },
                error: function(){
                    meterDiv.html('<span class="text-danger">Could not fetch household info.</span>');
                }
            });
        });

        // now fetch household options and populate the selects (filtering out main holder)
        var mainId = $('#selectedEnergyUserFbs').val();
        var mainText = $('#selectedEnergyUserFbs option:selected').text().trim();
        if (mainId !== undefined && mainId !== null) mainId = String(mainId);

        fetchHouseholdsIfNeededFbs(communityId, function (optionsHtml) {
            if (!optionsHtml) {
                container.html('<div class="text-danger">Failed to load households for that community.</div>');
                $('#sharedUsersCountFbs').val('').prop('disabled', true);
                return;
            }

            var tmp = $('<select></select>'); tmp.html(optionsHtml);
            var filteredOptions = [];
            tmp.find('option').each(function () {
                var optVal = $(this).attr('value');
                var optText = $(this).text().trim();
                if (optVal !== undefined && optVal !== null) optVal = String(optVal);
                if (!optVal || optVal === '') return;
                if (mainId && optVal == mainId) return;
                if (mainText && optText == mainText) return;
                filteredOptions.push('<option value="'+optVal+'">'+optText+'</option>');
            });

            if (filteredOptions.length === 0) {
                container.html('<div class="text-danger">No other households available in this community.</div>');
                $('#sharedUsersCountFbs').val('').prop('disabled', true);
                if (typeof $('.selectpicker').selectpicker === 'function') $('.selectpicker').selectpicker('refresh');
                return;
            }

            // populate options for each select, but preserve existing values where possible
            container.find('select.shared-user-select-fbs').each(function(idx){
                var $sel = $(this);

                // If this select existed before the function call, don't replace its options
                // (this preserves user selections when only the count changed).
                if (idx < currentCount && !forceRefresh) {
                    // try to re-set previous value if missing
                    if (prevSelections[idx]) {
                        if ($sel.find('option[value="' + prevSelections[idx] + '"]').length) {
                            $sel.val(prevSelections[idx]);
                        }
                    }
                    return;
                }

                var optionsStr = '<option disabled selected>Choose One...</option>' + filteredOptions.join('');
                var existingVal = $sel.val();

                $sel.html(optionsStr);

                // if it had a previous selection (from before function call), restore it
                if (prevSelections[idx]) {
                    if ($sel.find('option[value="' + prevSelections[idx] + '"]').length) {
                        $sel.val(prevSelections[idx]);
                    } else {
                        // if option not present (filtered out), append it so name will display; try to find text from optionsHtml
                        var optText = null;
                        var tmp = $('<select></select>'); tmp.html(optionsHtml);
                        var found = tmp.find('option[value="' + prevSelections[idx] + '"]');
                        if (found.length) optText = found.text().trim();
                        if (!optText) optText = prevSelections[idx];
                        $sel.append('<option value="' + prevSelections[idx] + '">' + optText + '</option>');
                        $sel.val(prevSelections[idx]);
                    }
                } else if (existingVal) {
                    // if element existed and had a value, try to keep it
                    if ($sel.find('option[value="' + existingVal + '"]').length) {
                        $sel.val(existingVal);
                    }
                } else if (window._sharedFbsSaved && window._sharedFbsSaved.length) {
                    // if we have cached removed selections, restore one into this new select
                    var restored = window._sharedFbsSaved.pop();
                    if (restored) {
                        // append restored option if missing
                        if ($sel.find('option[value="' + restored + '"]').length === 0) {
                            var tmp2 = $('<select></select>'); tmp2.html(optionsHtml);
                            var found2 = tmp2.find('option[value="' + restored + '"]');
                            var label2 = found2.length ? found2.text().trim() : restored;
                            $sel.append('<option value="' + restored + '">' + label2 + '</option>');
                        }
                        $sel.val(restored);
                    }
                }
            });

            if (typeof $('.selectpicker').selectpicker === 'function') $('.selectpicker').selectpicker('refresh');
        });
    }

    $(document).on('change', '#selectedCommunityFbs, #selectedEnergyUserFbs', function () {
        if ($('.shared-meter-row-fbs').is(':visible')) {
            var cnt = parseInt($('#sharedUsersCountFbs').val() || 0);
            if (cnt > 0) generateSharedSelectsFbs(cnt, true);
        }
    });
})();
