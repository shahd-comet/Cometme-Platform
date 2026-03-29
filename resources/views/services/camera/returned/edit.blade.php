@extends('layouts/layoutMaster')

@section('title', 'Edit Returned Camera')

@section('content')
<div class="container">
    <div class="card mt-3">
        <div class="card-header">Edit Returned Camera</div>
        <div class="card-body">
            <form method="POST" action="{{ route('camera-returned.update', $returned->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <label>Camera Community</label>
                        <select id="camera_community_id" name="camera_community_id" class="selectpicker form-control" data-live-search="true" required>
                            <option value="" disabled>Choose Camera Community</option>
                            @foreach($cameraCommunities as $cc)
                                @php
                                    $communityName = optional($cc->community)->english_name ?? optional($cc->community)->arabic_name ?? null;
                                    $compoundName = optional($cc->compound)->english_name ?? null;
                                    // Prefer "Community - Compound" when both available,
                                    // but avoid repeating identical names.
                                    if ($communityName && $compoundName) {
                                        $c = trim($communityName);
                                        $p = trim($compoundName);
                                        $label = ($c === $p) ? $c : ($c . ' - ' . $p);
                                    } else {
                                        $label = $cc->display_name ?? null;
                                        if (!$label) {
                                            $repositoryName = optional($cc->repository)->name ?? null;
                                            $householdName = optional($cc->household)->english_name ?? null;

                                            // build base from unique parts
                                            $parts = array_filter([$communityName, $repositoryName]);
                                            $parts = array_values(array_unique(array_map('trim', $parts)));
                                            $base = implode(' / ', $parts);

                                            if (!empty($base)) {
                                                if ($compoundName && in_array(trim($compoundName), $parts, true)) {
                                                    $label = $base;
                                                } else {
                                                    $label = $compoundName ? ($base . ' - ' . $compoundName) : $base;
                                                }
                                            } else {
                                                $label = $householdName ?: $compoundName ?: $repositoryName ?: 'Unknown';
                                            }
                                        }
                                    }
                                @endphp
                                <option value="{{ $cc->id }}" data-community="{{ $cc->community_id }}" title="{{ $cc->display_name }}" @if($returned->camera_community_id == $cc->id) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Date of Return</label>
                        <input type="date" name="date_of_return" class="form-control" required value="{{ old('date_of_return', $returned->date) }}">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>Camera Type</label>
                        <select name="camera_id" class="selectpicker form-control" data-live-search="true" required>
                            <option disabled>Choose Camera Type</option>
                            @foreach($cameras as $camera)
                                <option value="{{ $camera->id }}" @if($returned->camera_id == $camera->id) selected @endif>{{ $camera->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label># of Cameras</label>
                        <input type="number" name="number_of_cameras" class="form-control" value="{{ old('number_of_cameras', $returned->number_of_cameras) }}" min="0" required>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>NVR Type</label>
                        <select name="nvr_camera_id" class="selectpicker form-control" data-live-search="true">
                            <option disabled>Choose NVR</option>
                            @foreach($nvrCameras as $nvr)
                                <option value="{{ $nvr->id }}" @if($returned->nvr_camera_id == $nvr->id) selected @endif>{{ $nvr->model }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label># of NVRs</label>
                        <input type="number" name="number_of_nvrs" class="form-control" value="{{ old('number_of_nvrs', $returned->number_of_nvr) }}" min="0">
                    </div>

                    <div class="col-md-6 mt-3">
                        <label>SD Card Number</label>
                        <input type="number" name="sd_card_number" class="form-control" value="{{ old('sd_card_number', $returned->sd_card_number) }}">
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Where did the Camera Return ?</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_na" value="0" {{ $returned->status == 0 ? 'checked' : '' }}>
                                <p class="form-check-label" for="status_na">N/A</p>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_commet" value="1" {{ $returned->status == 1 ? 'checked' : '' }}>
                                <p class="form-check-label" for="status_commet">to Commet</p>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_community" value="2" {{ $returned->status == 2 ? 'checked' : '' }}>
                                <p class="form-check-label" for="status_community">to Community</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Repository</label>
                        <select name="repository_id" class="selectpicker form-control" data-live-search="true">
                            <option value="" selected>Choose Repository</option>
                            @foreach($repositories as $repo)
                                <option value="{{ $repo->id }}" @if($returned->repository_id == $repo->id) selected @endif>{{ $repo->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control">{{ old('notes', $returned->notes) }}</textarea>
                    </div>

                </div>

                <div class="mt-3 text-end">
                    <a href="{{ route('camera-returned.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
