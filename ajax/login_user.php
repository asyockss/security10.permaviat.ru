<?php
	session_start();
	include("../settings/connect_datebase.php");
	require_once("../libs/autoload.php");
	
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// Проверка reCAPTCHA
	if(isset($_POST['g-recaptcha-response']) == false) {
		echo "captcha_error";
		exit;
	}
	
	$Secret = "6LdksC8sAAAAAC1ffOPHWlVXiL_-uiX0w0f46rW8";
	$Recaptcha = new \ReCaptcha\ReCaptcha($Secret);
	$Response = $Recaptcha->verify($_POST["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]);
	
	if(!$Response->isSuccess()) {
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