<?php
/*main operation file*/
require_once '../app/extensions/app.element.php';
// $_SESSION['admin_edits'] = 'set';
//loging in


if (isset($_GET['logout'])) {
	session_unset();
	session_destroy();
	header('location:' . base_path . "home");
	die();
}

$element = new Element();





//lets fetch the genrees
if (!isset($_SESSION['all_genres'])) {
	$element->activeTable = "all_genres";
	$element->comparisons = [['genre', ' != ', '']];
	$element->joiners = [''];
	$element->order = " BY genre DESC ";
	$element->cols = "genre, trend_present";
	$element->limit = 1000;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) > 0) {
		$_SESSION['all_genres'] = array_reverse($data);
	}
}




//lets fetch netflix content 
if (!isset($_SESSION['trends_netflix'])) {
	$element->activeTable = "trends_netflix";
	$element->comparisons = [];
	$element->joiners = [''];
	$element->order = " BY _date DESC, id DESC ";
	$element->cols = "*";
	$element->limit = 5;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) > 0) {
		$_SESSION['trends_netflix'] = ($data);
	}
}

//lets fetch imdb content 
if (!isset($_SESSION['trends_imdb'])) {
	$element->activeTable = "trends_imdb";
	$element->comparisons = [];
	$element->joiners = [''];
	$element->order = " BY _date DESC, id DESC ";
	$element->cols = "*";
	$element->limit = 5;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) > 0) {
		$_SESSION['trends_imdb'] = ($data);
	}
}

//lets fetch tomato content 
if (!isset($_SESSION['trends_tomato'])) {
	$element->activeTable = "trends_tomato";
	$element->comparisons = [];
	$element->joiners = [''];
	$element->order = " BY _date DESC, id DESC ";
	$element->cols = "*";
	$element->limit = 5;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) > 0) {
		$_SESSION['trends_tomato'] = ($data);
	}
}



//lets fetch anchortrends content 
if (!isset($_SESSION['trends_anchortrends'])) {
	$element->activeTable = "trends_anchortrends";
	$element->comparisons = [];
	$element->joiners = [''];
	$element->order = " BY _date DESC, id DESC ";
	$element->cols = "*";
	$element->limit = 5;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) > 0) {
		$_SESSION['trends_anchortrends'] = ($data);
	}
}





//lets fetch trending countries
if (!isset($_SESSION['all_release_countriese'])) {
	$element->activeTable = "all_release_countries";
	$element->comparisons = [['present', ' = ', 'yes']];
	$element->joiners = [''];
	$element->order = " BY country DESC ";
	$element->cols = "country,url";
	$element->limit = 5;
	$element->offset = 0;
	/*get_data*/
	$data = $element->getData();
	if (count($data) > 0) {
		$_SESSION['all_release_countries'] = ($data);
	}
}



//fetch this page data
$element->activeTable = "lentec_navbar";
$element->comparisons = [];
$element->joiners = [''];
$element->order = " BY id DESC ";
$element->cols = "section_id, section_title";
$element->limit = 1000;
$element->offset = 0;


/*get_data*/
$data = $element->GetElementData();
