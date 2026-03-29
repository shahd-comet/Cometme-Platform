@extends('layouts/layoutMaster')

@section('title', 'Edit Camera Replacement')

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
            <h5>Edit Camera Replacement</h5>
        </div>
        <form method="POST" action="{{ route('replacement.update', $replacement->id) }}">
            @csrf
            @method('PUT')

            <div class="card-body">
                <table class="table table-bordered form-table">
                    <tr>
                        <th>Camera Community</th>
                        <td>
                            <select id="camera_community_id" name="camera_community_id" class="form-select" required>
                                @foreach($cameraCommunities as $cc)
                                    <option value="{{ $cc->id }}" data-community="{{ $cc->community_id }}" @if($cc->id == $replacement->camera_community_id) selected @endif>
                                        {{ $cc->community->english_name ?? 'Unknown' }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Compound</th>
                        <td>
                            <select id="compound_id_select" name="compound_id" class="form-select">
                                <option value="">Choose Compound...</option>
                                @foreach($compounds as $compound)
                                    <option value="{{$compound->id}}" data-community="{{ $compound->community_id }}" @if($replacement->compound_id == $compound->id) selected @endif>{{$compound->english_name}}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Date of Replacement</th>
                        <td>
                            <input type="date" name="date_of_replacement" class="form-control"
                                   value="{{ $replacement->date_of_replacement }}" required>
                        </td>
                    </tr>

                    <tr>
                        <th>Number of Damaged Cameras</th>
                        <td>
                            <input type="number" name="damaged_camera_count" class="form-control"
                                   value="{{ $replacement->damaged_camera_count }}" min="0" required>
                        </td>
                    </tr>

                    <tr>
                        <th>Number of New Cameras</th>
                        <td>
                            <input type="number" name="new_camera_count" class="form-control"
                                   value="{{ $replacement->new_camera_count }}" min="0" required>
                        </td>
                    </tr>

                    <tr>
                        <th>Number of Damaged SD Cards</th>
                        <td>
                            <input type="number" name="damaged_sd_card_count" class="form-control"
                                   value="{{ $replacement->damaged_sd_card_count }}" min="0" placeholder="Enter number of damaged SD cards">
                        </td>
                    </tr>

                    <tr>
                        <th>Number of New SD Cards</th>
                        <td>
                            <input type="number" name="new_sd_card_count" class="form-control"
                                   value="{{ $replacement->new_sd_card_count }}" min="0" placeholder="Enter number of new SD cards">
                        </td>
                    </tr>

                    <tr>
                        <th>Camera Type</th>
                        <td>
                            <select name="camera_id" class="form-select" required>
                                @foreach($cameras as $camera)
                                    <option value="{{ $camera->id }}" {{ $camera->id == $replacement->camera_id ? 'selected' : '' }}>
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
                                <option value="" {{ is_null($replacement->nvr_camera_id) ? 'selected' : '' }}>None</option>
                                @foreach($nvrs as $nvr)
                                    <option value="{{ $nvr->id }}" {{ $nvr->id == $replacement->nvr_camera_id ? 'selected' : '' }}>
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
                                   value="{{ $replacement->number_of_nvr }}" min="0">
                        </td>
                    </tr>

                    <tr>
                        <th>Incident Type</th>
                        <td>
                            <select name="camera_replacement_incident_id" class="form-select">
                                <option value="" disabled>Select Incident Type</option>
                                @foreach($cameraReplacementIncidents as $incident)
                                    <option value="{{ $incident->id }}" {{ $incident->id == $replacement->camera_replacement_incident_id ? 'selected' : '' }}>{{ $incident->english_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th>Notes</th>
                        <td>
                            <textarea name="notes" class="form-control" rows="3">{{ $replacement->notes }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>Donors (optional)</th>
                        <td>
                            <select name="donor_ids[]" class="form-select" multiple>
                                @foreach($donors as $donor)
                                    <option value="{{ $donor->id }}" 
                                        {{ $replacement->donors->contains('donor_id', $donor->id) ? 'selected' : '' }}>
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
                <a href="{{ route('camera.all') }}" class="btn btn-secondary">Cancel</a>
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
        // إذا كان الكمباوند المختار لا ينتمي للـ community أخفي الاختيار
        if (!found) {
            compoundSelect.value = '';
        }
    }
    communitySelect.addEventListener('change', filterCompounds);
    filterCompounds();
});
</script>
@endsection
