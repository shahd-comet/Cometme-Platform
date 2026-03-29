<div id="createActivistHolderModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create New Holder for Activist</h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button> 
            </div>
            <form method="POST" enctype='multipart/form-data' action="{{url('activist-holder')}}" id="activistHoldersForm">
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
                                <label class='col-md-12 control-label'>Community</label>
                                <select class="selectpicker form-control" name="community_id" 
                                    data-live-search="true" id="communitySelected">
                                    <option disabled selected>Choose one...</option>
                                    @foreach($communities as $community)
                                    <option value="{{$community->id}}">{{$community->english_name}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            <div id="community_id_error" style="color: red;"></div>
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

    $('#activistHoldersForm').on('submit', function (event) {

        event.preventDefault(); 

        let valid = true;

        var communityValue = $('#communitySelected').val();

        if (communityValue == null) {

            $('#community_id_error').html('Please select a community!'); 
            return false;
        } else  if (communityValue != null) {

            $('#community_id_error').empty();
        }

        $('#community_id_error').empty();

        if (valid) {

            $(this).addClass('was-validated');
            this.submit(); // submit the form
        }
    });
</script>