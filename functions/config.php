<?php
$cfg = array();
$cfg["Send_Email"] = 1;
$cfg["Email_Admin"] = "noreply@campanhasportoseguro.com.br";

$ipserver = $_SERVER['SERVER_NAME'];
$ipuser = $_SERVER['REMOTE_ADDR'];
$bd = array();
if(preg_match("/\.local/", $ipserver)){
	// ambiente local - homologacao
	$bd["prefix"] = "QuintaDoMonte";
	$bd["server"] = "localhost";
	$bd["user"] = "root";
	$bd["pw"] = "root";
	$bd["database"] = "perplan";
	$rootSite = "/perplan/";
	$rootSiteEmail = "http://localhost" . $rootSite;
} else {
	// ambiente teste - producao
	$bd["prefix"] = "QuintaDoMonte";
	$bd["server"] = "mysql.ionicalabs.com.br";
	$bd["user"] = "ionicalabs05";
	$bd["pw"] = "ionlabs05";
	$bd["database"] = "ionicalabs05";
	$rootSite = "/";
	$rootSiteEmail = "http://lancamentos-perplan.ionicalabs.com.br/";
}
?>
