<?php

class IndexModel
{
	public static function connectAction($pseudo, $password)
	{
		$query = "SELECT * FROM users 
		WHERE pseudo = ?";
		$req = PDOConnection::prepareAction($query);
		$req->execute([htmlspecialchars($pseudo)]);
		$result = $req->fetch(PDO::FETCH_ASSOC);
		if ($result['pseudo'])
		{
			if (password_verify($password, $result['password']))
			{
				Session::setSessionAction($result['pseudo']);
				echo json_encode(["ok" => "ok"]);
				return 1;
			}
			echo json_encode(["error" => "bad login"]);
			return 0;
		}
		$query = "INSERT INTO users (pseudo, password) VALUES (?, ?)";
		$req = PDOConnection::prepareAction($query);
		$req->execute([htmlspecialchars($pseudo), password_hash($password, PASSWORD_BCRYPT)]);
		self::connectAction($pseudo, $password);
	}
}