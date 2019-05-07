<?php
require_once "../../../core/views/ViewsManagement.php";
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$sql = "SELECT
				U.id,		
		       	U.username,
		       	U.email,
       			U.photo,
       			IF(F1.id_user_accepted IS NOT NULL, 1, 0)          AS invited,
				IF(F2.id_user_requested IS NOT NULL, 1, 0)         AS requester,
				IF((F1.accepted OR F2.accepted) IS NOT NULL, 1, 0) AS friend
			FROM user AS U
				LEFT JOIN friend AS F1
			    	ON U.id = F1.id_user_accepted
						AND F1.id_user_requested = '" . $session->user->id . "'
				LEFT JOIN friend AS F2
	                ON U.id = F2.id_user_requested
						AND F2.id_user_accepted = '" . $session->user->id . "'
			WHERE U.id <> '" . $session->user->id . "'
			ORDER BY U.username ASC";
	$query = $conn->query($sql);
	$vm = new ViewsManagement();
	$vm->session = $session;
	$vm->users = $query->fetchAll(PDO::FETCH_CLASS);
	$vm->set("panel_friends", "/app/views/fragments/panel-friends.php");
	$vm->set("panel_groups", "/app/views/fragments/panel-groups.php");
	$vm->set("content", "/app/views/users/list.php");
	$vm->render();
} else {
	RoutesManagement::redirect("/app/");
}