@extends('layouts/layoutMaster')



@section('title', 'Create Agriculture System')



@include('layouts.all')



@section('content')

<div class="container-fluid">

    <!-- Header -->

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="mb-0">

            <i class="bx bx-plus me-2"></i>Create New Agriculture System

        </h4>

        <a href="{{ route('agriculture-system.index') }}" class="btn btn-outline-secondary">

            <i class="bx bx-arrow-back me-1"></i>Back to List

        </a>

    </div>



    <!-- Success Message -->

    @if(session('message'))

        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <i class="bx bx-check-circle me-2"></i>{{ session('message') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    @endif



    <!-- Error Message -->

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <i class="bx bx-error-circle me-2"></i>{{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    @endif



    <!-- Validation Errors -->

    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <i class="bx bx-error-circle me-2"></i>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    @endif

    <!-- Form Card -->

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="bx bx-leaf me-2"></i>Agriculture System Information

            </h5>

        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('agriculture-system.store') }}" id="createSystemForm">

                @csrf



                <div class="row mb-3">

                    <div class="col-md-4">

                        <label class="form-label" for="installation_year">Installation Year</label>

                        <input type="number" 

                               class="form-control @error('installation_year') is-invalid @enderror" 

                               id="installation_year" 

                               name="installation_year" 

                               value="{{ old('installation_year') }}" 

                               min="1900" 

                               max="{{ date('Y') + 10 }}"

                               placeholder="e.g., {{ date('Y') }}">

                        @error('installation_year')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>

                    

                    <div class="col-md-6">

                        <label class="form-label" for="name">

                            System Name <span class="text-primary"></span><span class="text-danger">*</span>

                        </label>

                        <input type="text" 

                               class="form-control @error('name') is-invalid @enderror" 

                               id="name" 

                               name="name" 

                               value="{{ old('name') }}" 

                               placeholder="Enter system name"

                               required>

                        @error('name')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>

                    

                    

                </div>





                    

                    <div class="row mb-3 ">

                        <div class="col-md-4">



                        

                        <label class="form-label" for="azolla_type_id">

                            Azolla Type <span class="text-danger">*</span>

                        </label>

                        <select class="form-select @error('azolla_type_id') is-invalid @enderror" 

                                id="azolla_type_id" 

                                name="azolla_type_id" 

                                required>

                            <option value="">Select Azolla Type</option>

                            @foreach($azollaTypes as $type)

                                <option value="{{ $type->id }}" 

                                        {{ old('azolla_type_id') == $type->id ? 'selected' : '' }}>

                                    {{ $type->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('azolla_type_id')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                        </div>

                        <div class="col-md-4">

                        <label class="form-label" for="agriculture_system_cycle_id">System Cycle</label>

                        <select class="form-select @error('agriculture_system_cycle_id') is-invalid @enderror" 

                                id="agriculture_system_cycle_id" 

                                name="agriculture_system_cycle_id">

                            <option value="">Select System Cycle (Optional)</option>

                            @foreach($agricultureSystemCycles as $cycle)

                                <option value="{{ $cycle->id }}" 

                                        {{ old('agriculture_system_cycle_id') == $cycle->id ? 'selected' : '' }}>

                                    {{ $cycle->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('agriculture_system_cycle_id')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                        </div>

                    </div>

                <!-- Components Section -->

                <div class="mb-4">

                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <h6 class="mb-0">Components</h6>

                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addComponentModal">

                            <i class="bx bx-plus"></i> Add Component

                        </button>

                    </div>



                    <input type="hidden" name="components" id="componentsInput" value="{{ old('components') }}">



                    <div class="table-responsive">

                        <table class="table table-sm table-striped" id="componentsTable">

                            <thead>

                                <tr>

                                    <th>Category</th>

                                    <th>Component</th>

                                    <th>Model</th>

                                    <th class="text-center">Quantity</th>

                                    <th class="text-end">Unit Price</th>

                                    <th class="text-end">Total</th>

                                    <th class="text-center">Action</th>

                                </tr>

                            </thead>

                            <tbody>

                                <!-- rows added dynamically -->

                            </tbody>

                        </table>

                    </div>

                </div>

                <script>

                    $(function(){

                        const categories = @json($componentCategories ?? []);

                        const $category = $('#componentCategory');

                        const $component = $('#componentSelect');

                        const $model = $('#componentModelSelect');

                        const $qty = $('#componentQuantity');

                        const $unitPrice = $('#componentUnitPrice');

                        const $addBtn = $('#addComponentConfirmBtn');

                        const $tableBody = $('#componentsTable tbody');

                        let components = [];



                        function refreshHidden() {

                            $('#componentsInput').val(JSON.stringify(components));

                        }



                        function renderTable() {

                            $tableBody.empty();

                            components.forEach((c, idx) => {

                                const tr = $('<tr>');

                                tr.append($('<td>').text(c.category_name || ''));

                                tr.append($('<td>').text(c.component_name || ''));

                                tr.append($('<td>').text(c.model_name || ''));

                                tr.append($('<td class="text-center">').text(c.quantity));

                                tr.append($('<td class="text-end">').text((c.unit_price || 0).toFixed ? (parseFloat(c.unit_price||0).toFixed(2)) : c.unit_price));

                                const totalVal = (parseFloat(c.unit_price || 0) * parseFloat(c.quantity || 0)) || 0;

                                tr.append($('<td class="text-end">').text(totalVal.toFixed(2)));

                                const delBtn = $('<button type="button" class="btn btn-sm btn-danger">Remove</button>');

                                delBtn.on('click', function(){

                                    components.splice(idx,1);

                                    renderTable();

                                    refreshHidden();

                                });

                                tr.append($('<td class="text-center">').append(delBtn));

                                $tableBody.append(tr);

                            });

                        }



                        // populate categories

                        $category.empty().append('<option value="">Select Category</option>');

                        categories.forEach(cat => {

                            $category.append(`<option value="${cat.id}">${cat.name}</option>`);

                        });



                        // when category changes, fetch components

                            $category.on('change', function(){

                            const id = $(this).val();

                            $component.prop('disabled', true).empty().append('<option>Loading...</option>');

                            $model.prop('disabled', true).empty().append('<option value="">Select component first</option>');

                            $addBtn.prop('disabled', true);

                            if (!id) {

                                $component.prop('disabled', true).empty().append('<option value="">Select category first</option>');

                                return;

                            }

                            $.get(`/agriculture-system/components/${id}`, function(data){

                                $component.prop('disabled', false).empty().append('<option value="">Select Component</option>');

                                data.forEach(c => $component.append(`<option value="${c.id}">${c.english_name || c.english_name || c.name || c.english_name}</option>`));

                            });

                        });



                        // when component changes, fetch models

                        $component.on('change', function(){

                            const id = $(this).val();

                            $model.prop('disabled', true).empty().append('<option>Loading...</option>');

                            $addBtn.prop('disabled', true);

                            if (!id) {

                                $model.prop('disabled', true).empty().append('<option value="">Select component first</option>');

                                return;

                            }

                            $.get(`/agriculture-system/models/${id}`, function(data){

                                $model.prop('disabled', false).empty().append('<option value="">Select Model (optional)</option>');

                                data.forEach(m => $model.append(`<option value="${m.id}" data-brand="${m.brand || ''}" data-unit="${m.unit || ''}">${m.model}${m.brand?(' - '+m.brand):''}</option>`));

                                    $addBtn.prop('disabled', false);

                            });

                        });



                        // add component confirm

                        $addBtn.on('click', function(){

                            const catId = $category.val();

                            const catName = $category.find('option:selected').text();

                            const compId = $component.val();

                            const compName = $component.find('option:selected').text();

                            const modelId = $model.val() || null;

                            const modelName = $model.find('option:selected').text() || '';

                            const quantity = parseInt($qty.val()) || 1;

                            const unitPrice = parseFloat($unitPrice.val()) || 0;



                            if (!compId) return;



                            components.push({

                                category_id: catId,

                                category_name: catName,

                                component_id: compId,

                                component_name: compName,

                                model_id: modelId,

                                model_name: modelName,

                                quantity: quantity,

                                unit_price: unitPrice

                            });

                            renderTable();

                            refreshHidden();

                            // reset modal selects

                            $category.val('');

                            $component.prop('disabled', true).empty().append('<option value="">Select category first</option>');

                            $model.prop('disabled', true).empty().append('<option value="">Select component first</option>');

                            $qty.val(1);

                            // move focus back to the Add Component trigger before hiding modal

                            try {

                                const trigger = document.querySelector('[data-bs-toggle="modal"][data-bs-target="#addComponentModal"]');

                                if (trigger) trigger.focus();

                                const modalEl = document.getElementById('addComponentModal');

                                if (modalEl && typeof bootstrap !== 'undefined') {

                                    let bsModal = null;

                                    if (bootstrap.Modal && typeof bootstrap.Modal.getInstance === 'function') {

                                        bsModal = bootstrap.Modal.getInstance(modalEl);

                                    }

                                    if (!bsModal && bootstrap.Modal) {

                                        bsModal = new bootstrap.Modal(modalEl);

                                    }

                                    if (bsModal && typeof bsModal.hide === 'function') bsModal.hide();

                                } else {

                                    // fallback for other modal handlers

                                    if (typeof $ === 'function' && typeof $('#addComponentModal').modal === 'function') {

                                        $('#addComponentModal').modal('hide');

                                    }

                                }

                            } catch (e) {

                                // ignore errors

                            }

                        });



                        // load previous components if present

                        try {

                            const old = $('#componentsInput').val();

                            if (old) {

                                const parsed = JSON.parse(old);

                                if (Array.isArray(parsed)) {

                                    components = parsed;

                                    renderTable();

                                }

                            }

                        } catch(e){}



                    });

                </script>



                <div class="row mb-4">

                    <div class="col-12">

                        <label class="form-label" for="description">Description</label>

                        <textarea class="form-control @error('description') is-invalid @enderror" 

                                  id="description" 

                                  name="description" 

                                  rows="4" 

                                  placeholder="Describe the agriculture system...">{{ old('description') }}</textarea>

                        @error('description')

                            <div class="invalid-feedback">{{ $message }}</div>

                        @enderror

                    </div>

                </div>



                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('agriculture-system.index') }}" class="btn btn-outline-secondary">

                        <i class="bx bx-x me-1"></i>Cancel

                    </a>

                    <button type="submit" class="btn btn-primary">

                        <i class="bx bx-check me-1"></i>Create System

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

                    <div class="modal fade" id="addComponentModal" tabindex="-1" aria-hidden="true">

                        <div class="modal-dialog">

                            <div class="modal-content">

                                <div class="modal-header">

                                    <h5 class="modal-title">Add Component</h5>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                </div>

                                <div class="modal-body">

                                    <div class="mb-3">

                                        <label class="form-label">Category</label>

                                        <select id="componentCategory" class="form-select">

                                            <option value="">Loading...</option>

                                        </select>

                                    </div>



                                    <div class="mb-3">

                                        <label class="form-label">Component</label>

                                        <select id="componentSelect" class="form-select" disabled>

                                            <option value="">Select category first</option>

                                        </select>

                                    </div>



                                    <div class="mb-3">

                                        <label class="form-label">Model</label>

                                        <select id="componentModelSelect" class="form-select" disabled>

                                            <option value="">Select component first</option>

                                        </select>

                                    </div>



                                    <div class="mb-3">

                                        <label class="form-label">Quantity</label>

                                        <input type="number" id="componentQuantity" class="form-control" min="1" value="1">

                                    </div>



                                    <div class="mb-3">

                                        <label class="form-label">Unit Price</label>

                                        <input type="number" id="componentUnitPrice" class="form-control" min="0" step="0.01" value="0.00">

                                    </div>

                                </div>

                                <div class="modal-footer">

                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                                    <button type="button" class="btn btn-primary" id="addComponentConfirmBtn" disabled>Add</button>

                                </div>

                            </div>

                        </div>

                    </div>

@endsection