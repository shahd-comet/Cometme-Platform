@extends('layouts/layoutMaster')

@section('title', 'Edit Camera Addition')

@include('layouts.all')

@section('content')
<style>
    label, input, select, textarea {
        display: block;
        width: 100%;
    }

    label, table {
        margin-top: 20px;
    }

    .form-table th, .form-table td {
        vertical-align: middle;
    }
</style>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Edit Camera Addition</h5>
        </div>
        <form method="POST" action="{{ route('camera-additions.update', $addition->id) }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                <table class="table table-bordered form-table">
                    <tr>
                        <th>Camera Community</th>
                        <td>
                            <select id="camera_community_id" name="camera_community_id" class="form-select" required>
                                @foreach($cameraCommunities as $cc)
                                    <option value="{{ $cc->id }}" data-community="{{ $cc->community_id }}" @if($cc->id == $addition->camera_community_id) selected @endif>
                                        {{ $cc->community->english_name ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr id="compound_row">
                        <th>Compound</th>
                        <td>
                            <select id="compound_id_select" name="compound_id" class="form-select">
                                <option value="" disabled>Choose Compound...</option>
                                @foreach($compounds as $compound)
                                    <option value="{{$compound->id}}" data-community="{{ $compound->community_id }}" @if($addition->compound_id == $compound->id) selected @endif>{{$compound->english_name}}</option>
                                @endforeach
                            </select>
                            <span id="compound_name_display" style="display:none;"></span>
                        </td>
                    </tr>

                    <tr>
                        <th>Date of Addition</th>
                        <td>
                            <input type="date" name="date_of_addition" class="form-control"
                                   value="{{ $addition->date_of_addition }}" required>
                        </td>
                    </tr>

                    <tr>
                        <th>Number of Cameras</th>
                        <td>
                            <input type="number" name="number_of_cameras" class="form-control"
                                   value="{{ $addition->number_of_cameras }}" min="0" required>
                        </td>
                    </tr>

                    <tr>
                        <th>SD Card Number</th>
                        <td>
                            <input type="number" name="sd_card_number" class="form-control"
                                   value="{{ $addition->sd_card_number }}" placeholder="Enter SD Card Number">
                        </td>
                    </tr>

                    <tr>
                        <th>Camera Type</th>
                        <td>
                            <select name="camera_id" class="form-select" required>
                                @foreach($cameras as $camera)
                                    <option value="{{ $camera->id }}" {{ $camera->id == $addition->camera_id ? 'selected' : '' }}>
                                        {{ $camera->model }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>NVR Camera (optional)</th>
                        <td>
                            <select name="nvr_camera_id" class="form-select">
                                <option value="" {{ is_null($addition->nvr_camera_id) ? 'selected' : '' }}>None</option>
                                @foreach($nvrs as $nvr)
                                    <option value="{{ $nvr->id }}" {{ $nvr->id == $addition->nvr_camera_id ? 'selected' : '' }}>
                                        {{ $nvr->model }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Number of NVRs</th>
                        <td>
                            <input type="number" name="number_of_nvr" class="form-control"
                                   value="{{ $addition->number_of_nvr }}" min="0">
                        </td>
                    </tr>

                    <tr>
                        <th>Notes</th>
                        <td>
                            <textarea name="notes" class="form-control" rows="3">{{ $addition->notes }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>Donors (optional)</th>
                        <td>
                            <select name="donor_ids[]" class="form-select" multiple>
                                @foreach($donors as $donor)
                                    <option value="{{ $donor->id }}" 
                                        {{ $addition->donors->contains('donor_id', $donor->id) ? 'selected' : '' }}>
                                        {{ $donor->donor_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple donors</small>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('camera-additions.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const communitySelect = document.getElementById('camera_community_id');
    const compoundSelect = document.getElementById('compound_id_select');
    function filterCompounds() {
        const selectedCommunityOption = communitySelect.options[communitySelect.selectedIndex];
        const selectedCommunityId = selectedCommunityOption ? selectedCommunityOption.getAttribute('data-community') : null;
        let found = false;
        Array.from(compoundSelect.options).forEach(option => {
            if (!option.value) return;
            if (option.getAttribute('data-community') === selectedCommunityId) {
                option.style.display = '';
                if (compoundSelect.value == option.value) found = true;
            } else {
                option.style.display = 'none';
            }
        });
        if (!found) {
            compoundSelect.value = '';
        }
    }
    communitySelect.addEventListener('change', filterCompounds);
    filterCompounds();
});
</script>
@endsection
