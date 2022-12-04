<?php
session_start();
require_once '../controlers/entries_expenseControler.php';
require_once '../blades/header.php';
?>

<div class="container ck">
  <h1 class="non_ck" <?php $element->is_editable(current_page_table, 'page_title_phrase', 'text'); ?>><?php echo $page_title_phrase; ?></h1>
  <div style="min-height:70vh; padding:50px 20px;">
    <input type="hidden" value="<?php echo current_page_table; ?>" name="current_page_table" id="current_page_table">
    <form method="post" class="ajax" enctype="multipart/form-data" action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>">
      <div class="form-group">
        <input type="hidden" value="<?php echo csrf_token; ?>" name="csrf_token">
        <label for="meter" class="non_ck" <?php $element->is_editable(current_page_table, 'label2', 'text'); ?>><?php echo $label2; ?></label>
        <input  class="inputs"  type="date" name="_date_" id="_date_" required>
      </div>
     

    </form>
  </div>
</div>



<?php
require_once '../blades/footer.php';
?>

