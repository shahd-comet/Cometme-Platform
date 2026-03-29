<div class="container-fluid">
    <div class="row">
        <div class="col-12">
                <div class="card-body">

                    {{-- System Header --}}
                    @php
                        $totalItems = isset($components) ? ($components->sum('quantity') ?? 0) : 0;
                        $totalCost = isset($components) ? ($components->sum('total_price') ?? 0) : 0;
                        $totalWithVat = isset($components) ? ($components->sum('total_vet') ?? 0) : 0;
                    @endphp
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <h5 class="mb-1">{{ $system->name }}</h5>
                            <small class="text-muted">
                                {{ $system->description ? \Illuminate\Support\Str::limit($system->description, 180) : 'No description provided' }}
                            </small>
                        </div>
                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                            <span class="badge bg-info m-1">Total: {{ number_format($totalCost, 2) }} NIS</span>
                                <span class="badge bg-success m-1">Total w/ VAT: {{ number_format($totalWithVat, 2) }} NIS</span>

                        </div>
                    </div>

                    {{-- System Info --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Azolla Type:</strong>
                            <div>{{ $system->azollaType ? $system->azollaType->name : 'Not specified' }}</div>
                        </div>
                        <div class="col-md-6">
                            <strong>System Cycle:</strong>
                            <div>{{ $system->agricultureSystemCycle ? $system->agricultureSystemCycle->name : 'Not specified' }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Installation Year:</strong>
                            <div>{{ $system->installation_year ?? 'Not specified' }}</div>
                        </div>
                        <div class="col-md-4">
                            <strong>Status:</strong>
                            <div>
                                @if($system->is_archived)
                                    <span class="badge bg-danger">Archived</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </div>
                        </div>
                            <div class="col-md-4 text-md-end">
                                <button
                                    id="toggleComponentsBtn"
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary me-1 toggle-components-btn"
                                    aria-expanded="true"
                                    aria-controls="componentsCollapse">
                                    Show / Hide Components
                                </button>
                            </div>
                    </div>

                    {{-- Components Table --}}
                    <div class="collapse show" id="componentsCollapse">
                        <div class="card card-sm mb-3">
                            <div class="card-body p-2">
                                @if(isset($components) && $components->count())
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-striped align-middle mb-0">
                                            <thead class="table-light small">
                                                <tr>
                                                    <th style="width:40px;">#</th>
                                                    <th>Category</th>
                                                    <th>Component</th>
                                                    <th>Model / Specification</th>
                                                    <th style="width:80px;">Unit</th>
                                                    <th class="text-end" style="width:80px;">Qty</th>
                                                    <th class="text-end" style="width:120px;">Unit price</th>
                                                    <th class="text-end" style="width:120px;">Total</th>
                                                    <th class="text-end" style="width:120px;">Total w/ VET</th>
                                                </tr>
                                            </thead>
                                            <tbody class="small">
                                                @foreach($components as $c)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $c['category_name'] }}</td>
                                                        <td>{{ $c['component_name'] }}</td>
                                                        <td>
                                                            @if(!empty($c['model_name']))
                                                                <div><strong>{{ $c['model_name'] }}</strong></div>
                                                            @endif
                                                            @if(!empty($c['specification']))
                                                                <div class="text-muted small">{{ $c['specification'] }}</div>
                                                            @endif
                                                        </td>
                                                        <td>{{ $c['unit'] ?? '-' }}</td>
                                                        <td class="text-end">{{ $c['quantity'] }}</td>
                                                        <td class="text-end">{{ number_format($c['unit_price'], 2) }}</td>
                                                        <td class="text-end">{{ number_format($c['total_price'], 2) }}</td>
                                                        <td class="text-end">{{ number_format($c['total_vet'] ?? $c['total_price'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary">
                                                    <td colspan="5" class="text-end"><strong>Totals</strong></td>
                                                    <td class="text-end"><strong>{{ $totalItems }}</strong></td>
                                                    <td></td>
                                                    <td class="text-end"><strong>{{ number_format($totalCost, 2) }}</strong></td>
                                                    <td class="text-end"><strong>{{ number_format($totalWithVat, 2) }}</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="py-3 text-center text-muted">No components have been added to this system.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div> <!-- card-body -->
        </div> <!-- col-12 -->
    </div> <!-- row -->
</div> <!-- container-fluid -->

<script>
document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('toggleComponentsBtn');
    var collapseEl = document.getElementById('componentsCollapse');
    if (!btn || !collapseEl) return;

    var bsCollapse = null;
    if (typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
        if (typeof bootstrap.Collapse.getInstance === 'function') {
            bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
        }
        if (!bsCollapse) {
            try { bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: false }); } catch(e) { bsCollapse = null; }
        }
    }

    function updateBtn() {
        var isShown = collapseEl.classList.contains('show');
        btn.textContent = isShown ? 'Hide Components' : 'Show Components';
        btn.setAttribute('aria-expanded', isShown ? 'true' : 'false');
    }

    btn.addEventListener('click', function(e){
        e.preventDefault();
        if (bsCollapse) {
            if (collapseEl.classList.contains('show')) bsCollapse.hide();
            else bsCollapse.show();
        } else {
            collapseEl.classList.toggle('show');
            updateBtn();
        }
    });

    // listen to bootstrap collapse events when available
    if (collapseEl) {
        collapseEl.addEventListener('shown.bs.collapse', updateBtn);
        collapseEl.addEventListener('hidden.bs.collapse', updateBtn);
    }

    updateBtn();
});
</script>
