<?php
session_start();
require_once '../controlers/equipmentControler.php';
require_once '../blades/header.php';

if ($element->page_editable) {
  echo   '<div class="edit_div"  style="color:#fff; background:#000;">
              <textarea style="display:none;">' .
    json_encode([
      'equipment_name' => '',
      'equipment_rating' => '',
      'equipment_quantity' => '',
      'file' => '',
      'origin' => 'lentec_equipment_data',
      'form_title' => 'Add new Equipment'
    ]) .          '</textarea>
             <a href="#" class="addItem">Add new Equipment</a>
         </div>';
}

?>




<div class="container">
  <h1>Equipment Data</h1>
  <div class="float-wrappper center" id="main_item_field">
    <?php
    echo $frontEnd->displayEquipment($equipment_data)
    ?>

  </div>
</div>


<?php
require_once '../blades/footer.php';
?>