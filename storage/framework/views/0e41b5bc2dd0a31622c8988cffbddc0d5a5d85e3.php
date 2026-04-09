<!DOCTYPE html>
<html>
<head>
    <title>Comet-me</title>
</head>
<body>
    <h3><?php echo e($details['title']); ?></h3>
    <p>
        Dear <?php echo e($details['name']); ?>,
    </p>
    <p>
        <?php echo e($details['body']); ?>
    </p>
    <p>
        To complete the login process, please use the following one-time 2FA code:
    </p>
    <p>Verification Code :<?php echo e($details['code']); ?></p>
     
    <p>Best Regards</p>
</body>
</html><?php /**PATH C:\Users\User\CometProject\Cometme-Platform\resources\views/auth/email/code.blade.php ENDPATH**/ ?>