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

?>
