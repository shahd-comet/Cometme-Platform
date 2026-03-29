@extends('layouts/layoutMaster')

@section('title', 'Edit System Components')

@section('content')

<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Agriculture Users / </span> Edit System Components
</h4>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Edit Components for {{ optional($system)->name ?? 'System' }} (Holder: {{ optional($holder->household)->english_name ?? $holder->id }})</h5>
        <a href="{{ route('argiculture-user.show', $holder->id) }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form method="post" action="{{ route('argiculture-user.systems.components.update', ['holder' => $holder->id, 'system' => $system->id]) }}">
            @csrf
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Component</th>
                            <th>Model</th>
                            <th style="width:120px">Quantity</th>
                            <th style="width:140px">Unit Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($merged as $index => $row)
                            @php
                                $comp = App\Models\AgricultureComponent::find($row['component_id']);
                                $model = App\Models\AgricultureComponentModel::find($row['model_id']);
                            @endphp
                            <tr>
                                <td>{{ optional($comp)->english_name ?? '—' }}</td>
                                <td>{{ optional($model)->model ?? '—' }}</td>
                                <td>
                                    <input type="hidden" name="system_component_id[]" value="{{ $row['system_component_id'] }}">
                                    <input type="hidden" name="holder_component_id[]" value="{{ $row['holder_component_id'] }}">
                                    <input type="number" step="0.01" name="quantity[]" value="{{ $row['quantity'] }}" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="unit_price[]" value="{{ $row['unit_price'] }}" class="form-control form-control-sm">
                                </td>
                                <td class="text-end">
                                    @if($row['holder_component_id'])
                                        <form method="post" action="{{ route('argiculture-user.systems.components.delete', ['holder' => $holder->id, 'system' => $system->id, 'holder_component' => $row['holder_component_id']]) }}" onsubmit="return confirm('Remove this holder-specific component?');">
                                            @csrf
                                            <button class="btn btn-sm btn-danger" type="submit">Remove</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <button class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection
