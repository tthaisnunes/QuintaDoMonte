<?php
date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
include("config.php");

if(!isset($_POST["Acao"])){ $_POST["Acao"] = ""; }
if(!isset($_GET["uriA"])){ $_GET["uriA"] = ""; }
if(!isset($_GET["uriB"])){ $_GET["uriB"] = ""; }

require('bd_mysqli.php');
require('tables.php');

function enviarMensagemMail($dadosMensagem){
	if(strlen($dadosMensagem["Email_Destino"]) > 0){
		$Mensagem_News = $dadosMensagem["Mensagem"];
		$Mensagem_News_simple = "This is a multi-part message in MIME format.";
		$Mensagem_News_plain = str_replace(array("\n","\r"), array("<br>",""),$Mensagem_News);
		$Mensagem_News_plain = preg_replace("/(<\/?)[(!-:; {})*(\w+)]([^>]*>)/", "\n",$Mensagem_News_plain);
		$Mensagem_News_plain = str_replace(array("\n\n\n","\n\n"), "\n",$Mensagem_News_plain);
		$Email_Remetente = $dadosMensagem["Email_Remetente"];
		$headers  = 'From: ionicalabs@ionicalabs.com.br' . "\n";
		$headers .= 'Return-Path: ' . $Email_Remetente . "\n";
		$headers .= 'BCC: acelmar@ionica.com.br' . "\n";
		$headers .= 'MIME-Version: 1.0' ."\n";
		$headers .= 'Content-type: text/HTML; charset=utf-8' . "\n";
		$assunto = $dadosMensagem["Assunto"];
		$assunto = "=?UTF-8?B?" . base64_encode(utf8_encode($assunto)) . "?=";
		$sendEmailProceed = mail($dadosMensagem["Email_Destino"], $assunto, $Mensagem_News, $headers, "-r".$Email_Remetente);
		if($sendEmailProceed){
			return true;
		} else {
			return false;
		}
	}
}
?>
