<?php
session_start();
require_once '../controlers/privacy_policyControler.php';
require_once '../blades/header.php';
?>

<div class="container ck">
  <div style="min-height:70vh; padding:50px 20px;">
    <div <?php $element->is_editable(current_page_table, 'privacy', 'text'); ?>><?php echo $privacy; ?></div>
  </div>
</div>


<?php
require_once '../blades/footer.php';
?>

