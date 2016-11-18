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

	$Mensagem = '<div style="font-family:Arial; font-size:12px. line-height:1.1; color:#666666;">Solicitação de contato recebida pela landing page Perplan <strong>Quinta Do Monte</strong></div><br /><br />';
	$Mensagem .= '<div style="margin:0; padding:20px; background-color:#EFEFEF; border:1px solid #CCCCCC; font-family:Arial; font-size:12px. line-height:1.1; color:#333333;">';
	$Mensagem .= 'LP: Quinta do Monte<br />';
	$Mensagem .= 'NOME: ' . $_POST['name'] . '<br />';
	$Mensagem .= 'EMAIL: ' . $_POST['email'] . '<br />';
	$Mensagem .= 'TELEFONE: ' . $_POST['tel'];
	$Mensagem .= '</div>';

	$dadosMensagem = array();
	$dadosMensagem["Mensagem"] = $Mensagem;
	$dadosMensagem["Email_Remetente"] = $_POST["email"];
	$dadosMensagem["Assunto"] = "Contato Quinta do Monte (lp)";
	// $dadosMensagem["Email_Destino"] = "thaisnunes.ionica@gmail.com";
	$dadosMensagem["Email_Destino"] = "carlos.marin@perplan.com.br";
	$envio = enviarMensagemMail($dadosMensagem);
	if(!$envio){
		echo '{"status":"NO","message":"Falha envio"}';
	} else {
		if($Id_Agenda > 0){
			echo '{"status":"OK"}';
		} else {
			echo '{"status":"NO","message":"Não existe"}';
		}
	}
}
?>