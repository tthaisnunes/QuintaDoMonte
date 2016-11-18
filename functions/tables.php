<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

// NOMES DAS TABLES PARA USO NO SISTEMA
$td_agenda = $bd["prefix"] . "_Agenda";
$fields_Agenda_Type = array(
	"Id_Agenda"=>tc("sind",6),
	"Name_Agenda"=>tc("vc",255),
	"Email_Agenda"=>tc("vc",255),
	"Telefone_Agenda"=>tc("vc",50),
	"Dia_Agenda"=>tc("vc",10),
	"Periodo_Agenda"=>tc("vc",10),
	"Date_Insert_Agenda"=>tc("dt",""),
	"FA_Agenda"=>tc("ti",4)
);
$fields_Agenda = setTableFields($td_agenda, $fields_Agenda_Type, $_GET["uriA"], $_GET["uriB"], "print");


function createTable($table, $fieldType, $primary, $identity, $obConn=false){
	global $conn;
	if($obConn){ $connOK = $obConn; } else { $connOK = $conn; }
	$alert = "";
	if(empty($table) || !is_array($fieldType)){
		$alert .= "TABLE INEXISTENTE (" . $table . ") OU CAMPOS NÃO DETERMINADOS JÁ EXISTE<hr>";
	} else {
		if(mysqli_num_rows(mysqli_query($connOK, "SHOW TABLES LIKE '".$table."'"))){
			$linhaTable = getDataQry("SHOW COLUMNS FROM $table");
			$camposTableBD = array();
			foreach($linhaTable as $k_T => $arr_T){
				array_push($camposTableBD, $arr_T["Field"]);
			}
			$camposTableType = array();
			foreach($fieldType as $k_TT => $v_TT){
				array_push($camposTableType, $k_TT);
			}
			if($camposTableBD != $camposTableType){
				echo "TABELA $table DIFERENTE<hr>";
				$tableT = $table . '_' . date("YmdHis");
				execQry("RENAME TABLE  $table TO $tableT");
				setTableFields($table, $fieldType, "installbd", "proceed");
				$fieldsGet = array();
				foreach($camposTableBD as $cT){
					if(in_array($cT, $camposTableType)){
						array_push($fieldsGet, $cT);
					}
				}
				$fieldsIns = implode(",", $fieldsGet);
				$queryT = "INSERT INTO " . $table . " (" . $fieldsIns . ") SELECT " . $fieldsIns . " from " . $tableT;
				execQry($queryT);
				execQry("DROP TABLE " . $tableT);
			}
			
			
			$alert .= "TABLE " . $table . " JÁ EXISTE<hr>";
		} else {
			$alert .= "CRIAÇAO DE TABELA $table:<br>";
			$query = "CREATE TABLE IF NOT EXISTS $table (";
			foreach($fieldType as $field => $type){
				$query .= "$field $type";
				if($identity == $field){ $query .= " auto_increment"; }
				$query .= ",";
			}
			if(strlen($primary) > 0){ $query .= "PRIMARY KEY  ($primary)"; } else { $query = substr($query,0,strlen($query)-1); }
			//$query .= ") ENGINE=INNODB;";
			//$query .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
			//$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			$query .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
			//$query .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
			if(!empty($table)) $go = mysqli_query($connOK, $query) or die(mysqli_error($connOK));
			$alert .= $query . "<hr>";
		}
	}
	return $alert;
}

function tc($t,$c){
	if($t == "vc"){ return "varchar($c) NOT NULL default ''"; }
	if($t == "si"){ return "smallint($c) NOT NULL default '0'"; }
	if($t == "sind"){ return "smallint($c) NOT NULL"; }
	if($t == "ind"){ return "int($c) NOT NULL"; }
	if($t == "i"){ return "int($c) NOT NULL"; }
	if($t == "ti"){ return "tinyint($c) NOT NULL default '0'"; }
	if($t == "tind"){ return "tinyint($c) NOT NULL"; }
	if($t == "d"){ return "date NULL"; }
	if($t == "dt"){ return "datetime NULL"; }
	if($t == "db"){ return "DOUBLE(".(strlen($c)>0?$c:" 12, 2 ").") NOT NULL default '0.00'"; }
	if($t == "mt"){ return "MEDIUMTEXT NULL"; }
	if($t == "t"){ return "TEXT NULL"; }
}


$alertInstall = "";
function setTableFields($table, $camposType, $i="", $p="", $echo=""){
	global $alertInstall;
	$campos = array();
	$IdPrimary = "";
	foreach($camposType as $kk => $vv){
		$campos[] = $kk;
		if(empty($IdPrimary)){ $IdPrimary = $kk; }
	}
	if($i == "installbd" && $p == "proceed"){
		$alertInstall .= "INTALAÇÃO DE TABELA: " . $table . ":<br>";
		$alertInstall .= createTable($table, $camposType, $IdPrimary, $IdPrimary);
	}
	if($echo == "print"){
		echo utf8_decode($alertInstall); $alertInstall = "";
	}
	return $campos;
}

if($_GET["uriA"] == "listTables"){
	echo '<h1>LISTA E TABELAS EM BD</h1>';
	$tablesInBd = getTablesInBd();
	foreach($tablesInBd as $tables){
		echo "<div>$tables</div>";
	}
}
?>