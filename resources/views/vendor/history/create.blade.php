<style>
    label, input {
    display: block;
}

label, table {
    margin-top: 20px;
}

.headingLabel {
    font-size:18px;
    font-weight: bold;
}
</style> 


<div id="createNewVendingHistory" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                    Create New Vending History	
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body">
                <form method="POST" enctype='multipart/form-data' id="vendingHistoryForm"
                    action="{{url('vending-history')}}">
                    @csrf
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Vending Point</label>
                                <select class="selectpicker form-control" name="vendor_id" data-live-search="true" 
                                    id="vendorSelected" required>
                                    <option disabled selected>Choose one...</option>
                                    @foreach($vendors as $vendor)
                                    <option value="{{$vendor->id}}">{{$vendor->english_name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <div id="vendor_id_error" style="color: red;"></div>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Service</label>
                                <select class="selectpicker form-control" name="service_type_id" data-live-search="true" 
                                    id="serviceSelected" required>
                                </select>
                            </fieldset>
                            <div id="service_type_id_error" style="color: red;"></div>
                        </div>

                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Visit By</label>
                                <select class="selectpicker form-control" name="user_id" data-live-search="true" 
                                    id="userSelected">
                                    <option disabled selected>Choose one...</option>
                                    @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <div id="user_id_error" style="color: red;"></div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Visit Date</label>
                                <input type="date" name="visit_date" required
                                    class="form-control">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Collecting Date From</label>
                                <input type="date" name="collecting_date_from" required
                                    class="form-control">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Collecting Date To</label>
                                <input type="date" name="collecting_date_to" required
                                    class="form-control">
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Total Amount</label>
                                <input type="number" name="total_amount_due" class="form-control" 
                                    id="totalAmount" step="0.01">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Collecting Amount</label>
                                <input type="number" name="amount_collected" class="form-control" 
                                    id="amountCollected" step="0.01">
                            </fieldset>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-4 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Remaining Amount</label>
                                <input type="number" name="remaining_balance" class="form-control" 
                                    id="remainingBalance" step="0.01" readonly>
                            </fieldset>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Notes</label>
                                <textarea name="notes" class="form-control" 
                                    style="resize:none" cols="20" rows="2">
                                </textarea>
                            </fieldset>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>

    $(document).on('change', '#vendorSelected', function () {

        vendor_id = $(this).val();
        $.ajax({
            url: "vending-history/get_by_vendor/" + vendor_id,
            method: 'GET',
            success: function(data) {
                var select = $('#serviceSelected');
                select.prop('disabled', false); 
                select.html(data.html);
                select.selectpicker('refresh');
            }
        });
    });


    $(document).ready(function() {

        function calculateRemaining() {

            let total = parseFloat($('#totalAmount').val()) || 0;
            let collected = parseFloat($('#amountCollected').val()) || 0;
            let remaining = total - collected;
            $('#remainingBalance').val(remaining.toFixed(2));
        }

        $('#totalAmount, #amountCollected').on('input', function () {

            calculateRemaining();
        });

        $('#vendingHistoryForm').on('submit', function (event) {

            var vendorValue = $('#vendorSelected').val();
            var serviceValue = $('#serviceSelected').val();
            var userValue = $('#userSelected').val();

            if (vendorValue == null) {

                $('#vendor_id_error').html('Please select a vendor!'); 
                return false;
            } else if (vendorValue != null){

                $('#vendor_id_error').empty();
            }

            if (serviceValue == null) {

                $('#service_type_id_error').html('Please select a service!'); 
                return false;
            } else if (serviceValue != null){

                $('#service_type_id_error').empty();
            }

            if (userValue == null) {

                $('#user_id_error').html('Please select a user!'); 
                return false;
            } else if (userValue != null){

                $('#user_id_error').empty();
            }

            $(this).addClass('was-validated');  
            $('#vendor_id_error').empty();  
            $('#service_type_id_error').empty();  
            $('#user_id_error').empty(); 
            this.submit();
        });
    });
</script>