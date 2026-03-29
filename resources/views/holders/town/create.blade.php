<div id="createTownHolderModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create New Holder from Town</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button> 
            </div>
            <form method="POST" enctype='multipart/form-data' action="{{url('town-holder')}}" id="townHoldersForm">
            @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>English Name</label>
                                <input type="text" class="form-control" name="english_name" 
                                    placeholder="Enter English Name" required> 
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Arabic Name</label> 
                                <input type="text" class="form-control" name="arabic_name" 
                                    placeholder="Enter Arabic Name" required> 
                            </fieldset> 
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" 
                                    placeholder="Enter Phone Number" required> 
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Town</label>
                                <select class="selectpicker form-control" name="town_id" 
                                    data-live-search="true" id="townSelected">
                                    <option disabled selected>Choose one...</option>
                                    @foreach($towns as $town)
                                    <option value="{{$town->id}}">{{$town->english_name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <div id="town_id_error" style="color: red;"></div>
                        </div>
                    </div><br>
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Has Internet?</label>
                                <select class="selectpicker form-control" name="has_internet" 
                                    data-live-search="true" >
                                    <option disabled selected>Choose one...</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Has Refrigerator?</label>
                                <select class="selectpicker form-control" name="has_refrigerator" 
                                    data-live-search="true" >
                                    <option disabled selected>Choose one...</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    $('#townHoldersForm').on('submit', function (event) {

        event.preventDefault(); 

        let valid = true;

        var townValue = $('#townSelected').val();

        if (townValue == null) {

            $('#town_id_error').html('Please select a town!'); 
            return false;
        } else  if (townValue != null) {

            $('#town_id_error').empty();
        }

        $('#town_id_error').empty();

        if (valid) {

            $(this).addClass('was-validated');
            this.submit(); // submit the form
        }
    });
</script>