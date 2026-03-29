<div class="row">
    <h5>Donors</h5>
</div>

@if(count($allWaterHolderDonors) > 0)
    <table class="table table-striped" id="allWaterHolderDonorsTable">
        <tbody>
        @foreach($allWaterHolderDonors as $donor)
            <tr>
                <td class="text-center">{{$donor->Donor->donor_name}}</td>
                <td class="text-center">
                    <a class="btn deleteWaterDonor" data-id="{{$donor->id}}">
                        <i class="fa fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif