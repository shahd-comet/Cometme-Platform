<div class="row">
    <div class="col-xl-4">
        <label>Community</label>
        <select class="form-control" name="community_id">
            @if($allWaterHolder->Community)
            <option selected value="{{$allWaterHolder->Community->id}}">
            {{$allWaterHolder->Community->english_name}}
            </option>
            @endif
        </select>
    </div>
    <div class="col-xl-4">
        <label>Water Holder</label>
        <select class="form-control" name="holder_id" id="holderSelect">
            @if($allWaterHolder->Household)
                <option selected value="{{$allWaterHolder->Household->id}}" data-is-household="household">
                    {{$allWaterHolder->Household->english_name}}
                </option>
            @elseif($allWaterHolder->PublicStructure)
                <option selected value="{{$allWaterHolder->PublicStructure->id}}" data-is-household="public">
                    {{$allWaterHolder->PublicStructure->english_name}}
                </option>
            @endif
        </select>
        <input type="hidden" name="is_household" id="isHousehold">
    </div>
</div>

<script>
    const holderSelect = document.getElementById('holderSelect');
    const isHouseholdInput = document.getElementById('isHousehold');

    // Set initial value on page load
    isHouseholdInput.value = holderSelect.selectedOptions[0].dataset.isHousehold;

    // Update hidden field whenever the select changes
    holderSelect.addEventListener('change', function() {
        isHouseholdInput.value = this.selectedOptions[0].dataset.isHousehold;
    });
</script>