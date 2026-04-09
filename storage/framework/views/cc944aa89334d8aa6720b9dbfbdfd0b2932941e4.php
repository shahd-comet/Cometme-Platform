<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="<?php echo e((!empty($containerNav) ? $containerNav : 'container-fluid')); ?> d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
    <div class="mb-2 mb-md-0">
      © <script>
        document.write(new Date().getFullYear())

      </script>
      , made with ❤️ by 
      <a href="<?php echo e((!empty(config('variables.creatorUrl')) ? config('variables.creatorUrl') : '')); ?>" target="_blank" class="footer-link fw-semibold">
        <?php echo e((!empty(config('variables.creatorName')) ? config('variables.creatorName') : '')); ?>
      </a>
    </div>
    <div>
      <a href="https://www.facebook.com/p/Comet-ME-100064679921744/" 
        class="footer-link me-4" target="_blank">
        Facebook
      </a>
      <a href="<?php echo e(config('variables.websiteUrl') ? config('variables.websiteUrl') : '#'); ?>" 
        target="_blank" class="footer-link me-4">Website
      </a>
    </div>
  </div>
</footer>
<!--/ Footer-->
<?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/layouts/sections/footer/footer.blade.php ENDPATH**/ ?>