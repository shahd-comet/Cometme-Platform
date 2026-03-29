@extends('layouts/layoutMaster')



@section('title', 'Elc')



@include('layouts.all')



<style>

    label,

    input {

        display: block;

    }



    label {

        margin-top: 20px;

    }

</style>



@section('vendor-style')

@endsection



@section('content')

    <h4 class="py-3 breadcrumb-wrapper mb-4">

        <span class="text-muted fw-light">Add </span> New Elctr.

    </h4>



    <div class="card">

        <div class="card-content collapse show">

            <div class="card-body">

                <form method="POST" enctype='multipart/form-data' id="elecUserForm"

                    action="{{ url('progress-household') }}">

                    @csrf

                    <div class="row">

                        <div class="col-xl-6 col-lg-6 col-md-6">

                            <fieldset class="form-group">

                                <label>New/Old Community</label>

                                <select name="misc" id="selectedUserMisc" data-live-search="true"

                                    class="selectpicker form-control" required>

                                    <option disabled selected>Choose one...</option>

                                    @foreach($installationTypes as $installationType)

                                        <option value="{{ $installationType->id }}">{{ $installationType->type }}</option>

                                    @endforeach

                                </select>

                            </fieldset>

                            <div id="misc_error" style="color: red;"></div>

                        </div>



                        <div class="col-xl-6 col-lg-6 col-md-6">

                            <fieldset class="form-group">

                                <label>Community</label>

                                <select class="selectpicker form-control" data-live-search="true" name="community_id"

                                    id="selectedUserCommunity" required>

                                </select>

                            </fieldset>

                            <div id="community_id_error" style="color: red;"></div>

                        </div>



                        <div class="col-xl-6 col-lg-6 col-md-6">

                            <fieldset class="form-group">

                                <label>Compound (optional)</label>

                                <select class="selectpicker form-control" name="compound_id" id="selectedCompound"

                                    data-live-search="true">

                                    <option value="" selected>Choose one...</option>

                                </select>

                            </fieldset>

                        </div>

                    </div>



                    <div class="row">

                        <div class="col-xl-6 col-lg-6 col-md-6">

                            <fieldset class="form-group">

                                <label>Users</label>

                                <select name="household_id[]" id="selectedUserHousehold" class="selectpicker form-control"

                                    data-live-search="true" multiple disabled required>

                                    <option disabled selected>Choose one...</option>

                                </select>

                            </fieldset>

                            <div id="household_id_error" style="color: red;"></div>



                            

                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6">

                            <fieldset class="form-group">

                                <label>Was the meter added to the system?</label>

                                <div>

                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input" type="radio" name="meter_added" id="meter_added_yes" value="1">

                                        <p class="form-check-label" for="meter_added_yes">Yes</p>

                                    </div>

                                    <div class="form-check form-check-inline">

                                        <input class="form-check-input" type="radio" name="meter_added" id="meter_added_no" value="0">

                                        <p class="form-check-label" for="meter_added_no">No</p>

                                    </div>

                                </div>

                            </fieldset>

                            <div id="meter_added_error" style="color: red;"></div>

                        </div>



                        





                    </div>



                    <div class="row">

                        <div class="col-xl-6 col-lg-6 col-md-6 mb-1">

                            <fieldset class="form-group">

                                <label>Energy System Type</label>

                                <select name="energy_system_type_id" class="selectpicker form-control"

                                    id="selectedEnergySystemType" data-live-search="true" required>

                                    <option disabled selected>Choose one...</option>

                                    @foreach($energySystemTypes as $energySystemType)

                                        <option value="{{ $energySystemType->id }}">{{ $energySystemType->name }}</option>

                                    @endforeach

                                </select>

                            </fieldset>

                            <div id="energy_system_type_id_error" style="color: red;"></div>

                        </div>



                        <div class="col-xl-6 col-lg-6 col-md-6">

                            <fieldset class="form-group">

                                <label>Energy System</label>

                                <select name="energy_system_id" id="selectedEnergySystem" class="form-control" disabled

                                    required>

                                    <option disabled selected>Choose one...</option>

                                </select>

                            </fieldset>

                            <button type="button" class="btn btn-primary mt-1" name="generate_system_id"

                                id="generateSys">Generate a New Energy System Type</button>

                            <div id="energy_system_id_error" style="color: red;"></div>

                        </div>

                    </div>



                    <div class="row">

                        <div class="col-xl-6 col-lg-6 col-md-6 mb-1">

                            <fieldset class="form-group">

                                <label>Cycle Year</label>

                                <select name="energy_system_cycle_id" class="selectpicker form-control"

                                    id="energySystemCycleSelected" data-live-search="true" required>

                                    <option disabled selected>Choose one...</option>

                                    @foreach($energySystemCycles as $energySystemCycle)

                                        <option value="{{ $energySystemCycle->id }}">{{ $energySystemCycle->name }}</option>

                                    @endforeach

                                </select>

                            </fieldset>

                            <div id="energy_system_cycle_id_error" style="color: red;"></div>

                        </div>

                    </div>



                    



                    <div class="row mt-4">

                        <div class="col-xl-4 col-lg-4 col-md-4">

                            <button type="submit" class="btn btn-primary">Save changes</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>



    {{-- Create New Household Modal --}}

    <div id="createNewHousehold" class="modal fade" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h1 class="modal-title fs-5">Create New Household</h1>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <input type="hidden" id="csrf" value="{{ Session::token() }}">

                    <div class="row">

                        <div class="col-xl-6">

                            <fieldset class="form-group">

                                <label>Community</label>

                                <select name="community_id" id="selectedCommunity" class="selectpicker form-control"

                                    data-live-search="true">

                                    <option disabled selected>Choose one...</option>

                                    @foreach($communities as $community)

                                        <option value="{{ $community->id }}">{{ $community->english_name }}</option>

                                    @endforeach

                                    <option value="other" style="color:red">Other</option>

                                </select>

                            </fieldset>

                        </div>

                        <div class="col-xl-6">

                            <fieldset class="form-group">

                                <label>Father/Husband Name</label>

                                <input type="text" name="english_name" id="english_name" placeholder="Write in English"

                                    class="form-control">

                            </fieldset>

                        </div>

                    </div>



                    <div class="row">

                        <div class="col-xl-4">

                            <fieldset class="form-group">

                                <label>Father/Husband Name (Arabic)</label>

                                <input type="text" name="arabic_name" id="arabic_name" placeholder="Write in Arabic"

                                    class="form-control">

                            </fieldset>

                        </div>

                        <div class="col-xl-4">

                            <fieldset class="form-group">

                                <label>Wife/Mother Name</label>

                                <input type="text" name="women_name_arabic" id="women_name_arabic" class="form-control">

                            </fieldset>

                        </div>

                        <div class="col-xl-4">

                            <fieldset class="form-group">

                                <label>Profession</label>

                                <select name="profession_id" id="selectedProfession" class="form-control">

                                    <option disabled selected>Choose one...</option>

                                    @foreach($professions as $profession)

                                        <option value="{{ $profession->id }}">{{ $profession->profession_name }}</option>

                                    @endforeach

                                    <option value="other" style="color:red">Other</option>

                                </select>

                            </fieldset>

                            @include('employee.household.profession')

                        </div>

                    </div>



                    <div class="row mt-2">

                        <div class="col-xl-4"><input type="number" name="number_of_male" id="number_of_male"

                                class="form-control" placeholder="Male"></div>

                        <div class="col-xl-4"><input type="number" name="number_of_female" id="number_of_female"

                                class="form-control" placeholder="Female"></div>

                        <div class="col-xl-4"><input type="number" name="number_of_adults" id="number_of_adults"

                                class="form-control" placeholder="Adults"></div>

                    </div>



                    <div class="row mt-2">

                        <div class="col-xl-4"><input type="number" name="number_of_children" id="number_of_children"

                                class="form-control" placeholder="Children under 16"></div>

                        <div class="col-xl-4"><input type="number" name="school_students" id="school_students"

                                class="form-control" placeholder="Children in school"></div>

                        <div class="col-xl-4"><input type="number" name="university_students" id="university_students"

                                class="form-control" placeholder="University students"></div>

                    </div>



                    <div class="mt-3">

                        <button type="button" class="btn btn-secondary" id="newHouseholdButton">Submit</button>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>
$(function () {

    let compoundsXhr = null;
    let compoundsTimer = null;

    $('#selectedUserMisc').on('change', function () {
        var installation_type = $(this).val();
        $.get(`household/get_community_type/${installation_type}`, function (data) {
            $('#selectedUserCommunity')
                .prop('disabled', false)
                .html(data.html)
                .selectpicker('refresh');
        });
    });

    $('#selectedUserCommunity').on('changed.bs.select', function () {

        var communityId = $(this).val();
        var $compoundSelect = $('#selectedCompound');
        var $householdSelect = $('#selectedUserHousehold');

        $compoundSelect
            .empty()
            .append('<option value="">Choose one...</option>')
            .selectpicker('refresh');

        $householdSelect
            .empty()
            .append('<option disabled selected>Choose one...</option>')
            .selectpicker('refresh');

        if (!communityId) return;

        clearTimeout(compoundsTimer);

        compoundsTimer = setTimeout(function () {

            if (compoundsXhr && compoundsXhr.readyState !== 4) {
                compoundsXhr.abort();
            }

            compoundsXhr = $.get(`/compounds/by-community/${communityId}`)
                .done(function (data) {

                    if (Array.isArray(data) && data.length > 0) {
                        $compoundSelect.closest('.form-group').show();
                        data.forEach(function (c) {
                            $compoundSelect.append(
                                `<option value="${c.id}">${c.english_name}</option>`
                            );
                        });
                        $compoundSelect.selectpicker('refresh');
                    } else {
                        $compoundSelect.closest('.form-group').hide();
                        $.get(`household/get_un_user_by_community/${communityId}`, function (response) {
                            $householdSelect
                                .prop('disabled', false)
                                .html(response.html)
                                .selectpicker('refresh');
                        });
                    }
                })
                .fail(function () {
                    $compoundSelect.closest('.form-group').hide();
                    $.get(`household/get_un_user_by_community/${communityId}`, function (response) {
                        $householdSelect
                            .prop('disabled', false)
                            .html(response.html)
                            .selectpicker('refresh');
                    });
                });

        }, 150);

        changeEnergySystemType($('#selectedEnergySystemType').val(), communityId);
    });

    $('#selectedCompound').on('changed.bs.select', function () {

        var compoundId = $(this).val();
        var $householdSelect = $('#selectedUserHousehold');

        if (!compoundId) {
            $householdSelect
                .html('<option disabled selected>Choose one...</option>')
                .selectpicker('refresh');
            return;
        }

        $.get(`/compound/get_households/get_by_compound/${compoundId}`, function (data) {
            $householdSelect
                .html(data.htmlHouseholds)
                .prop('disabled', false)
                .selectpicker('refresh');
        });
    });

    $('#selectedEnergySystemType').on('changed.bs.select', function () {
        var energy_type_id = $(this).val();
        var community_id = [1, 3, 4].includes(Number(energy_type_id))
            ? $('#selectedUserCommunity').val()
            : 0;
        changeEnergySystemType(energy_type_id, community_id);
    });

    function changeEnergySystemType(energy_type_id, community_id) {
        if (!energy_type_id) return;
        $.get(`energy-user/get_by_energy_type/${energy_type_id}/${community_id}`, function (data) {
            $('#selectedEnergySystem')
                .prop('disabled', false)
                .html(data.html);
            checkGenerateButtonState();
        });
    }

    function buildDisplayName() {

        var communityText = $('#selectedUserCommunity option:selected').text().trim();
        var compoundVal = $('#selectedCompound').val();
        var compoundText = $('#selectedCompound option:selected').text().trim();
        var energyTypeText = $('#selectedEnergySystemType option:selected').text().trim();

        if (!$('#selectedUserCommunity').val() || !$('#selectedEnergySystemType').val()) {
            return null;
        }

        var parts = [];

        if (compoundVal) {
            parts.push(compoundText || compoundVal);
        } else {
            parts.push(communityText || $('#selectedUserCommunity').val());
        }

        parts.push(energyTypeText || $('#selectedEnergySystemType').val());

        return parts.join(' ').trim();
    }

    function checkGenerateButtonState() {

        var displayName = buildDisplayName();
        var $btn = $('#generateSys');

        if (!displayName) {
            $btn.prop('disabled', false).removeAttr('title');
            return;
        }

        var exists = $('#selectedEnergySystem option').filter(function () {
            return $(this).text().trim() === displayName;
        }).length > 0;

        if (exists) {
            $btn.prop('disabled', true).attr('title', 'Already exists');
        } else {
            $btn.prop('disabled', false).removeAttr('title');
        }
    }

    $('#selectedCompound').closest('.form-group').hide();

    $('#selectedUserCommunity, #selectedCompound, #selectedEnergySystemType')
        .on('changed.bs.select', function () {
            setTimeout(checkGenerateButtonState, 50);
        });

    checkGenerateButtonState();

});
</script>





@endsection