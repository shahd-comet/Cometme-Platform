@php
  $pricingModal = true;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Agriculture Issues-Actions')

@include('layouts.all')

@section('content')
 
<p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" 
        data-target="#collapseAgricultureExport" aria-expanded="false" 
        aria-controls="collapseAgricultureExport">
        <i class="menu-icon tf-icons bx bx-export"></i>
        Export  
    </button>
</p> 



<div class="collapse multi-collapse mb-4" id="collapseAgricultureExport">
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-xl-10 col-lg-10 col-md-10">
                                <h5>
                                Export Agriculture Issues/Actions Report
                                    <i class='fa-solid fa-file-excel text-info'></i>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <form method="POST" enctype='multipart/form-data' 
                        action="{{ route('agriculture-issue.export') }}">
                        @csrf
                        <div class="card-body"> 
                            <div class="row" style="margin-top:20px">
                                <div class="col-xl-3 col-lg-3 col-md-3">
                                    <button class="btn btn-info" type="submit">
                                        <i class='fa-solid fa-file-excel'></i>
                                        Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>  
            </div>
        </div> 
    </div> 
</div>

<h4 class="py-3 breadcrumb-wrapper mb-4">
  <span class="text-muted fw-light">All </span> Agriculture Issues/Actions
</h4>

@if(session()->has('message'))
    <div class="row">
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    </div>
@endif

<div class="container">
    <div class="card my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Category</label>
                        <select name="category_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByCategory">
                            <option disabled selected>Search Category</option>
                            @foreach($actionCategories as $actionCategory)
                                <option value="{{$actionCategory->id}}">{{$actionCategory->english_name}}</option>
                            @endforeach
                        </select> 
                    </fieldset>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3">
                    <fieldset class="form-group">
                        <label class='col-md-12 control-label'>Filter By Action</label>
                        <select name="action_id" class="selectpicker form-control" 
                            data-live-search="true" id="filterByAction">
                            <option disabled selected>Search Action</option>
                            @foreach($agricultureActions as $agricultureAction)
                                <option value="{{$agricultureAction->id}}">{{$agricultureAction->english_name}}</option>
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
                    <a class="nav-link active" data-bs-toggle="tab" href="#agriculture-actions" role="tab">
                        <i class='fas fa-tasks me-2'></i>
                        Actions
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#agriculture-issues" role="tab">
                        <i class='fas fa-bug me-2'></i> 
                        Issues
                    </a>
                </li>

            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="agricultureTabContent">
                <!-- All Actions Tab -->
                <div class="tab-pane fade show active" id="agriculture-actions" role="tabpanel" 
                    aria-labelledby="actions-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                            @if(Auth::guard('user')->user()->user_type_id == 1 ||
                                Auth::guard('user')->user()->user_type_id == 2)
                                <div style="margin-top:18px">
                                    <button type="button" class="btn btn-success" 
                                        data-bs-toggle="modal" data-bs-target="#createActionAgriculture">
                                        Create New Agriculture Action	
                                    </button>
                                    @include('agriculture.issue.create-action')
                                </div>
                            @endif
                        </div>
                    </div>
                    <table id="agricultrueActionsTable" class="table table-striped my-2 data-table-action">
                        <thead>
                            <tr>
                                <th class="text-center">Action (English)</th>
                                <th class="text-center">Action (Arabic)</th>
                                <th class="text-center">Action Category</th>
                                <th class="text-center">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>

                <!-- All Issues Tab -->
                <div class="tab-pane fade" id="agriculture-issues" role="tabpanel" aria-labelledby="issue-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex gap-2">
                            @if(Auth::guard('user')->user()->user_type_id == 1 ||
                                Auth::guard('user')->user()->user_type_id == 2)
                                <div style="margin-top:18px">
                                    <button type="button" class="btn btn-success" 
                                        data-bs-toggle="modal" data-bs-target="#createIssueAgriculture">
                                        Create New Agriculture Issue	
                                    </button>
                                    @include('agriculture.issue.create-issue')
                                </div>
                            @endif
                        </div>
                    </div>
                    <table id="agricultureIssuesTable" class="table table-striped my-2 data-table-issues">
                        <thead>
                            <tr>
                                <th class="text-center">Issue (English)</th>
                                <th class="text-center">Issue (Arabic)</th>
                                <th class="text-center">Action</th>
                                <th class="text-center">Action Category</th>
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

@include('request.water.show')
<script>
 
    $('#exportFormWaterHolder').on('submit', function (event) {

        event.preventDefault(); 

        let valid = true;

        var fileTypeValue = $('#fileType').val();

        if (fileTypeValue == null) {

            $('#file_type_error').html('Please select a type!'); 
            return false;
        } else  if (fileTypeValue != null) {

            $('#file_type_error').empty();
        }

        $('#file_type_error').empty();

        if (valid) {

            $(this).addClass('was-validated');
            this.submit(); 
        }
    });



    // Clear Filters for Export
    $('#clearAllWaterFiltersButton').on('click', function() {

        $('.selectpicker').prop('selectedIndex', 0);
        $('.selectpicker').selectpicker('refresh');
    });

    $(function () {

        // keep track of initialized tables
        var tables = {};

        function initActionTable() {

            if (tables.action) return;
            tables.action = $('.data-table-action').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('agriculture-action.index') }}",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.action_filter = $('#filterByAction').val();
                        d.category_filter = $('#filterByCategory').val();
                    }
                }, 
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'arabic_name', name: 'arabic_name'},
                    {data: 'category', name: 'category'},
                    {data: 'action'}
                ]
            });
        }

        function initIssueTable() {

            if (tables.issue) return;
            tables.issue = $('.data-table-issues').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('agriculture-issue.index') }}",
                    data: function (d) {
                        d.search = (d.search && d.search.value) ? d.search.value : '';
                        d.action_filter = $('#filterByAction').val();
                        d.category_filter = $('#filterByCategory').val();
                    }
                },
                columns: [
                    {data: 'english_name', name: 'english_name'},
                    {data: 'arabic_name', name: 'arabic_name'},
                    {data: 'agriculture_action', name: 'agriculture_action'},
                    {data: 'category', name: 'category'},
                    { data: 'action', orderable: false, searchable: false }
                ]
            });
        }


        initActionTable();

        // On tab shown, lazy-init the target table
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target).attr('href');
            if (target == '#agriculture-issues') initIssueTable();
            if (target == '#agriculture-actions') initActionTable();


            if ($('.selectpicker').length && typeof $('.selectpicker').selectpicker === 'function') {

                $('.selectpicker').selectpicker('refresh');
            }
        });


        // Reload initialized tables when any filter changes
        function reloadInitializedTables() {

            if (tables.action) tables.action.ajax.reload();
            if (tables.issue) tables.issue.ajax.reload();
        }

        $('#filterByAction, #filterByCategory').on('change', function () {

            if (tables.issue) tables.issue.ajax.reload();
            if (tables.action) tables.action.ajax.reload();
        });

        // Clear filters
        $(document).on('click', '#clearFiltersButton', function () {

            $('#filterByAction').prop('selectedIndex', 0);
            $('#filterByCategory').prop('selectedIndex', 0);
            $('.selectpicker').selectpicker('refresh');
            if (tables.action) tables.action.ajax.reload();
            if (tables.issue) tables.issue.ajax.reload();
        });


        // Delete record for the action
        $('#agricultrueActionsTable').on('click', '.deleteAgricultrueAction',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this Action?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteAgricultrueAction') }}",
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
                                    $('#agricultrueActionsTable').DataTable().draw();
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

        // Delete record for the issue
        $('#agricultureIssuesTable').on('click', '.deleteAgricultureIssue',function() {
            var id = $(this).data('id');

            Swal.fire({
                icon: 'warning',
                title: 'Are you sure you want to delete this Issue?',
                showDenyButton: true,
                confirmButtonText: 'Confirm'
            }).then((result) => {

                if(result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('deleteAgricultureIssue') }}",
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
                                    $('#agricultureIssuesTable').DataTable().draw();
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
  
        // View update form for action
        $('#agricultrueActionsTable').on('click', '.updateAgricultureAction',function() {

            var id = $(this).data('id');
            var url = "{{ url('agriculture-action') }}/" + id + "/edit";
            window.location.href = url;
        });

        // View update form for issue
        $('#agricultureIssuesTable').on('click', '.updateAgricultureIssue',function() {

            var id = $(this).data('id');
            var url = "{{ url('agriculture-issue') }}/" + id + "/edit";
            window.location.href = url;
        });
      
    });
</script>

@endsection