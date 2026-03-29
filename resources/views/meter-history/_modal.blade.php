<div class="meter-info-modal">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <h4>Meter Information</h4>
        <div>
            <button class="btn btn-sm btn-outline-primary" id="exportMeterHistoryCsv">+ Export CSV</button>
        </div>
    </div>

    <p class="text-muted">View detailed information about this meter including current holder, status, and history.</p>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="mb-2"><strong>Meter Number:</strong><br>
                <a href="#">{{ $meter->meter_number ?? ($histories->first()->old_meter_number ?? '') }}</a>
            </div>

            <div class="mb-2"><strong>Community:</strong><br>
                {{ $communityName ?? ($histories->first()->community->english_name ?? '') }}
            </div>

            <div class="mb-2"><strong>Status of Meter:</strong><br>
                <span class="badge bg-light text-primary">{{ $meter->MeterCase->meter_case_name_english ?? ($latest->status->english_name ?? '') }}</span>
            </div>

            <div class="mb-2"><strong>Last Updated:</strong><br>
                {{ $latest->date ?? $latest->created_at ?? '' }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-2"><strong>Current Holder:</strong><br>
                {{ $currentHolder ?? ($latest->household->english_name ?? '') }}
            </div> 

            <div class="mb-2"><strong>Status:</strong><br>
                <span class="badge bg-success">{{ $latest->status->english_name ?? '' }}</span>
            </div>

            <div class="mb-2"><strong>Reason:</strong><br>
                <span class="badge bg-warning text-dark">{{ $latest->reason->english_name ?? '' }}</span>
            </div>
        </div>
    </div>

    <div class="mt-4 text-center">
        <a href="{{ route('meter-history.show', $meter->meter_number ?? ($histories->first()->old_meter_number ?? '')) }}" class="btn btn-dark" target="_blank">View Full History</a>
    </div>
</div>
