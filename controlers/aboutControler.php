<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
//loging in check
if(!isset($_SESSION['username']))
{
	//do something
}
else
{
	//do something
}

$element = new Element();
$element->activeTable = "lentec_about";
$element->comparisons = [];
$element->joiners = [''];  
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title"; 
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element ->GetElementData();
