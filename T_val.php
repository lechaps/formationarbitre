<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idTest		= GetGET('idtest');
$val		= GetGET('val');
/* F I N   D U   C H A R G E M E N T */
$valpossible = array (1, 2, 3);
if (!in_array($val, $valpossible)) die("UNKNOWN VALIDATION PARAMETER");

$db_Obj = BDD_Connection();
/* V E R I F I C A T I O N   D U   N O M B R E   D E   Q U E S T I O N   L I E E   A   C E   T E S T */
$SQL_txt= 'SELECT count(*) FROM  question_test WHERE idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$resultat = $Query -> fetch();
if ($resultat[0]<$minNbQvalTest)   {
	unset($db_Obj);
	die('ERROR : INVALID NUMBER OF QUESTION ('.$resultat[0].' INSTEAD OF '.$minNbQvalTest.')');	
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* V E R I F I C A T I O N   D U   T E S T */
$SQL_txt= 'SELECT idTest, idStatut FROM test WHERE idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest ));
$resultat = $Query -> fetch ();
if ($resultat=='')   {
	unset($db_Obj);
	die('ERROR : INVALID TEST');	
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* C H A R G E M E N T   D E S   D R O I T S */
if (($resultat[1]<$val) && (!in_array($_SESSION['Tright'],$T_right[$resultat[1]]["DOWNGRADABLE"]))) die("UNAUTHORIZED ACCESS");
if (($resultat[1]>$val) && (!in_array($_SESSION['Tright'],$T_right[$resultat[1]]["UPGRADABLE"]))) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare('UPDATE test SET idStatut=:val WHERE idTest=:idTest');
	$Query ->execute(array (':idTest' => $idTest, ':val' => $val)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	die("Données non enregistrées<br>TECHNICAL ERROR : ".$error->getMessage());
}
$db_Obj->commit();
$Query->closeCursor();
/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 14, 'Test n°'.$idTest.' - statut : '.$resultat[1].'=>'.$val);
/* F I N   L O G   U  T I L I S A T E U R  */
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

header('Location: T_search.php');// Redirection sur la page d'information des traitements
die();
?>