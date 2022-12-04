<?php
session_start();
require_once '../controlers/entries_meterControler.php';
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
        <input class="inputs" id="period_date" type="text" name="period_date" required>
      </div>
      <div class="form-group">
        <label for="meter" class="non_ck" <?php $element->is_editable(current_page_table, 'label1', 'text'); ?>><?php echo $label1; ?></label>
        <select class="inputs" id="meter_serial_number" name="meter_serial_number" required>
          <option value="" selected disabled><?php echo $label1; ?></option>
          <?php
          foreach ($meters_data as $value) {
            echo "<option value='" . $value['meter_serial_number'] . "'>" . $value['meter_name'] . "</option>";
          }
          ?>
        </select>
      </div>

    </form>


    <?php

    if (isset($_SESSION['admin_edits'])) {
    ?>

      <br><br>
      <h1 class="non_ck" <?php $element->is_editable(current_page_table, 'page_title_phrase1', 'text'); ?>><?php echo $page_title_phrase1; ?></h1>


      <form method="post" class="bulk_meter" id="bulk_meter" enctype="multipart/form-data" action="<?php echo base_path . str_replace(".php", "", basename($_SERVER['PHP_SELF']));  ?>">
      <div class="form-group" id="bulk_meter_result" style="max-height: 150px !important; overflow-y:auto;">
    </div>
      <div class="form-group">
          <label for="meter" class="non_ck" <?php $element->is_editable(current_page_table, 'label10', 'text'); ?>><?php echo $label10; ?></label>
          <textarea style="width: 100%; height: 300px;" required></textarea>
        </div>
        <div class="form-group">
          <select class="inputs" id="meter_serial_number" name="meter_serial_number" required>
            <option value="" selected disabled><?php echo $label1; ?></option>
            <?php
            foreach ($meters_data as $value) {
              echo "<option value='" . $value['meter_serial_number'] . "'>" . $value['meter_name'] . "</option>";
            }
            ?>
          </select>
        </div>

        <div class="form-group">
          <input name="ad_unit" type="submit" value="add bulk data">
        </div>

      </form>

    <?php
    }
    ?>
  </div>
</div>

<?php
require_once '../blades/footer.php';
?>