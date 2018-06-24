<?php
/**********************************************************************************/
// C O N N E C T I O N   A   L A   B D D 
function BDD_Connection()   {
	try {
		$strConnection 	= 'mysql:host=127.0.0.1;dbname=lespeign_GE'; 
		$arrExtraParam	= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); //alternative de $pdo->query("SET NAMES 'utf8'"); 
		$pdo 			= new PDO($strConnection, 'DB_USER', 'DB_PASSWORD', $arrExtraParam);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		$msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
		die($msg);
	}
	return $pdo;
}

/**********************************************************************************/
// C H A R G E M E N T   D ' U N   P A R A M   B D D
function Load_BDDParam($C, $T)   {
	switch ($T)   {
		case 'Niveau' :
			global $niveau_array;
			$S= 'SELECT idNiveau, lbNiveau FROM niveau WHERE idNiveau in ('.implode(',', array_keys($niveau_array)).')';
			break;
		case 'Statut' :
			global $status_array;
			$S= 'SELECT idStatut, lbStatut FROM statut WHERE idStatut in ('.implode(',', array_keys($status_array)).')';
			break;
		case 'Loi' :
			$S= 'SELECT idLoi, lbLoi FROM loi';
			break;
		case 'Droit' :
			global $droit_array;
			$S= 'SELECT idDroit, lbDroit FROM droit WHERE idDroit in ('.implode(',', array_keys($droit_array)).')';
			break;
		case 'TypeQ' :
			global $droit_array;
			$S= 'SELECT idtypequestion, lbtypequestion FROM typequestion';
			break;
		default : 
			die('UNKNOW PARAM TO LOAD');
	}

	$Q= $C->query($S);
	$R= $Q->fetchAll();
	$Q->closeCursor();
	return $R;
}

/**********************************************************************************/
// E N R E G I S T R E M E N T   D ' U N   E V E N E M E N T   U T I L I S A T E U R
function User_event($C, $E, $Lb=null)   {
	/*Positionnement du N°Utilisateur*/
	if (!isset($_SESSION['idUtilisateur'] ))
		$U=0;
	else
		$U=$_SESSION['idUtilisateur'];
	try {
		$Query = $C->prepare('INSERT INTO utilisateur_evenement (idUtilisateur, IPadress, idEvenement, lbUE, dtUE) VALUES (:idUtilisateur, :IPadress, :idEvenement, :lbUE, :dtUE)');	
		$Query ->execute(array (':idUtilisateur' => $U, ':IPadress' => $_SERVER["REMOTE_ADDR"], ':idEvenement' => $E, ':lbUE' => $Lb, ':dtUE' => date("Y-m-d H:i:s") )); 
		}
	catch(PDOException $error)   {
		$Query->closeCursor();
		die("LOG ERROR");
	}
	$Query->closeCursor();
}
?>