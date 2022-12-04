<?php
session_start();
require_once '../controlers/verify_accountControler.php';
require_once '../blades/header.php';
?>

<section class="area-sign-in">
  <div class="sign-in" style="font-weight: normal; text-align:center;">
    <h3 class="center">Account Verification</h3>
    <?php echo $html; ?>
  </div>
</section>

<?php
require_once '../blades/footer.php';
?>