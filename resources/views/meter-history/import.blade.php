@extends('layouts/layoutMaster')

@section('title', 'Import Meter History')

@include('layouts.all')

@section('content')

<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">Meter History / </span>Import
</h4>

@if(session()->has('success'))
    <div class="row">
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session()->get('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div class="row">
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session()->get('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if(session()->has('message'))
    <div class="row">
        <div class="alert alert-info alert-dismissible" role="alert">
            {{ session()->get('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<div class="container">
    <div class="card my-2">
        <div class="card-body">
            <h5 class="py-3 breadcrumb-wrapper mb-4">
                <span class="text-muted fw-light">Import </span>Meter History Excel
            </h5>

            <div class="row">
                <form action="{{ route('meter-history.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <label for="excel_file" class="form-label">Select Excel File</label>
                        <input type="file" name="excel_file" class="form-control" id="excel_file" 
                               accept=".xlsx,.xls,.csv" required>
                        @error('excel_file')
                        <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mt-3">
                        <button id="meterHistoryImportButton" type="submit" class="btn btn-success">
                            <i class='fa-solid fa-upload'></i>
                            Import Meter History
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
