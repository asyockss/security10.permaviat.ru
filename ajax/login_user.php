<?php
	session_start();
	include("../settings/connect_datebase.php");
	require_once("../libs/autoload.php");
	
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// Проверка reCAPTCHA v3
	if(isset($_POST['g-recaptcha-response']) == false) {
		echo "captcha_error";
		exit;
	}
	
	$Secret = "6Lcovy8sAAAAAAZW9cZtLlMqPukyUbao3uplKzVp";
	$token = $_POST['g-recaptcha-response'];

	// Проверка токена reCAPTCHA v3
	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$Secret."&response=".$token);
	$responseKeys = json_decode($response, true);
	
	if(!$responseKeys["success"] || $responseKeys["score"] < 0.5) {
		echo "captcha_error";
		exit;
	}
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
	
	$id = -1;
	while($user_read = $query_user->fetch_row()) {
		$id = $user_read[0];
	}
	
	if($id != -1) {
		$_SESSION['user'] = $id;
		echo md5(md5($id));
	} else {
		echo "";
	}
?>