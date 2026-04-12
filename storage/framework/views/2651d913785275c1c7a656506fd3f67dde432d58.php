<!-- Add Vendor Username Modal -->
<div class="modal fade" id="addVendorUsernameModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addVendorUsernameForm">
        <?php echo csrf_field(); ?>
        <div class="modal-header">
          <h5 class="modal-title">Add New Vendor Username</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="modalServiceId" value="">
          <div class="mb-3">
            <label for="newVendorUsername" class="form-label">Vendor Username</label>
            <input type="text" class="form-control" id="newVendorUsername" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Username</button>
        </div>
      </form>
    </div>
  </div>
</div><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/vendor/create-vendor-username.blade.php ENDPATH**/ ?>