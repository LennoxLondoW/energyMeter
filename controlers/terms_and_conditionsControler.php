<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
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

$element = new Element();
$element->activeTable = "lentec_terms_and_conditions";
$element->comparisons = [];
$element->joiners = [''];  
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title"; 
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element ->GetElementData();
