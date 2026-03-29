<style>
    label, input {
        display: block;
    }

    label, table {
        margin-top: 20px;
    }
</style>  

<div id="createComponentAccessory" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">
                    Create New Camera Component & Accessory
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" 
                    aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype='multipart/form-data' 
                    action="{{url('camera-component-accessory')}}">
                    @csrf

                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Component Name</label>
                                <input type="text" name="component_name" 
                                    class="form-control" placeholder="Enter component name" required>
                            </fieldset>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Component Type</label>
                                <input type="text" name="component_type" 
                                    class="form-control" placeholder="Enter component type" required>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                            <fieldset class="form-group">
                                <label class='col-md-12 control-label'>Description</label>
                                <textarea name="description" class="form-control" 
                                    rows="3" placeholder="Enter component description"></textarea>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row" style="margin-top:20px">
                        <div class="col-xl-4 col-lg-4 col-md-4">
                            <button type="submit" class="btn btn-primary">
                                Create Component & Accessory
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 