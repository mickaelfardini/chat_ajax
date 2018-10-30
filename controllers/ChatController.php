<?php

class ChatController
{
	public static function defaultAction()
	{
		if (!Controller::isConnected())
		{
			header("Location: ../");
			return 0;
		}
		Controller::renderAction("chat");
	}

	public static function getMembers()
	{
		return ChatModel::getMembersAction($_SESSION['uid']);
	}

	public static function getUserInfo()
	{
		return ChatModel::getUserInfoAction();
	}

	public static function getUserMessagesAction()
	{
		ChatModel::getUserMessagesAction();
	}

	public static function sendAction()
	{
		ChatModel::sendAction();
	}

	public static function getLastMessageAction()
	{
		ChatModel::getLastMessageAction();
	}
}