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

<div id="createAddition" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Add New Camera Addition</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" action="{{ route('camera-additions.store') }}">
                @csrf
                <div class="modal-body">
                    <table class="table table-bordered form-table">
                        <tr>
                            <th>Camera Community</th>
                            <td>
                                <select id="camera_community_id" name="camera_community_id" class="form-select" required>
                                    <option value="" selected disabled>Select Camera Community</option>
                                    @foreach($cameraCommunities as $cc)
                                        <option value="{{ $cc->id }}" data-community="{{ $cc->community_id }}">
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
                                    <option value="" selected disabled>Choose Compound...</option>
                                    @foreach($compounds as $compound)
                                        <option value="{{$compound->id}}" data-community="{{ $compound->community_id }}">
                                            {{$compound->english_name}}
                                        </option>
                                    @endforeach
                                </select>
                                <span id="compound_name_display" style="display:none;"></span>
                            </td>
                        </tr>

                        <tr>
                            <th>Date of Addition</th>
                            <td><input type="date" id="date_of_addition" name="date_of_addition" class="form-control" required></td>
                        </tr>

                        <tr>
                            <th>Number of Cameras</th>
                            <td><input type="number" id="number_of_cameras" name="number_of_cameras" class="form-control" min="0" required></td>
                        </tr>

                        <tr>
                            <th>SD Card Number</th>
                            <td><input type="number" id="sd_card_number" name="sd_card_number" class="form-control" placeholder="Enter SD Card Number"></td>
                        </tr>

                        <tr>
                            <th>Camera Type</th>
                            <td>
                                <select id="camera_id" name="camera_id" class="form-select" required>
                                    <option value="" selected disabled>Select Camera Type</option>
                                    @foreach($cameras as $camera)
                                        <option value="{{ $camera->id }}">{{ $camera->model }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>NVR Camera (optional)</th>
                            <td>
                                <select id="nvr_camera_id" name="nvr_camera_id" class="form-select">
                                    <option value="" selected>None</option>
                                    @foreach($nvrs as $nvr)
                                        <option value="{{ $nvr->id }}">{{ $nvr->model }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>Number of NVRs</th>
                            <td><input type="number" id="number_of_nvr" name="number_of_nvr" class="form-control" min="0"></td>
                        </tr>

                        <tr>
                            <th>Notes (optional)</th>
                            <td><textarea id="notes" name="notes" class="form-control" rows="3"></textarea></td>
                        </tr>
                        <tr>
                            <th>Donors (optional)</th>
                            <td>
                                <select id="donor_ids" name="donor_ids[]" class="form-select" multiple>
                                    @foreach($donors as $donor)
                                        <option value="{{ $donor->id }}">{{ $donor->donor_name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple donors</small>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Addition</button>
                </div>
            </form>
        </div>
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
