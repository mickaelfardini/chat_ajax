<?php

class IndexController
{
	public static function defaultAction()
	{
		Controller::renderAction("login");
	}

	public static function connectAction()
	{
		IndexModel::connectAction($_POST['pseudo'], $_POST['password']);
	}
}