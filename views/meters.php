<?php
session_start();
require_once '../controlers/metersControler.php';
require_once '../blades/header.php';

if ($element->page_editable) {
  echo   '<div class="edit_div"  style="color:#fff; background:#000;">
              <textarea style="display:none;">' .
    json_encode([
      'meter_serial_number' => '',
      'meter_name' => '',
      'lat' => '',
      'lng' => '',
      'origin' => 'lentec_meters_data',
      'form_title' => 'Add new Meter'
    ]) .          '</textarea>
             <a href="#" class="addItem">Add new Meter</a>
         </div>';
}

?>


<div class="container" id="main_item_field">
  <?php
  echo $frontEnd->displayMeter($meters_data);
  ?>
</div>

<?php
require_once '../blades/footer.php';
?>