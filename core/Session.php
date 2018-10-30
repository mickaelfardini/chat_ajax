<?php

class Session
{
	public static function startSessionAction()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	public static function setSessionAction($name)
	{
		$query = "SELECT * FROM users WHERE pseudo = ?";
		$req = PDOConnection::prepareAction($query);
		$req->execute([htmlspecialchars($name)]);	
		$result = $req->fetch(PDO::FETCH_ASSOC);
		$_SESSION['pseudo'] = $result['pseudo'];
		$_SESSION['uid'] = md5($result['id']);

	}

	public static function destroySessionAction()
	{
		session_unset();
		session_destroy();
	}
}