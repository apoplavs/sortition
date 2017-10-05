<?php
//require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'input_form.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.'Sortition.class.php';

if ($_POST['partisipants'] && $_POST['sectors'] && $_POST['zones'] && $_POST['partisipantsInSector'] && $_POST['tours']) {

	if (($sectors * $partisipantsInSector) < $partisipants || $zones < 2 || $tours > ($sectors / 2) || $partisipants < 8 || $sectors < 4) {
		echo "<h1 align='center' style='color: red'>С такими параметрами невозможно разместить всех участников<h1/>";
		die();
	}
	$sortition = new Sortition($partisipants, $sectors, $zones, $partisipantsInSector, $tours, (isset($_POST['circularPass']) ? true : false));
	$sortition->TossUp();
}