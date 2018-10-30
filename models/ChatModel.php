<?php

class ChatModel
{
	public static function getUserInfoAction()
	{
		$query = "SELECT pseudo, img, status FROM users WHERE md5(id) = ?";
		$req = PDOConnection::prepareAction($query);
		$req->execute([$_SESSION['uid']]);
		return $req->fetchAll(PDO::FETCH_ASSOC)[0];
	}

	public static function getMembersAction($id)
	{
		$query = "SELECT pseudo, img, status, last_msg
				FROM users 
				WHERE md5(id) != ?";
		$req = PDOConnection::prepareAction($query);
		$req->execute([$id]);
		return $req->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function getUserMessagesAction()
	{
		$query = "SELECT messages.id, content, date_message, 
					sender.pseudo AS 'sender',
					receiver.pseudo AS 'receiver', sender.img
					FROM messages
					JOIN users sender ON id_sender = sender.id
					JOIN users receiver ON id_receiver = receiver.id
					WHERE (sender.pseudo = ? AND receiver.pseudo = ?)
					OR (receiver.pseudo = ? AND sender.pseudo = ?)
					ORDER BY date_message ASC";
		$req = PDOConnection::prepareAction($query);
		$req->execute([$_SESSION['pseudo'], $_POST['pseudo'],
						$_SESSION['pseudo'], $_POST['pseudo']]);
		echo json_encode([$req->fetchAll(PDO::FETCH_ASSOC)]);
		return 1;
	}

	public static function getLastMessageAction()
	{
		$query = "SELECT messages.id, content, date_message, 
					sender.pseudo AS 'sender',
					receiver.pseudo AS 'receiver', sender.img
					FROM messages
					JOIN users sender ON id_sender = sender.id
					JOIN users receiver ON id_receiver = receiver.id
					WHERE ((sender.pseudo = ? AND receiver.pseudo = ?)
					OR (receiver.pseudo = ? AND sender.pseudo = ?))
					AND messages.id > ?
					ORDER BY date_message DESC";
		$req = PDOConnection::prepareAction($query);
		$req->execute([$_SESSION['pseudo'], $_POST['pseudo'],
						$_SESSION['pseudo'], $_POST['pseudo'], $_POST['id_msg']]);
		echo json_encode([$req->fetchAll(PDO::FETCH_ASSOC)]);
		return 1;
	}

	public static function sendAction()
	{
		$query = "SELECT id FROM users WHERE pseudo = ?";
		$req = PDOConnection::prepareAction($query);
		$req->execute([$_POST['pseudo']]);
		$id = $req->fetch(PDO::FETCH_ASSOC)['id'];

		$query = "SELECT id FROM users WHERE pseudo = ?";
		$req = PDOConnection::prepareAction($query);
		$req->execute([$_SESSION['pseudo']]);
		$id_user = $req->fetch(PDO::FETCH_ASSOC)['id'];

		if (!$id) {
			echo json_encode(["error" => "Invalid username"]);
			return 0;
		}
		$queryInsertDate = "UPDATE users u1 JOIN users u2
							ON u1.id = ? AND u2.id = ?
							SET u1.last_msg = NOW(),
							u2.last_msg = NOW()";
		$reqDate = PDOConnection::prepareAction($queryInsertDate);
		$reqDate->execute([$id, $id_user]);
		$query = "INSERT INTO messages (id_sender, id_receiver, content)
					VALUES (?, ?, ?)";
		$req = PDOConnection::prepareAction($query);
		if($req->execute([$id_user,
						$id,
						$_POST['content']]))
		{
			echo json_encode(["ok" => "Message successfully sent."]);
			return 1;
		}
		echo json_encode(["error" => "An error has occured."]);
		return 0;
	}
}