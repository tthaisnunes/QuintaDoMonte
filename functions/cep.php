<?php
$ipserver = $_SERVER['HTTP_HOST'];
$bd = array();
if (preg_match("/local/", $ipserver)){
	$bd["server"] = "localhost";
	$bd["user"] = "root";
	$bd["pw"] = "root";
	$bd["database"] = "cepbrasil";
}

$conn = mysql_connect($bd["server"], $bd["user"], $bd["pw"]);
mysql_select_db($bd["database"]);

$cep = $_GET["cep"];
$cep5 = substr($cep, 0, 5);
if(!preg_match("/\-/", $cep)){
	$cep = $cep5 . "-" . substr($cep, 5, 3);
}

$resultado = 0;
$resultado_txt = 'falha';

$queryB = "select * from cep_log_index where cep5 = '" . $cep5 . "'";
$tabelaBaseBD = mysql_query($queryB) or die(mysql_error());
$arrayc = array();
if(mysql_num_rows($tabelaBaseBD) > 0){
	while($tabelaBaseResult = mysql_fetch_array($tabelaBaseBD)){
		$uf = $tabelaBaseResult["uf"];
		
		$query = "select * from " . $uf . " where cep = '" . $cep . "'";
		$cc = mysql_query($query) or die(mysql_error());
		$arrayc = array();
		if(mysql_num_rows($cc) > 0){
			while($acc = mysql_fetch_array($cc)){
				$cidade = $acc["cidade"];
				$bairro = $acc["bairro"];
				$tipo_logradouro = $acc["tp_logradouro"];
				$logradouro = $acc["logradouro"];
			}
			$resultado = 1;
			$resultado_txt = 'sucesso - cep completo';
		}
	}
}

//echo $queryB . "<hr>" . $query . "<br>";
echo '<?phpxml version="1.0" encoding="iso-8859-1"?>
<webservicecep>
<uf>' . strtoupper($uf) . '</uf>
<cidade>' . $cidade . '</cidade>
<bairro>' . $bairro . '</bairro>
<tipo_logradouro>' . $tipo_logradouro . '</tipo_logradouro>
<logradouro>' . $logradouro . '</logradouro>
<resultado>' . $resultado . '</resultado>
<resultado_txt>' . $resultado_txt . '</resultado_txt>
</webservicecep>';

?>