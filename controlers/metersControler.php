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


$pagination = '
<div class="w-100 center pagination">
	<button>
		<a PREVLINK >Prev</a>
	</button>
	<button>
		<a NEXTLINK>Next</a>
	</button>
</div>';

$meters = new Element();
$meters->activeTable = "lentec_meters_data";
$meters->comparisons = [];
$meters->joiners = [''];
$meters->order = " BY meter_name ASC ";
$meters->cols = "*";
$meters->limit = 1;
$meters->offset = isset($_GET['offset']) && is_numeric($_GET['offset']) ? $_GET['offset'] : 0;
/*get_data*/
$meters_data = $meters->getData($pagination);

// if(isset($_GET['dynamic'])){
//   die($frontEnd->displaymeters($meters_data));
// }





$element = new Element();
$element->activeTable = "lentec_meters";
$element->comparisons = [];
$element->joiners = [''];  
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title"; 
$element->limit = 1000;
$element->offset = 0;
/*get_data*/
$data = $element ->GetElementData();
