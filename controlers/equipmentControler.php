<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
require_once '../app/extensions/app.validation.php';
require_once '../app.extensions/app.front.extension.php';
//loging in check
if(!isset($_SESSION['email']))
{
	//do something
	// $_SESSION['logged_page'] = str_replace(".php","",basename($_SERVER['PHP_SELF']));
	// header('location:'.base_path."/sign_in");
	// die();
}
else
{
	// do something
	// if(!isset($_SESSION['admin_edits'])){
	// 	header('location:' . $page );
	// }
}


$equipment = new Element();
$equipment->activeTable = "lentec_equipment_data";
$equipment->comparisons = [];
$equipment->joiners = [''];
$equipment->order = " BY equipment_name ASC ";
$equipment->cols = "*";
$equipment->limit = 40;
$equipment->offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
/*get_data*/
$equipment_data = $equipment->getData();

if(isset($_GET['dynamic'])){
  die($frontEnd->displayEquipment($equipment_data));
}





$element = new Element();
$element->activeTable = "lentec_equipment";
$element->comparisons = [];
$element->joiners = [''];  
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title"; 
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element ->GetElementData();
