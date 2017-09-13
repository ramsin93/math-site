<?php
header("Location: plot.php");

include_once "safereval/config.safereval.php";
include_once "safereval/class.safereval.php";

if (!empty($_POST['equation'])) {
	$equation =  $_POST['equation'];
	$width = empty($_POST['width']) ? 600 : $_POST['width'];
	$height = empty($_POST['height']) ? 600 : $_POST['height'];
	$scale = empty($_POST['scale']) ? 1. : floatval($_POST['scale']);
	
	$seval = new SaferEval();
	$code = str_replace('X', '$x', $equation) . ';';
	$errors = $seval->checkScript($code, 1);
	
	session_id('dbefb3bb6a20767d5f1d');
	session_start();
	
	if ($errors) {
		$_SESSION['error'] = $seval->htmlErrors($errors);
	} else {
		$_SESSION['equation'] = $equation;
		$_SESSION['width'] = $width;
		$_SESSION['height'] = $height;
		$_SESSION['scale'] = $scale;
	}
}

?>