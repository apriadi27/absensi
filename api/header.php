<?php
	header("Access-Control-Allow-Origin: *");
    header("X-Requested-With: XMLHttpRequest");
    header("Content-Type: application/json;charset=UTF-8");
	header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
	
	include "koneksi.php";
	//include 'func.php';
	date_default_timezone_set('Asia/Bangkok');
	// error_reporting("E_NONE");

	mysqli_query($conn, 'START TRANSACTION');
	$val = [
		'error' => false,
		'msg' => '',
		'data' => ''
	];

	$log = "../log/".date('Y-m-d').".txt";
	$logText = '';
?>