@extends('layouts/layoutMaster')

@section('title', 'edit internet system')

@include('layouts.all')

@section('content')
<div class="container">
    <h2>Create Internet System Return</h2>

    <form id="returnForm" method="POST" action="{{ route('internet.returns.store') }}">
        @csrf

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
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

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Returned Items</h5>

                <div id="itemsWrapper">
                    <div class="return-item row align-items-end mb-2" data-index="0">
                        <div class="col-md-3">
                            <label>Component Type</label>
                            <select name="items[0][component_type]" class="form-control component-type">
                                <option value="">-- Select type --</option>
                                <option value="App\Models\Router">Router</option>
                                <option value="App\Models\Switche">Switch</option>
                                <option value="App\Models\PatchPanel">Patch Panel</option>
                                <option value="App\Models\PatchCord">Patch Cord</option>
                                <option value="App\Models\NetworkCabinet">Network Cabinet</option>
                                <option value="App\Models\InternetConnector">Connector</option>
                                <option value="App\Models\Camera">Camera</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Component</label>
                            <select name="items[0][component_id]" class="form-control component-id selectpicker" data-live-search="true">
                                <option value="">-- Select component --</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Serial / Tag</label>
                            <input type="text" name="items[0][component_serial]" class="form-control" />
                        </div>

                        <div class="col-md-1">
                            <label>Qty</label>
                            <input type="number" name="items[0][quantity]" class="form-control" value="1" min="1" />
                        </div>

                        <div class="col-md-2">
                            <label>Condition</label>
                            <select name="items[0][condition]" class="form-control">
                                <option value="1">Good</option>
                                <option value="2">Damaged</option>
                                <option value="3">Failed</option>
                            </select>
                        </div>

                        <div class="col-md-1 text-right">
                            <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top:32px;">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="mt-2">
                    <button id="addItem" type="button" class="btn btn-secondary">Add Item</button>
                </div>

            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Save Return</button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    // small helper: fetch components for a chosen type
async function fetchSystemsForCommunity(communityId) {
    if (!communityId) return [];

    if (systemsByCommunity && systemsByCommunity[communityId] && systemsByCommunity[communityId].length) {
        return systemsByCommunity[communityId];
    }

    const url = '/api/internet-systems?community_id=' + encodeURIComponent(communityId);
    const res = await fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    });

    if (!res.ok) {
        throw new Error('Failed to fetch systems');
    }

    return await res.json();
}

    // fetch internet systems for a given community
    // preloaded systems by community from server (fallback to API if empty)
    const systemsByCommunity = {!! json_encode($systemsByCommunity ?? []) !!};

    async function fetchSystemsForCommunity(communityId) {
        if (!communityId) return [];
        // prefer preloaded mapping
        if (systemsByCommunity && systemsByCommunity[communityId]) {
            return systemsByCommunity[communityId];
        }
        // fallback to ajax endpoint
        const url = '/api/internet-systems?community_id=' + encodeURIComponent(communityId);
        const res = await fetch(url);
        if (!res.ok) return [];
        return res.json(); // expect array of {id, label}
    }

    function makeItem(index) {
        return `
        <div class="return-item row align-items-end mb-2" data-index="${index}">
            <div class="col-md-3">
                <label>Component Type</label>
                <select name="items[${index}][component_type]" class="form-control component-type">
                    <option value="">-- Select type --</option>
                    <option value="App\\\\Models\\\\Router">Router</option>
                    <option value="App\\\\Models\\\\Switche">Switch</option>
                    <option value="App\\\\Models\\\\PatchPanel">Patch Panel</option>
                    <option value="App\\\\Models\\\\PatchCord">Patch Cord</option>
                    <option value="App\\\\Models\\\\NetworkCabinet">Network Cabinet</option>
                    <option value="App\\\\Models\\\\InternetConnector">Connector</option>
                    <option value="App\\\\Models\\\\Camera">Camera</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Component</label>
                <select name="items[${index}][component_id]" class="form-control component-id selectpicker" data-live-search="true">
                    <option value="">-- Select component --</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Serial / Tag</label>
                <input type="text" name="items[${index}][component_serial]" class="form-control" />
            </div>
            <div class="col-md-1">
                <label>Qty</label>
                <input type="number" name="items[${index}][quantity]" class="form-control" value="1" min="1" />
            </div>
            <div class="col-md-2">
                <label>Condition</label>
                <select name="items[${index}][condition]" class="form-control">
                    <option value="1">Good</option>
                    <option value="2">Damaged</option>
                    <option value="3">Failed</option>
                </select>
            </div>
            <div class="col-md-1 text-right">
                <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top:32px;">&times;</button>
            </div>
        </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', function(){
        let nextIndex = 1;

        document.getElementById('addItem').addEventListener('click', function(){
            const wrapper = document.getElementById('itemsWrapper');
            wrapper.insertAdjacentHTML('beforeend', makeItem(nextIndex));
            nextIndex++;
        });

        // delegate remove
        document.getElementById('itemsWrapper').addEventListener('click', function(e){
            if (e.target.matches('.remove-item')) {
                const row = e.target.closest('.return-item');
                if (row) row.remove();
            }
        });

        // delegate change of component-type to load components
        document.getElementById('itemsWrapper').addEventListener('change', async function(e){
            if (e.target.matches('.component-type')) {
                const row = e.target.closest('.return-item');
                const select = row.querySelector('.component-id');
                const type = e.target.value;
                // clear current options
                select.innerHTML = '<option value="">-- Loading... --</option>';
                const items = await fetchComponentsFor(type);
                select.innerHTML = '<option value="">-- Select component --</option>' + items.map(it => `<option value="${it.id}">${it.label}</option>`).join('');
                // if using selectpicker: refresh (safe check)
                if (window.jQuery && $(select).length && typeof $(select).selectpicker === 'function') $(select).selectpicker('refresh');
            }
        });

        // when community changes, load related internet systems
        const communitySelect = document.getElementById('internet_system_community_id');
        const systemSelect = document.getElementById('internet_system_id');
        // helper to set enabled/disabled state for the Internet System select
        function updateSystemEnabledState() {
            if (!systemSelect || !communitySelect) return;
            const shouldDisable = !(communitySelect.value && communitySelect.value !== '');
            if (window.jQuery && $(systemSelect).length && typeof $(systemSelect).selectpicker === 'function') {
                $(systemSelect).prop('disabled', shouldDisable);
                try { $(systemSelect).selectpicker('refresh'); } catch (e) {}
            } else {
                systemSelect.disabled = shouldDisable;
            }
        }

        // run once now and also after a short delay and on load to account for selectpicker initialization timing
        updateSystemEnabledState();
        setTimeout(updateSystemEnabledState, 120);
        window.addEventListener('load', updateSystemEnabledState);

        if (communitySelect) {
            communitySelect.addEventListener('change', async function (e) {
                const cid = e.target.value;

                if (!systemSelect) return;

                // If no community choosen
                if (!cid) {
                    systemSelect.innerHTML = '<option value="">-- Select system --</option>';
                    systemSelect.disabled = true;

                    if (window.jQuery && typeof $(systemSelect).selectpicker === 'function') {
                        $(systemSelect).prop('disabled', true);
                        $(systemSelect).selectpicker('refresh');
                    }

                    // clear returned items
                    const wrapper = document.getElementById('itemsWrapper');
                    wrapper.querySelectorAll('.return-item').forEach((node, idx) => {
                        if (idx > 0) node.remove();
                    });
                    wrapper.querySelectorAll('.return-item').forEach((node) => {
                        const typeSel = node.querySelector('.component-type');
                        const compSel = node.querySelector('.component-id');
                        const serial = node.querySelector('input[name^="items"][name$="[component_serial]"]');
                        const qty = node.querySelector('input[name^="items"][name$="[quantity]"]');
                        if (typeSel) typeSel.value = '';
                        if (compSel) {
                            compSel.innerHTML = '<option value="">-- Select component --</option>';
                            if (window.jQuery && $(compSel).length && typeof $(compSel).selectpicker === 'function') $(compSel).selectpicker('refresh');
                        }
                        if (serial) serial.value = '';
                        if (qty) qty.value = 1;
                    });

                    return;
                }

                // while loading 
                systemSelect.innerHTML = '<option value="">-- Loading systems... --</option>';
                systemSelect.disabled = true;

                if (window.jQuery && typeof $(systemSelect).selectpicker === 'function') {
                    $(systemSelect).prop('disabled', true);
                    $(systemSelect).selectpicker('refresh');
                }

                try {
                    const systems = await fetchSystemsForCommunity(cid);

                    systemSelect.innerHTML =
                        '<option value="">-- Select system --</option>' +
                        systems.map(s =>
                            `<option value="${s.id}">${s.label ?? s.system_name ?? s.name ?? s.id}</option>`
                        ).join('');

                    systemSelect.disabled = false;

                    if (window.jQuery && typeof $(systemSelect).selectpicker === 'function') {
                        $(systemSelect).prop('disabled', false);
                        $(systemSelect).selectpicker('refresh');
                    }

                    // clear existing returned items' component selections when changing community/system
                    const wrapper = document.getElementById('itemsWrapper');
                    // remove extra rows (keep only first)
                    wrapper.querySelectorAll('.return-item').forEach((node, idx) => {
                        if (idx > 0) node.remove();
                    });
                    // clear component-type and component-id for remaining rows
                    wrapper.querySelectorAll('.return-item').forEach((node) => {
                        const typeSel = node.querySelector('.component-type');
                        const compSel = node.querySelector('.component-id');
                        const serial = node.querySelector('input[name^="items"][name$="[component_serial]"]');
                        const qty = node.querySelector('input[name^="items"][name$="[quantity]"]');
                        if (typeSel) typeSel.value = '';
                        if (compSel) {
                            compSel.innerHTML = '<option value="">-- Select component --</option>';
                            if (window.jQuery && $(compSel).length && typeof $(compSel).selectpicker === 'function') $(compSel).selectpicker('refresh');
                        }
                        if (serial) serial.value = '';
                        if (qty) qty.value = 1;
                    });

                } catch (error) {
                    console.error('Error loading systems:', error);

                    systemSelect.innerHTML = '<option value="">-- Failed to load systems --</option>';
                    systemSelect.disabled = true;

                    if (window.jQuery && typeof $(systemSelect).selectpicker === 'function') {
                        $(systemSelect).prop('disabled', true);
                        $(systemSelect).selectpicker('refresh');
                    }
                }
            });
        }

        // when internet system changes, refresh all component selects so they are filtered by the selected system
        if (systemSelect) {
            systemSelect.addEventListener('change', async function(e){
                const sysId = e.target.value;
                const wrapper = document.getElementById('itemsWrapper');
                const rows = Array.from(wrapper.querySelectorAll('.return-item'));
                for (const row of rows) {
                    const typeSel = row.querySelector('.component-type');
                    const compSel = row.querySelector('.component-id');
                    if (!compSel) continue;
                    if (typeSel && typeSel.value) {
                        compSel.innerHTML = '<option value="">-- Loading... --</option>';
                        const items = await fetchComponentsFor(typeSel.value);
                        compSel.innerHTML = '<option value="">-- Select component --</option>' + items.map(it => `<option value="${it.id}">${it.label}</option>`).join('');
                    } else {
                        compSel.innerHTML = '<option value="">-- Select component --</option>';
                    }
                    if (window.jQuery && $(compSel).length && typeof $(compSel).selectpicker === 'function') $(compSel).selectpicker('refresh');
                }
            });
        }
    });
</script>
@endsection
