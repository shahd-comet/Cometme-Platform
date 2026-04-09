@extends('layouts/layoutMaster')

@section('title', 'edit internet system')

@include('layouts.all')

@section('content')

<div class="container">
    <h2>Create A new Internet System Return</h2>
    <form id="returnForm" action="{{ route('internet.returns.store') }}" method="POST">
        @csrf
        <!-- validation errors -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
                <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Return Details</h5>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="internet_system_community_id">Community</label>
                        <select name="internet_system_community_id" id="internet_system_community_id" class="form-control selectpicker" data-live-search="true">
                            <option value="">-- Select community --</option>
                            @foreach($communities as $c)
                                    <option value="{{ $c->id }}">{{ $c->english_name ?? ($c->arabic_name ?? $c->id) }}</option>
                                @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="internet_system_id">Internet System</label>
                        <select name="internet_system_id" id="internet_system_id" class="form-control selectpicker" data-live-search="true" disabled>
                            <option value="">-- Select system --</option>
                            @foreach($systems as $s)
                                <option value="{{ $s->id }}">{{ $s->system_name ?? $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="returned_by">Returned By (user)</label>
                        <select name="returned_by" id="returned_by" class="form-control selectpicker" data-live-search="true">
                            <option value="">-- Select user --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-4 form-group">
                        <label for="return_date">Return Date</label>
                        <input type="datetime-local" name="return_date" id="return_date" class="form-control" />
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="0">Pending</option>
                            <option value="1">Received</option>
                            <option value="2">Inspected</option>
                            <option value="3">Approved</option>
                            <option value="4">Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="reason_id">Reason</label>
                        <select name="reason_id" id="reason_id" class="form-control selectpicker">
                            <option value="">-- Reason (optional) --</option>
                            @foreach($reasons as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                </div>

            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<!-- components Section -->


@endsection
@section('scripts')
<script>
    // important Functions
    // Fetch systems for selected community

</script>

@endsection