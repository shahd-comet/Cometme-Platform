@php
    // Pick the correct H2O and Grid objects (user or public)
    $h2o = $h2oUser ?? $h2oPublic ?? null;
    $grid = $gridUser ?? $gridPublic ?? null;
    $network = $networkUser ?? null;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Edit Water User')

@include('layouts.all')

@section('content')

<h4 class="py-3 breadcrumb-wrapper mb-4">
    <span class="text-muted fw-light">Edit</span>

    @if($allWaterHolder->Household)
        {{ $allWaterHolder->Household->english_name }}
    @elseif($allWaterHolder->PublicStructure)
        {{ $allWaterHolder->PublicStructure->english_name }}
    @endif

    <span class="text-muted fw-light">Information</span>
</h4>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('all-water.update', $allWaterHolder->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- Basic Info Section --}}
            @include('users.water.all.basic-info')

            <hr>

            {{-- H2O Section --}}
            @include('users.water.all.h2o')

            <hr>

            {{-- Grid Section --}}
            @include('users.water.all.grid')

            <hr>

            {{-- Network Section --}}
            @include('users.water.all.network')

            <hr>

            {{-- Notes Section --}}
            @include('users.water.all.notes')

            <hr>

            {{-- Donors Section --}}
            @include('users.water.all.donors')

            {{-- Submit Button --}}
            <div class="row mt-3">
                <div class="col-xl-4">
                    <button type="submit" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection