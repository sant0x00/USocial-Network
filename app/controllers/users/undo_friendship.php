<?php
require_once "../../../core/session/SessionManagement.php";
require_once "../../../core/routes/RoutesManagement.php";
require_once "../../../core/db/DatabaseConnection.php";
$session = SessionManagement::getInstance();
if ($session->logged()) {
	$conn = DatabaseConnection::getInstance();
	$out = array("error" => false);
	//
	$id01 = $session->user->id;
	$id02 = isset($_POST["id"]) ? $_POST["id"] : "";
	//
	$sql = "DELETE F FROM friend AS F 
			WHERE ((F.id_user_requested = $id01 AND F.id_user_accepted = $id02)
				OR (F.id_user_requested = $id02 AND F.id_user_accepted = $id01))";
	$query = $conn->query($sql);
	if ($query->rowCount() > 0) {
		$out["id"] = $id02;
		$out["message"] = "Successful undo friendship.";
		header("Content-type: application/json");
		echo json_encode($out);
		die();
	} else {
		$out["error"] = true;
		$out["message"] = "Error on deletion.";
		header("Content-type: application/json");
		echo json_encode($out);
		die();
	}
} else {
	RoutesManagement::redirect("/app/");
}