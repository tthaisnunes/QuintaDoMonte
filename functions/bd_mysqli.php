<?php
$conn = mysqli_connect($bd["server"], $bd["user"], $bd["pw"], $bd["database"]) or die(mysqli_error($conn));

function getTablesInBd(){
	global $bd, $conn;
	$tablesInBd = array();
	$tableList = array();
	$res = mysqli_query($conn,"SHOW TABLES");
	while($cRow = mysqli_fetch_array($res)){
		$tableList[] = $cRow[0];
	}
	$table = 0;
	foreach($tableList as $k => $tablename){
		//if(preg_match("/^" . $bd["prefix"] . "_/", $tablename)){
			array_push($tablesInBd, $tablename);
		//}
		$table++;
	}
	return $tablesInBd;
}
$tables_exists = false;
$tablesInBd = getTablesInBd();
if(count($tablesInBd) > 0){
	$tables_exists = true;
}

# FUNCAO PARA MONTAR QUERY
function selectQry($table, $fields, $where, $order, $limit, $num){
	$fields = str_replace("\"","",$fields);
	$where = str_replace("\"","",$where);
	$order = str_replace("\"","",$order);
	$qry = "SELECT $fields FROM $table";
	if(strlen($where) > 0){ $qry .= " WHERE $where"; }
	if(strlen($order) > 0){ $qry .= " ORDER BY $order"; }
	if(strlen($limit) > 0){ $limit--; $qry .= " LIMIT $limit"; }
	if(strlen($num) > 0){ $qry .= ",$num"; }
	return $qry;
}

//$debug = 1;
function failQuery($error){
	global $showConnections, $debug, $isEditor;
	if($showConnections == 1 && $debug == 0 && $isEditor == 0){
		return "OCORREU UMA FALHA";
	} else {
		return $error;
	}
}

function secureSuperGlobalGET(&$value, $key, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	$_GET[$key] = htmlspecialchars(stripslashes($_GET[$key]));
	$_GET[$key] = str_ireplace("script", "blocked", $_GET[$key]);
	$_GET[$key] = mysqli_real_escape_string($connOK, $_GET[$key]);
	return $_GET[$key];
}

function secureSuperGlobalPOST(&$value, $key, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	$_POST[$key] = htmlspecialchars(stripslashes($_POST[$key]));
	$_POST[$key] = str_ireplace("script", "blocked", $_POST[$key]);
	$_POST[$key] = mysqli_real_escape_string($connOK, $_POST[$key]);
	return $_POST[$key];
}
	
function secureGlobals(){
	if(count($_GET) > 0){
		array_walk($_GET, 'secureSuperGlobalGET');
	}
	if(count($_POST) > 0){
		array_walk($_POST, 'secureSuperGlobalPOST');
	}
}

function getDataQry($query, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	$cc = mysqli_query($connOK, $query) or die( failQuery( $query . '<hr>' . mysqli_error($connOK) ));
	//$cc = mysqli_query($query);
	$cn = 0;
	$arrayc = array();
	if(mysqli_num_rows($cc) > 0){
		while($acc = mysqli_fetch_array($cc)){
			$cn++;
			$arrayc[$cn] = array();
			$arrayc[$cn] = $acc;
		}
	}
	return $arrayc;
}

function getDataQryID($query, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	$cc = mysqli_query($connOK, $query) or die( failQuery( $query . '<hr>' . mysqli_error($connOK) ));
	//$cc = mysqli_query($query);
	$arrayc = array();
	if(mysqli_num_rows($cc) > 0){
		while($acc = mysqli_fetch_array($cc)){
			$cn = $acc[0];
			$arrayc[$cn] = array();
			$arrayc[$cn] = $acc;
		}
	}
	return $arrayc;
}

function saveDataQry($tbl, $array, $obConn=false){
	global $conn, $debug, $alert, $clsbdajax;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	$nf = 0;
	$fu = ""; $fif = ""; $fiv = "";
	foreach($array as $f => $v){
		if($clsbdajax == 1) $v = utf8_decode($v);
		if($nf == 0){
			$idName = $f;
			$id = $v;
		} else {
			$v = str_replace("'","&#39;",$v);
			if(preg_match("/Valor_/",$f)){
				if(preg_match("/,/",$v)){
					$v = str_replace(".","",$v);
					$v = str_replace(",",".",$v);
				}
			}
			if(!preg_match("/Data_Ins/",$f)){ $fu .= "$f='$v',"; }
			if(preg_match("/Data_Ins/",$f) && empty($v)){ $v=date("Y-m-d H:i:s"); }
			$fif .= "$f,";
			$fiv .= "'$v',";
		}
		$nf++;
	}
	$fu = substr($fu,0,strlen($fu)-1);
	$fif = substr($fif,0,strlen($fif)-1);
	$fiv = substr($fiv,0,strlen($fiv)-1);
	if(strlen($fu)>0 || strlen($fif) > 0){
		if($id > 0 && $id < 9999900){
			$findID = getDataQry("SELECT $idName FROM $tbl where $idName='$id'", $connOK);
			if(count($findID) > 0){
				$act = "update";
			} else {
				$fif = $idName . ", " . $fif;
				$fiv = "'" . $id . "', " . $fiv;
				$act = "insert";
				
			}
		} else {
			$act = "insert";
		}
		if($act == "update"){
			$qryUpdate = "UPDATE $tbl set $fu where $idName='$id'";
			if($debug == 1){
				echo $qryUpdate;
			} else {
				$sql = mysqli_query($connOK, $qryUpdate) or die( failQuery( $qryUpdate . '<hr>' . mysqli_error($connOK) ) );
			}
		} else {
			$qryInsert = "INSERT INTO $tbl ($fif) VALUES ($fiv)";
			if($debug == 1){
				echo $qryInsert;
			} else {
				$sql = mysqli_query($connOK, $qryInsert) or die( failQuery( $qryInsert . '<hr>' . mysqli_error($connOK) ) );
				$id = mysqli_insert_id($connOK);
			}
		}
		return $id;
	} else {
		$alert .= "VALORES DE INSERCAO OU UPDATE INCOMPLETOS";
		return false;
	}
}

function deleteDataQry($tbl, $id_key, $id_val, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	if(!empty($tbl) && !empty($id_key) && !empty($id_val)){
		$rm = mysqli_query($connOK, "delete from $tbl where $id_key='$id_val'") or die( failQuery( $qryInsert . '<hr>' . mysqli_error($connOK) ) );
		return true;
	} else {
		return false;
	}
}

function execQry($qry, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	if(!empty($qry)){
		$exec = mysqli_query($connOK, $qry) or die( failQuery( $qry . '<hr>' . mysqli_error($connOK) ) );
		return true;
	} else {
		return false;
	}
}
?>