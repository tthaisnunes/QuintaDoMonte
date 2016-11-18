<?php
include("functions.php");
if(!array_key_exists("action", $_POST)){ $_POST["action"] = ""; }
if($_POST["action"] == "saveAgenda"){
	$arraySend = array(
		"Id_Agenda" => 0,
		"Email_Agenda" => $_POST["email"],
		"Telefone_Agenda" => $_POST["tel"],
		"Name_Agenda" => $_POST["name"]
	);
	$Id_Agenda = saveDataQry($td_agenda, $arraySend);
	if($Id_Agenda > 0){
		echo '{"status":"OK"}';
	} else {
		echo '{"status":"NO","message":"Não existe"}';
	}
}
?>