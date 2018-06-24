<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Tright'],$T_right["0"]["UPGRADABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$lbTest		= GetPOST('lbTest');
$dtTest		= GetPOST('dtTest');
$idNiveau	= GetPOST('idNiveau');
$dtTest	= Format_Date($dtTest, "JJ/MM/AAAA", "", "AAAA-MM-JJ");
$lbTest 	= cleanString($lbTest);
$strictlbTest = formatUniqueString($lbTest);
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* V E R I F I C A T I O N   D E   L ' U N I C I T E   D U   T E S T */
$SQL_txt= 'SELECT idTest FROM test WHERE strictlbTest like :strictlbTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':strictlbTest' => '%'.$strictlbTest.'%'));
$resultat = $Query -> fetch ();
if ($resultat!='')   {
	unset($db_Obj);
	header('Location: T_report.php?action=DBL&idtest='.$resultat[0]);
	die();	
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare('INSERT INTO test (lbTest, strictlbTest, dtTest, idStatut, idNiveau) VALUES (:lbTest, :strictlbTest, :dtTest, :idStatut, :idNiveau)');
	$Query ->execute(array (':lbTest' => $lbTest, ':strictlbTest' => $strictlbTest, ':dtTest' => $dtTest, ':idStatut' => $Tstatus_creation, ':idNiveau' => $idNiveau  )); 
	$idTest = $db_Obj->lastInsertId();
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	die("Données non enregistrées<br>TECHNICAL ERROR : ".$error->getMessage());
}
$db_Obj->commit();
$Query->closeCursor();
/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 12, 'Test n°'.$idTest);
/* F I N   L O G   U  T I L I S A T E U R  */
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

header('Location: T_report.php?action=OK&idtest='.$idTest); // Redirection sur la page d'information des traitements
die();
?>