@extends('layouts/layoutMaster')

@section('title', 'All Cameras')

@include('layouts.all')

@section('content')
  <style>
    label,input{display:block}
    label,table{margin-top:20px}
    .compound-muted{color:#b0b0b0;font-size:.85em}
    td.details-control{text-align:center;width:48px}
    .summary-expand{padding:.15rem .35rem;font-size:.85rem}
    .served-bar{
      background:#f8fafc;border-radius:6px;border:1px solid rgba(31,45,61,.06);
      padding:.75rem 1rem;display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem
    }
    .served-bar .label{color:#2b2f33;font-weight:600}
    .served-bar .muted{color:#7f8a8d;font-size:.85rem}
    .served-bar .metric{min-width:140px;text-align:right}
    .served-bar .metric .num{font-size:1.25rem;font-weight:700;color:#1f2d3d}
    .served-bar .metric .lbl{color:#586069;font-size:.85rem}
  </style>

  @if(session()->has('message'))
    <div class="row">
      <div class="alert alert-success">{{ session()->get('message') }}</div>
    </div>
  @endif

  <p>
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target=".multi-collapse"
      aria-expanded="false" aria-controls="collapseCommunityCameraExport">
      <i class="bx bx-export"></i>
      Export Data
    </button>

    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target=".multi-collapse"
      aria-expanded="false" aria-controls="collapseCommunityCameraExport">
      <i class="bx bx-expand-alt"></i>
      Toggle All
    </button>
  </p>

  <div class="collapse multi-collapse container mb-4" id="collapseCommunityCameraExport">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="row">
              <div class="col-xl-10 col-lg-10 col-md-10">
                <h5>
                  Export Installed Cameras Report
                  <i class='fa-solid fa-file-excel text-info'></i>
                </h5>
              </div>
              <div class="col-xl-2 col-lg-2 col-md-2">
                <fieldset class="form-group">
                  <button class="" id="clearCameraFiltersButton" type="button">
                    <i class='fa-solid fa-eraser'></i>
                    Clear Filters
                  </button>
                </fieldset>
              </div>
            </div>
          </div>

          <form id="collapseExportForm" method="POST" enctype='multipart/form-data' action="{{ route('camera.export') }}">
            @csrf
            <div class="card-body">
              <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3">
                  <fieldset class="form-group">
                    <select id="exportTypeCollapse" class="selectpicker form-control" data-live-search="true">
                      <option value="" disabled selected>Choose export type</option>
                      <option value="{{ route('camera.export') }}">All Installed Cameras</option>
                      <option value="{{ route('camera-additions.export') }}">Additions Cameras</option>
                      <option value="{{ route('replacements.export') }}">Replacements Cameras</option>
                    </select>
                  </fieldset>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3">
                  <fieldset class="form-group">
                    <select class="selectpicker form-control" data-live-search="true" name="sub_region" required>
                      <option disabled selected>Choose Sub Region...</option>
                      @foreach($subRegions as $subRegion)
                        <option value="{{$subRegion->id}}">{{$subRegion->english_name}}</option>
                      @endforeach
                    </select>
                  </fieldset>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3">
                  <fieldset class="form-group">
                    <select name="community" class="selectpicker form-control" data-live-search="true">
                      <option disabled selected>Search Community</option>
                      @foreach($communities as $community)
                        <option value="{{$community->id}}">{{$community->english_name}}</option>
                      @endforeach
                    </select>
                  </fieldset>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3">
                  <fieldset class="form-group">
                    <input type="date" name="date" id="installationDate" class="form-control" title="Installation Data from">
                  </fieldset>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 d-none" id="incidentTypeExportCollapseDiv">
                  <fieldset class="form-group">
                    <select id="exportReplacementIncidentType" name="incident_type" class="selectpicker form-control" data-live-search="true">
                      <option value="" disabled selected>Choose Incident Type...</option>
                      @foreach($cameraReplacementIncidents as $incident)
                        <option value="{{ $incident->id }}">
                          {{ $incident->english_name ?? $incident->name ?? $incident->id }}
                        </option>
                      @endforeach
                    </select>
                  </fieldset>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-3 mt-3">
                  <fieldset class="form-group">
                    <button id="collapseExportButton" class="btn btn-info" type="button">
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
    All <span class="text-muted fw-light">Cameras</span>
  </h4>

  <div class="container card m-3">
    @php
      $blockedTypes = [7, 8, 9, 11, 14, 12];
      $currentType = Auth::guard('user')->user()->user_type_id ?? null;
      $isAllowed = !in_array($currentType, $blockedTypes);

      $summaryData = $summaryData ?? [];
      $summaryData['total_installed'] = $summaryData['total_installed'] ?? 0;
      $summaryData['total_replaced']  = $summaryData['total_replaced']  ?? 0;
      $summaryData['total_added']     = $summaryData['total_added']     ?? 0;
      $summaryData['total_returned']  = $summaryData['total_returned']  ?? 0;
      $summaryData['total_damaged']   = $summaryData['total_damaged']   ?? 0;
      $summaryData['total_current']   = $summaryData['total_current']   ?? 0;
      $summaryByRegion = $summaryByRegion ?? [];
    @endphp

    <div class="row">
      <div class="col-md-4">
        <label>Filter by Region</label>
        <select id="filterByRegionInstalled" class="selectpicker form-control" data-live-search="true">
          <option disabled selected>Choose Sub Region</option>
          @foreach($regions as $region)
            <option value="{{ $region->id }}">{{ $region->english_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label>Filter by Community</label>
        <select id="filterByCommunityInstalled" class="selectpicker form-control" data-live-search="true">
          <option disabled selected>Choose Community</option>
          @foreach($communities as $community)
            <option value="{{ $community->id }}">{{ $community->english_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-4">
        <label id="filterDateLabel">Installation date from</label>
        <input type="date" id="filterByDateInstalled" class="form-control">
      </div>

      <div class="col-xl-3 col-lg-3 col-md-3 mt-2">
        <fieldset class="form-group">
          <label class='col-md-12 control-label'>Clear All Filters</label>
          <button class="btn btn-dark" id="clearFiltersInstalledButton" type="button">
            <i class='fa-solid fa-eraser'></i>
            Clear Filters
          </button>
        </fieldset>
      </div>
    </div>

    <ul class="nav nav-tabs m-3" id="cameraTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab">
          <i class="fa-solid fa-file-lines me-1"></i> Summary
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="installed-tab" data-bs-toggle="tab" data-bs-target="#installed" type="button" role="tab">
          <i class="fa-solid fa-camera me-1"></i> Installed
        </button>
      </li>

      <!--<li class="nav-item" role="presentation">-->
      <!--  <button class="nav-link" id="additions-tab" data-bs-toggle="tab" data-bs-target="#additions" type="button" role="tab">-->
      <!--    <i class="fa-solid fa-plus me-1"></i> Additions-->
      <!--  </button>-->
      <!--</li>-->

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="replacement-tab" data-bs-toggle="tab" data-bs-target="#replacement" type="button" role="tab">
          <i class="fa-solid fa-repeat me-1"></i> Replacements
        </button>
      </li>

      <li class="nav-item" role="presentation">
        <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
          <i class="fa-solid fa-rotate-left me-1"></i> Removed
        </button>
      </li>
    </ul>

    <div class="tab-content" id="cameraTabsContent">

      <div class="tab-pane fade show active" id="summary" role="tabpanel" aria-labelledby="summary-tab">
        <div class="card-body">
          <h5 class="card-title">Summary of All Cameras</h5>

          <div class="served-bar mb-3">
            <div>
              <div class="label">Served Summary</div>
              <div class="muted">Overview of communities and repository coverage</div>
            </div>

            <div style="display:flex;gap:1rem;align-items:center;">
              <div class="metric">
                <div class="num">
                  <i class="fa-solid fa-users" style="margin-right:.5rem;color:#2b7a78;"></i>
                  {{ $summaryData['total_served_communities'] ?? 0 }}
                </div>
                <div class="lbl">Served communities</div>
              </div>

              <div class="metric">
                <div class="num">
                  <i class="fa-solid fa-video" style="margin-right:.5rem;color:#1659a8;"></i>
                  {{ max(0, ((int)($summaryData['total_current'] ?? $summaryData['total_cameras'] ?? 0) - (int)($summaryData['total_repository_cameras'] ?? 0))) }}
                </div>
                <div class="lbl">Total community cameras</div>
              </div>

              <div class="metric">
                <div class="num">
                  <i class="fa-solid fa-warehouse" style="margin-right:.5rem;color:#7a4bdb;"></i>
                  {{ $summaryData['total_served_repositories'] ?? 0 }}
                </div>
                <div class="lbl">Total repositories</div>
              </div>

              <div class="metric">
                <div class="num">
                  <i class="fa-solid fa-boxes-stacked" style="margin-right:.5rem;color:#cf6a32;"></i>
                  {{ $summaryData['total_repository_cameras'] ?? 0 }}
                </div>
                <div class="lbl">Repo cameras</div>
              </div>
            </div>
          </div>

          <div class="row text">
            <div class="col-md-6">
              <div class="card text-white bg-secondary mb-3 text-center">
                <div class="card-body">
                  <h5 class="card-title">Total Current Cameras</h5>
                  <p class="card-text display-2">{{ $summaryData['total_current'] ?? 0 }}</p>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card text-white bg-primary mb-3 text-center">
                <div class="card-body">
                  <h5 class="card-title">Total Installed Ever</h5>
                  <p class="card-text display-2">
                    {{ ($summaryData['total_current'] ?? 0) + ($summaryData['total_damaged'] ?? 0) + ($summaryData['total_returned'] ?? 0) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="card text-white bg-warning mb-3 text-center">
                <div class="card-body">
                  <h5 class="card-title text-center">Total Replaced Cameras</h5>
                  <p class="card-text display-3 text-center">{{ $summaryData['total_replaced'] ?? 0 }}</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card text-white bg-dark mb-3 text-center">
                <div class="card-body">
                  <h5 class="card-title text-light">Total Damaged Cameras</h5>
                  <p class="card-text display-3">{{ $summaryData['total_damaged'] ?? 0 }}</p>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card text-white bg-danger mb-3 text-center">
                <div class="card-body">
                  <h5 class="card-title">Total Removed Cameras</h5>
                  <p class="card-text display-3">{{ $summaryData['total_returned'] ?? 0 }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-3">
            <div class="col-12">
              <table id="summaryTable" class="table table-striped" style="width:100%">
                <thead>
                  <tr>
                    <th></th>
                    <th>Region</th>
                    <th class="text-center">Installed</th>
                    <th class="text-center">Replaced</th>
                    <th class="text-center">Damaged</th>
                    <th class="text-center">Removed</th>
                    <th class="text-center">Current</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>

        </div>
      </div>

      <div class="tab-pane fade" id="installed" role="tabpanel" aria-labelledby="installed-tab">
        <div class="card-body">
          <div class="row">
            <div class="col-md-2">
              <button type="button" class="btn btn-success"
                @if($isAllowed) data-bs-toggle="modal" data-bs-target="#createCommunityCamera"
                @else disabled title="You do not have permission to create cameras" @endif>
                Create New Installed Camera
              </button>
            </div>
            @if($isAllowed)
              @include('services.camera.create')
            @endif

            <div class="col-md-2">
              <button type="button" class="btn btn-success"
                @if($isAllowed) data-bs-toggle="modal" data-bs-target="#createAddition"
                @else disabled title="You do not have permission to create cameras" @endif>
                Create New Added Camera
              </button>
            </div>
            @if($isAllowed)
              @include('services.camera.additions.create')
            @endif
          </div>

          <table id="installedCameraTable" class="table table-striped" style="width:100%">
            <thead>
              <tr>
                <th class="text-center">Community</th>
                <th class="text-center">Region</th>
                <th class="text-center">Responsible</th>
                <th class="text-center"># of Cameras</th>
                <th class="text-center">Installation date</th>
                <th class="text-center">Options</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="replacement" role="tabpanel" aria-labelledby="replacement-tab">
        <div class="my-2">
          <div class="card-body">
            <div>
              <button type="button" class="btn btn-success"
                @if($isAllowed) data-bs-toggle="modal" data-bs-target="#createReplacement"
                @else disabled title="You do not have permission to create cameras" @endif>
                Create New Replaced Camera
              </button>
            </div>

            @if($isAllowed)
              @include('services.camera.replacements.create')
            @endif

            <table id="replacementTableAll" class="table table-striped">
              <thead>
                <tr>
                  <th>Community</th>
                  <th>Date</th>
                  <th># Damaged</th>
                  <th># New</th>
                  <th>Camera Type</th>
                  <th>NVR</th>
                  <th># NVRs</th>
                  <th>Incident Type</th>
                  <th>Donors</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="additions" role="tabpanel" aria-labelledby="additions-tab">
        <div class="my-2">
          <div class="card-body">
            <table id="additionTableAll" class="table table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>Community</th>
                  <th>Date</th>
                  <th># Cameras</th>
                  <th>SD Card Number</th>
                  <th># NVRs</th>
                  <th>Donors</th>
                  <th>Notes</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="tab-pane fade" id="returned" role="tabpanel" aria-labelledby="returned-tab">
        <div class="my-2">
          <div class="card-body">
            <div>
              <button type="button" class="btn btn-success"
                @if($isAllowed) data-bs-toggle="modal" data-bs-target="#createReturned"
                @else disabled title="You do not have permission to create returned records" @endif>
                Create Removed Camera Record
              </button>
            </div>

            @if($isAllowed)
              @include('services.camera.returned.create')
            @endif

            <table id="returnedTableAll" class="table table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>Community</th>
                  <th>Repository</th>
                  <th>Date</th>
                  <th># Cameras</th>
                  <th>Camera Type</th>
                  <th>NVR</th>
                  <th># NVRs</th>
                  <th>Notes</th>
                  <th>Options</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>

          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    (function () {
      let tableInstalled = null;
      let tableReplacement = null;
      let tableAddition = null;
      let tableReturned = null;
      let tableSummary = null;

      function ajaxErrorHandler(xhr, textStatus, errorThrown) {
        console.error('AJAX error:', textStatus, errorThrown);
        if (xhr && xhr.responseText) {
          console.error('Server response:', xhr.responseText);
          try {
            const json = JSON.parse(xhr.responseText);
            Swal.fire('Error', json.message || json.error || 'Server error', 'error');
          } catch (e) {
            Swal.fire('Error', 'Server error: see console for details', 'error');
          }
        } else {
          Swal.fire('Error', 'AJAX error: ' + textStatus, 'error');
        }
      }

      function initSummary() {
        if ($.fn.DataTable.isDataTable('#summaryTable')) {
          $('#summaryTable').DataTable().clear().destroy();
        }

        let summaryRows = @json($summaryByRegion ?? []);
        if (!Array.isArray(summaryRows) || summaryRows.length === 0) {
          summaryRows = [{
            region: 'All',
            total_installed: @json($summaryData['total_installed'] ?? 0),
            total_replaced:  @json($summaryData['total_replaced'] ?? 0),
            total_added:     @json($summaryData['total_added'] ?? 0),
            total_damaged:   @json($summaryData['total_damaged'] ?? 0),
            total_returned:  @json($summaryData['total_returned'] ?? 0),
            total_current:   @json($summaryData['total_current'] ?? 0)
          }];
        }

        tableSummary = $('#summaryTable').DataTable({
          data: summaryRows,
          columns: [
            { className: 'details-control', orderable: false, data: null, defaultContent: '<button type="button" class="btn btn-sm btn-outline-primary summary-expand">+</button>' },
            { data: 'region', name: 'region' },
            {
              data: null,
              name: 'total_installed',
              className: 'text-center',
              render: function (data, type, row) {
                var installed = Number(row.total_installed || 0);
                var added = Number(row.total_added || 0);
                var replaced = Number(row.total_replaced || 0);
                var damaged = Number(row.total_damaged || 0);
                return installed + added + replaced + damaged;
              }
            },
            { data: 'total_replaced', name: 'total_replaced', className: 'text-center' },
            { data: 'total_damaged', name: 'total_damaged', className: 'text-center' },
            { data: 'total_returned', name: 'total_returned', className: 'text-center' },
            { data: 'total_current', name: 'total_current', className: 'text-center' }
          ],
          paging: false,
          searching: false,
          info: false,
          ordering: false,
          autoWidth: false
        });

        $('#summaryTable tbody').off('click', 'button.summary-expand').on('click', 'button.summary-expand', function (e) {
          e.stopPropagation();
          var tr = $(this).closest('tr');
          var row = tableSummary.row(tr);

          if (row.child.isShown()) {
            var childTable = tr.next('tr').find('table.child-table');
            if (childTable.length) {
              var childId = childTable.attr('id');
              if ($.fn.DataTable.isDataTable('#' + childId)) {
                try { $('#' + childId).DataTable().destroy(); } catch (e) {}
              }
            }
            row.child.hide();
            tr.removeClass('shown');
            return;
          }

          var d = row.data();
          var uniqueId = 'child-' + (new Date()).getTime() + '-' + Math.floor(Math.random() * 1000);
          var html = '<table id="' + uniqueId + '" class="table table-sm child-table" style="width:100%">';
          html += '<thead><tr><th>Community</th><th class="text-center">Installed</th><th class="text-center">Replaced</th><th class="text-center">Damaged</th><th class="text-center">Removed</th><th class="text-center">Current</th></tr></thead>';
          html += '<tbody></tbody></table>';

          var childData = Array.isArray(d.communities) ? d.communities : [];
          var filteredData = childData.filter(function (c) {
            var installed = Number(c.total_installed || 0);
            var replaced = Number(c.total_replaced || 0);
            var damaged = Number(c.total_damaged || 0);
            var returned = Number(c.total_returned || 0);
            var current = Number(c.total_current || 0);
            return (installed + replaced + damaged + returned + current) > 0;
          });

          if (filteredData.length === 0) {
            row.child('<div class="p-2">No communities with non-zero totals.</div>').show();
            tr.addClass('shown');
            return;
          }

          row.child(html).show();
          tr.addClass('shown');

          try {
            $('#' + uniqueId).DataTable({
              data: filteredData,
              columns: [
                { data: 'community', name: 'community' },
                {
                  data: null,
                  name: 'total_installed',
                  className: 'text-center',
                  render: function (data, type, row) {
                    var installed = Number(row.total_installed || 0);
                    var added = Number(row.total_added || 0);
                    var replaced = Number(row.total_replaced || 0);
                    var damaged = Number(row.total_damaged || 0);
                    return installed + added + replaced + damaged;
                  }
                },
                { data: 'total_replaced', name: 'total_replaced', className: 'text-center' },
                { data: 'total_damaged', name: 'total_damaged', className: 'text-center' },
                { data: 'total_returned', name: 'total_returned', className: 'text-center' },
                { data: 'total_current', name: 'total_current', className: 'text-center' }
              ],
              paging: false,
              searching: false,
              info: false,
              ordering: false,
              autoWidth: false
            });
          } catch (e) {
            console.error('Failed to init child DataTable', e);
          }
        });
      }

      function initInstalledTable() {
        if ($.fn.DataTable.isDataTable('#installedCameraTable')) {
          $('#installedCameraTable').DataTable().clear().destroy();
        }

        tableInstalled = $('#installedCameraTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('camera.index') }}",
            data: function (d) {
              d.community_filter = $('#filterByCommunityInstalled').val();
              d.region_filter = $('#filterByRegionInstalled').val();
              d.date_filter = $('#filterByDateInstalled').val();
            },
            error: ajaxErrorHandler
          },
          columns: [
            {
              data: null, name: 'community', orderable: false, searchable: false,
              render: function (data, type, row) {
                const community = row.name || row.community || '';
                const compound = row.compound || '';
                return '<div>' + community + (compound ? '<div class="compound-muted">' + compound + '</div>' : '') + '</div>';
              }
            },
            { data: 'region', name: 'region' },
            { data: 'english_name', name: 'english_name' },
            { data: 'total_current', name: 'total_current' },
            { data: 'installation_date', name: 'installation_date' },
            { data: 'action' }
          ]
        });
      }

      function initReplacementTable() {
        if ($.fn.DataTable.isDataTable('#replacementTableAll')) {
          $('#replacementTableAll').DataTable().clear().destroy();
        }

        tableReplacement = $('#replacementTableAll').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('replacement.index') }}",
            data: function (d) {
              d.region_filter = $('#filterByRegionInstalled').val();
              d.community_filter = $('#filterByCommunityInstalled').val();
              d.date_filter = $('#filterByDateInstalled').val();
            },
            error: ajaxErrorHandler
          },
          columns: [
            {
              data: null, name: 'community', orderable: false, searchable: false,
              render: function (data, type, row) {
                const community = row.community || row.name || '';
                const compound = row.compound || '';
                return '<div>' + community + (compound ? '<div class="compound-muted">' + compound + '</div>' : '') + '</div>';
              }
            },
            { data: 'date_of_replacement', name: 'date_of_replacement' },
            { data: 'damaged_camera_count', name: 'damaged_camera_count' },
            { data: 'new_camera_count', name: 'new_camera_count' },
            { data: 'camera_type', name: 'camera_type' },
            { data: 'nvr', name: 'nvr' },
            { data: 'number_of_nvr', name: 'number_of_nvr' },
            { data: 'incident_type', name: 'incident_type' },
            { data: 'donors', name: 'donors' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
        });
      }

      function initAdditionTable() {
        if ($.fn.DataTable.isDataTable('#additionTableAll')) {
          $('#additionTableAll').DataTable().clear().destroy();
        }

        tableAddition = $('#additionTableAll').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('camera-additions.index') }}",
            data: function (d) {
              d.region_filter = $('#filterByRegionInstalled').val();
              d.community_filter = $('#filterByCommunityInstalled').val();
              d.date_filter = $('#filterByDateInstalled').val();
            },
            error: ajaxErrorHandler
          },
          columns: [
            {
              data: null, name: 'community', orderable: false, searchable: false,
              render: function (data, type, row) {
                const community = row.community || row.name || '';
                const compound = row.compound || '';
                return '<div>' + community + (compound ? '<div class="compound-muted">' + compound + '</div>' : '') + '</div>';
              }
            },
            { data: 'date_of_addition', name: 'date_of_addition' },
            { data: 'number_of_cameras', name: 'number_of_cameras' },
            { data: 'sd_card_number', name: 'sd_card_number' },
            { data: 'number_of_nvr', name: 'number_of_nvr' },
            { data: 'donors', name: 'donors' },
            { data: 'notes', name: 'notes' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
        });
      }

      function initReturnedTable() {
        if ($.fn.DataTable.isDataTable('#returnedTableAll')) {
          $('#returnedTableAll').DataTable().clear().destroy();
        }

        tableReturned = $('#returnedTableAll').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: "{{ route('camera-returned.index') }}",
            data: function (d) {
              d.region_filter = $('#filterByRegionInstalled').val();
              d.community_filter = $('#filterByCommunityInstalled').val();
              d.date_filter = $('#filterByDateInstalled').val();
            },
            error: ajaxErrorHandler
          },
          columns: [
            {
              data: null, name: 'community', orderable: false, searchable: false,
              render: function (data, type, row) {
                const community = row.community || row.name || '';
                const compound = row.compound || '';
                return '<div>' + community + (compound ? '<div class="compound-muted">' + compound + '</div>' : '') + '</div>';
              }
            },
            { data: 'repository', name: 'repository' },
            { data: 'date', name: 'date' },
            { data: 'number_of_cameras', name: 'number_of_cameras' },
            { data: 'camera_type', name: 'camera_type' },
            { data: 'nvr', name: 'nvr' },
            { data: 'number_of_nvr', name: 'number_of_nvr' },
            { data: 'notes', name: 'notes' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
          ]
        });
      }

      function reloadAllTables() {
        try {
          if ($.fn.DataTable.isDataTable('#installedCameraTable')) $('#installedCameraTable').DataTable().ajax.reload();
          if ($.fn.DataTable.isDataTable('#replacementTableAll')) $('#replacementTableAll').DataTable().ajax.reload();
          if ($.fn.DataTable.isDataTable('#additionTableAll')) $('#additionTableAll').DataTable().ajax.reload();
          if ($.fn.DataTable.isDataTable('#returnedTableAll')) $('#returnedTableAll').DataTable().ajax.reload();
        } catch (err) {
          console.error('Error reloading tables', err);
        }
      }

      $(function () {
        initInstalledTable();
        initSummary();

        $('#exportTypeCollapse').on('change', function () {
          var selected = $(this).val();
          var replacementsRoute = '{{ route('replacements.export') }}';
          if (selected === replacementsRoute) {
            $('#incidentTypeExportCollapseDiv').removeClass('d-none');
          } else {
            $('#incidentTypeExportCollapseDiv').addClass('d-none');
            $('#exportReplacementIncidentType').prop('selectedIndex', 0);
          }
          try { $('.selectpicker').selectpicker('refresh'); } catch (e) {}
        });

        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
          const target = $(e.target).data('bs-target');

          if (target === '#replacement' && !$.fn.DataTable.isDataTable('#replacementTableAll')) initReplacementTable();
          if (target === '#additions' && !$.fn.DataTable.isDataTable('#additionTableAll')) initAdditionTable();
          if (target === '#returned' && !$.fn.DataTable.isDataTable('#returnedTableAll')) initReturnedTable();

          try {
            let labelText = 'Installation date from';
            if (target === '#replacement') labelText = 'Replacement Date from';
            else if (target === '#additions') labelText = 'Addition Date from';
            else if (target === '#returned') labelText = 'Removed Date from';
            $('#filterDateLabel').text(labelText);
          } catch (err) {}

          try { $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust(); } catch (err) {}

          try {
            const tabName = String(target || '').replace('#', '');
            if (tabName) {
              const u = new URL(window.location.href);
              u.searchParams.set('tab', tabName);
              history.replaceState(null, '', u.toString());
            }
          } catch (e) {}
        });

        $('#filterByRegionInstalled, #filterByCommunityInstalled, #filterByDateInstalled').on('change', function () {
          reloadAllTables();
        });

        $('#clearFiltersInstalledButton').on('click', function () {
          $('.selectpicker').prop('selectedIndex', 0);
          try { $('.selectpicker').selectpicker('refresh'); } catch (e) {}
          $('#filterByDateInstalled').val('');
          reloadAllTables();
        });

        $('#clearCameraFiltersButton').on('click', function (e) {
          if (e && e.preventDefault) e.preventDefault();
          $('.selectpicker').prop('selectedIndex', 0);
          try { $('.selectpicker').selectpicker('refresh'); } catch (e) {}
          $('#installationDate').val('');
          $('#exportTypeCollapse').prop('selectedIndex', 0);
          $('#incidentTypeExportCollapseDiv').addClass('d-none');
          try { $('#exportReplacementIncidentType').prop('selectedIndex', 0); } catch (e) {}
          reloadAllTables();
        });

        $('#collapseExportButton').on('click', function () {
          let url = $('#exportTypeCollapse').val();
          const isDefault = !url;

          if (isDefault) url = '{{ route('camera.export') }}';

          try { $('#collapseCommunityCameraExport').collapse('hide'); }
          catch (e) { $('#collapseCommunityCameraExport').hide(); }

          setTimeout(function () {
            if (isDefault) {
              if ($('#collapseExportForm input[name="download_default"]').length === 0) {
                $('<input>').attr({ type: 'hidden', name: 'download_default', value: '1' }).appendTo('#collapseExportForm');
              }
            }
            $('#collapseExportForm').attr('action', url).submit();
          }, 300);
        });

        $('#collapseExportForm').on('submit', function () {
          try { $('#collapseCommunityCameraExport').collapse('hide'); }
          catch (e) { $('#collapseCommunityCameraExport').hide(); }
        });
      });

      (function activateTabFromUrl() {
        try {
          const params = new URLSearchParams(window.location.search);
          let tab = params.get('tab');
          if (!tab && location.hash) tab = location.hash.replace('#','');
          if (!tab) return;

          const tabBtn = document.querySelector('#' + tab + '-tab');
          if (tabBtn) {
            try {
              var bsTab = new bootstrap.Tab(tabBtn);
              bsTab.show();
            } catch (e) {
              tabBtn.click();
            }

            setTimeout(function () {
              try {
                if (tab === 'returned') {
                  if ($.fn.DataTable.isDataTable('#returnedTableAll')) $('#returnedTableAll').DataTable().ajax.reload();
                  else initReturnedTable();
                }
                if (tab === 'replacement') {
                  if ($.fn.DataTable.isDataTable('#replacementTableAll')) $('#replacementTableAll').DataTable().ajax.reload();
                  else initReplacementTable();
                }
                if (tab === 'additions') {
                  if ($.fn.DataTable.isDataTable('#additionTableAll')) $('#additionTableAll').DataTable().ajax.reload();
                  else initAdditionTable();
                }
              } catch (e) {}
            }, 250);
          }
        } catch (e) {}
      })();

      $(document).on('click', '.updateCamera', function () {
        const id = $(this).data('id');
        window.location.href = "{{ url('replacements') }}/" + id + "/edit";
      });

      $('#installedCameraTable').on('click', '.viewCameraCommunityButton', function () {
        var id = $(this).data('id');
        window.open("{{ url('camera') }}/" + id);
      });

      $('#installedCameraTable').on('click', '.updateCameraCommunity', function () {
        var id = $(this).data('id');
        window.location.href = "{{ url('camera') }}/" + id + "/edit";
      });

      $('#installedCameraTable').on('click', '.deleteCameraCommunity', function () {
        var id = $(this).data('id');

        Swal.fire({
          icon: 'warning',
          title: 'Are you sure you want to delete this installed camera?',
          showDenyButton: true,
          confirmButtonText: 'Confirm',
          denyButtonText: 'No'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('deleteCameraCommunity') }}",
              type: 'get',
              data: { id: id },
              success: function (response) {
                if (response.success == 1) {
                  Swal.fire({
                    icon: 'success',
                    title: response.msg,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'Okay!'
                  }).then(() => {
                    $('#installedCameraTable').DataTable().draw();
                  });
                } else {
                  Swal.fire('Error', response.msg || 'Invalid ID', 'error');
                }
              },
              error: function () {
                Swal.fire('Error', 'An error occurred while deleting.', 'error');
              }
            });
          } else if (result.isDenied) {
            Swal.fire('Changes are not saved', '', 'info');
          }
        });
      });

      $('#replacementTableAll').on('click', '.deleteCamera', function () {
        var id = $(this).data('id');

        Swal.fire({
          icon: 'warning',
          title: 'Are you sure you want to delete this camera replacement?',
          showDenyButton: true,
          confirmButtonText: 'Confirm',
          denyButtonText: 'No'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('replacement.delete') }}",
              type: 'POST',
              data: { _token: "{{ csrf_token() }}", id: id },
              success: function (response) {
                if (response.success == 1) {
                  Swal.fire({
                    icon: 'success',
                    title: response.msg,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'Okay!'
                  }).then(() => {
                    $('#replacementTableAll').DataTable().draw();
                  });
                } else {
                  Swal.fire('Error', response.msg, 'error');
                }
              },
              error: function () {
                Swal.fire('Error', 'An error occurred while deleting.', 'error');
              }
            });
          } else if (result.isDenied) {
            Swal.fire('Changes are not saved', '', 'info');
          }
        });
      });

      $('#additionTableAll').on('click', '.updateAddition', function () {
        var id = $(this).data('id');
        window.location.href = "{{ url('camera-additions') }}/" + id + "/edit";
      });

      $('#additionTableAll').on('click', '.deleteAddition', function () {
        var id = $(this).data('id');

        Swal.fire({
          icon: 'warning',
          title: 'Are you sure you want to delete this camera addition?',
          showDenyButton: true,
          confirmButtonText: 'Confirm',
          denyButtonText: 'No'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('camera-additions.destroy') }}",
              type: 'POST',
              data: { _token: "{{ csrf_token() }}", id: id },
              success: function (response) {
                if (response.success == 1) {
                  Swal.fire({
                    icon: 'success',
                    title: response.msg,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'Okay!'
                  }).then(() => {
                    $('#additionTableAll').DataTable().draw();
                  });
                } else {
                  Swal.fire('Error', response.msg, 'error');
                }
              },
              error: function () {
                Swal.fire('Error', 'An error occurred while deleting.', 'error');
              }
            });
          } else if (result.isDenied) {
            Swal.fire('Changes are not saved', '', 'info');
          }
        });
      });

      $('#returnedTableAll').on('click', '.deleteReturned', function () {
        var id = $(this).data('id');

        Swal.fire({
          icon: 'warning',
          title: 'Are you sure you want to delete this returned record?',
          showDenyButton: true,
          confirmButtonText: 'Confirm',
          denyButtonText: 'No'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "{{ route('camera-returned.destroy') }}",
              type: 'POST',
              data: { _token: "{{ csrf_token() }}", id: id },
              success: function (response) {
                if (response.success == 1) {
                  Swal.fire({
                    icon: 'success',
                    title: response.msg,
                    showDenyButton: false,
                    showCancelButton: false,
                    confirmButtonText: 'Okay!'
                  }).then(() => {
                    $('#returnedTableAll').DataTable().draw();
                  });
                } else {
                  Swal.fire('Error', response.msg, 'error');
                }
              },
              error: function () {
                Swal.fire('Error', 'An error occurred while deleting.', 'error');
              }
            });
          } else if (result.isDenied) {
            Swal.fire('Changes are not saved', '', 'info');
          }
        });
      });

    })();
  </script>
@endsection
