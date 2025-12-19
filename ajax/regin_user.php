<?php
	session_start();
	require_once("../settings/connect_datebase.php");
	require_once("../libs/autoload.php");

	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
	$id = -1;
	
	if($user_read = $query_user->fetch_row()) {
		echo $id;
	} else {
		if(isset($_POST['g-recaptcha-response']) == false) {
			echo "Ошибка проверки безопасности";
			exit;
		}
		$Secret = "6Lcovy8sAAAAAAZW9cZtLlMqPukyUbao3uplKzVp";
		$token = $_POST['g-recaptcha-response'];

		// Проверка токена reCAPTCHA v3
    	$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$Secret."&response=".$token);
    	$responseKeys = json_decode($response, true);
		
		if($responseKeys["success"] && $responseKeys["score"] >= 0.5) {
			$mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$password."', 0)");
			
			$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
			$user_new = $query_user->fetch_row();
			$id = $user_new[0];
				
			if($id != -1) $_SESSION['user'] = $id; // запоминаем пользователя
			echo $id;
		} else {
			echo "Пользователь не распознан.";
			exit;
		}
	}
?>