<?php
	session_start();
	include("../settings/connect_datebase.php");
	require_once("../libs/autoload.php");
	
	// Проверка reCAPTCHA v3
	if(isset($_POST['g-recaptcha-response']) == false) {
		echo "-2"; // ошибка капчи
		exit;
	}
	
	$Secret = "6Lcovy8sAAAAAAZW9cZtLlMqPukyUbao3uplKzVp";
	$token = $_POST['g-recaptcha-response'];

	// Проверка токена reCAPTCHA v3
	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$Secret."&response=".$token);
	$responseKeys = json_decode($response, true);

	// Проверяем score
	if(!$responseKeys["success"] || $responseKeys["score"] < 0.5) {
		echo "-2"; // ошибка капчи
		exit;
	}
	
	$login = $_POST['login'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."';");
	
	$id = -1;
	if($user_read = $query_user->fetch_row()) {
		// создаём новый пароль
		$id = $user_read[0];
	}
	
	function PasswordGeneration() {
		// создаём пароль
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; // матрица
		$max=10; // количество
		$size=StrLen($chars)-1; // Определяем количество символов в $chars
		$password="";
		
		while($max--) {
			$password.=$chars[rand(0,$size)];
		}
		
		return $password;
	}
	
	if($id != -1) {
		//обновляем пароль
		$password = PasswordGeneration();
		// проверяем не используется ли пароль 
		$query_password = $mysqli->query("SELECT * FROM `users` WHERE `password`= '".md5($password)."';");
		while($password_read = $query_password->fetch_row()) {
			// создаём новый пароль
			$password = PasswordGeneration();
		}
		// обновляем пароль
		$mysqli->query("UPDATE `users` SET `password`='".md5($password)."' WHERE `login` = '".$login."'");
		// отсылаем на почту
		//mail($login, 'Безопасность web-приложений КГАПОУ "Авиатехникум"', "Ваш пароль был только что изменён. Новый пароль: ".$password);
	}
	
	echo $id;
?>