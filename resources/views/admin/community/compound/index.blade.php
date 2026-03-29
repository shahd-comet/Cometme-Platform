@extends('layouts/layoutMaster')

@section('title', 'community compounds')

@include('layouts.all')

@section('content')
<style>
    label, input {
    display: block;
}

label, table {
    margin-top: 20px;
}
</style>

<p> 
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseCompoundCommunityExport" aria-expanded="false" 
        aria-controls="collapseCompoundCommunityExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export Data
    </button>
</p>

<div class="collapse multi-collapse container mb-4" id="collapseCompoundCommunityExport">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <h5>
                                Export Community-Compound Report 
                                <i class='fa-solid fa-file-excel text-info'></i>
                            </h5>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-2">
                            <fieldset class="form-group">
                                <button class="" id="clearCompoundCommunityFiltersButton">
                                <i class='fa-solid fa-eraser'></i>
                                    Clear Filters
                                </button>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <form method="POST" enctype='multipart/form-data' 
                    action="{{ route('community-compound.export') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="community" class="selectpicker form-control" 
                                    data-live-search="true">
                                        <option disabled selected>Search Community</option>
                                        @foreach($communities as $community)
                                        <option value="{{$community->id}}">
                                            {{$community->english_name}}
                                        </option>
                                        @endforeach
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="region" class="selectpicker form-control" 
                                    data-live-search="true">
                                        <option disabled selected>Search Region</option>
                                        @foreach($regions as $region)
                                        <option value="{{$region->id}}">
                                            {{$region->english_name}}
                                        </option>
                                        @endforeach
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <select name="compound" class="selectpicker form-control" 
                                    data-live-search="true">
                                        <option disabled selected>Search Compound</option>
                                        @foreach($compounds as $compound)
                                        <option value="{{$compound->id}}">
                                            {{$compound->english_name}}
                                        </option>
                                        @endforeach
                                    </select> 
                                </fieldset>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3">
                                <fieldset class="form-group">
                                    <button class="btn btn-info" type="submit">
                                        <i class='fa-solid fa-file-excel'></i>
                                        Export Excel
                                    </button>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </form>
            </div>  
        </div>
    </div> 
</div> 

<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">All </span> community compounds
</h4>

@if(session()->has('message'))
    <div class="row">
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    </div>
@endif

@if(session()->has('error'))
    <div class="row">
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    </div>
@endif

<div class="container">
    <div class="card my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Region</label>
                        <select name="region_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByRegion">
                            <option disabled selected>Choose one...</option>
                            @foreach($regions as $region)
                                <option value="{{$region->id}}">{{$region->english_name}}</option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Sub Region</label>
                        <select name="sub_region_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterBySubRegion">
                            <option disabled selected>Choose one...</option>
                            @foreach($subregions as $subRegion)
                                <option value="{{$subRegion->id}}">{{$subRegion->english_name}}</option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Community</label>
                        <select name="community_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByCommunity">
                            <option disabled selected>Choose one...</option>
                            @foreach($communities as $community)
                                <option value="{{$community->id}}">{{$community->english_name}}</option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Clear All Filters</label>
                        <button class="btn btn-dark" id="clearFiltersButton">
                            <i class='fa-solid fa-eraser'></i>
                            Clear Filters
                        </button>
                    </fieldset>
                </div>
            </div>
               
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" role="tablist" id="inProgressTabs" style="padding-top:25px">
                <li class="nav-item">
                    <a class="nav-link active" id="compounds-tab" data-bs-toggle="tab" href="#compounds" role="tab" 
                        aria-controls="compounds" aria-selected="true">
                        <i class='fa-solid fa-box me-1'></i>
                        Community Compounds
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="compound_households-tab" data-bs-toggle="tab" href="#compound_households" role="tab" 
                        aria-controls="compound_households" aria-selected="false">
                        <i class='fa-solid fa-boxes me-1'></i>
                        Compound Households
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3">
                <!-- All Compounds Tab -->
                <div class="tab-pane fade show active" id="compounds" role="tabpanel" 
                    aria-labelledby="compounds-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                        @if(optional(Auth::guard('user')->user())->user_type_id == 1 ||
                            optional(Auth::guard('user')->user())->user_type_id == 2)
                            <div style="margin-top:18px">
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#createCommunityCompound">
                                    Create New Community Compound	
                                </button>
                                @include('admin.community.compound.create_compound')
                            </div>
                        @endif
                        </div>
                    </div>
                    
                    <table id="compoundTable" class="table table-striped data-table-compounds my-2">
                        <thead>
                            <tr>
                                <th >Compound</th>
                                <th >Community</th>
                                <th >Region</th>
                                @if(Auth::guard('user')->user()->user_type_id == 1 ||
                                    Auth::guard('user')->user()->user_type_id == 2  )
                                    <th >Options</th>
                                @else
                                    <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <!-- All Household-Compounds Tab -->
                <div class="tab-pane fade" id="compound_households" role="tabpanel" aria-labelledby="compound_households-tab">

                    @if(optional(Auth::guard('user')->user())->user_type_id == 1 ||
                        optional(Auth::guard('user')->user())->user_type_id == 2 ||
                        optional(Auth::guard('user')->user())->user_type_id == 3 )
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-4">
                                <button type="button" class="btn btn-success" 
                                    data-bs-toggle="modal" data-bs-target="#createCompoundHouseholds">
                                    Create New Compound Households	
                                </button>
                                @include('admin.community.compound.create')
                            </div>
                        </div>
                    @endif

                    <table id="compoundCommunityTable" class="table table-striped my-2 data-table-compound-households">
                        <thead>
                            <tr>
                                <th class="text-center">Confirmed Household/Public Structure</th>
                                <th class="text-center">Community</th>
                                <th class="text-center">Region</th>
                                <th class="text-center">Compound</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
 
    // Clear Filters for Export
    $('#clearFiltersButton').on('click', function() {

        $('.selectpicker').prop('selectedIndex', 0);
        $('.selectpicker').selectpicker('refresh');
    });

    $(function () {

        // keep track of initialized tables
        var tables = {};

        function initCompoundsTable() {

            if (tables.compounds) return;
            tables.compounds = $('.data-table-compounds').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('compound.index') }}",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.sub_region_filter = $('#filterBySubRegion').val();
                    }
                }, 
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'community_english_name', name: 'community_english_name'},
                    {data: 'name', name: 'name'},
                    {data: 'action'}
                ]
            });
        }

        function initCompoundHouseholdTable() {

            if (tables.compound_households) return;
            tables.compound_households = $('.data-table-compound-households').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('community-compound.index') }}",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.community_filter = $('#filterByCommunity').val();
                        d.region_filter = $('#filterByRegion').val();
                        d.sub_region_filter = $('#filterBySubRegion').val();
                    }
                },
                columns: [
                    {data: 'household', name: 'household'},
                    {data: 'community_english_name', name: 'community_english_name'},
                    {data: 'name', name: 'name'},
                    {data: 'english_name', name: 'english_name'},
                    {data: 'action'}
                ]
            });
        }


        initCompoundsTable();

        // On tab shown, lazy-init the target table
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr('href');
            if (target == '#compounds') initCompoundsTable();
            if (target == '#compound_households') initCompoundHouseholdTable();

            if ($('.selectpicker').length && typeof $('.selectpicker').selectpicker === 'function') {

                $('.selectpicker').selectpicker('refresh');
            }
        });


        // Reload initialized tables when any filter changes
        function reloadInitializedTables() {

            if (tables.compounds) tables.compounds.ajax.reload();
            if (tables.compound_households) tables.compound_households.ajax.reload();
        }

        $('#filterByCommunity, #filterByRegion, #filterBySubRegion').on('change', function () {

            if (tables.compound_households) tables.compound_households.ajax.reload();
            if (tables.compounds) tables.compounds.ajax.reload();
            updateCountValue();
        });

        // Clear filters
        $(document).on('click', '#clearFiltersButton', function () {
            $('#filterByCommunity').prop('selectedIndex', 0);
            $('#filterByRegion').prop('selectedIndex', 0);
            $('#filterBySubRegion').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            if (tables.compound_households) tables.compound_households.ajax.reload();
            if (tables.compounds) tables.compounds.ajax.reload();
            updateCountValue();
        });


        // View record details
        $('#compoundTable').on('click', '.detailsCompoundButton', function() {
            var id = $(this).data('id');
            var url = window.location.href; 
           
            url = url +'/'+ id ;
            window.open(url); 
        }); 

        // View record update page
        $('#compoundTable').on('click', '.updateCompoundButton', function() {
            var id = $(this).data('id');
            var url = window.location.href; 
            url = url +'/'+ id +'/edit';
             
            // AJAX request
            $.ajax({
                url: '/compound/' + id + '/editpage',
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    window.open(url, "new"); 
                }
            });
        });

        // delete community compound
        $('#compoundTable').on('click', '.deleteCompound',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this compound?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteCompound') }}",
                        type: 'get',
                        data: {id: id},
                        success: function(response) {
                            if(response.success == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'Okay!'
                                }).then((result) => {
                                    $('#compoundTable').DataTable().draw();
                                });
                            } else {

                                alert("Invalid ID.");
                            }
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
                }
            });
        });

        // delete compound household
        $('#compoundCommunityTable').on('click', '.deleteCompoundHousehold',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this sub compound household?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {
                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteCompoundHousehold') }}",
                        type: 'get',
                        data: {id: id},
                        success: function(response) {
                            if(response.success == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.msg,
                                    showDenyButton: false,
                                    showCancelButton: false,
                                    confirmButtonText: 'Okay!'
                                }).then((result) => {
                                    $('#compoundCommunityTable').DataTable().draw();
                                });
                            } else {

                                alert("Invalid ID.");
                            }
                        }
                    });
                } else if (result.isDenied) {
                    Swal.fire('Changes are not saved', '', 'info')
                }
            });
        });
    });
</script>
@endsection
