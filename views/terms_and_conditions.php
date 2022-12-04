<?php
session_start();
require_once '../controlers/terms_and_conditionsControler.php';
require_once '../blades/header.php';
?>

<div class="container ck">
  <div style="min-height:70vh; padding:50px 20px;">
    <div <?php $element->is_editable(current_page_table, 'terms_and_conditions', 'text'); ?>><?php echo $terms_and_conditions; ?></div>
  </div>
</div>


<?php
require_once '../blades/footer.php';
?>

