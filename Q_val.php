<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idQuestion	= GetPOST('idquestion');
$val		= GetPOST('val');
/* F I N   D U   C H A R G E M E N T */
$valpossible = array (1, 2, 3);
if (!in_array($val, $valpossible)) die(json_encode(array('result' => 'ERR', 'message' =>'UNKNOWN VALIDATION PARAMETER')));

$db_Obj = BDD_Connection();
/* V E R I F I C A T I O N   D E   L A   Q U E S T I O N */
$SQL_txt= 'SELECT idQuestion, idStatut FROM question WHERE idQuestion=:idQuestion';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion ));
$resultat = $Query -> fetch ();
if ($resultat=='')   {
	unset($db_Obj);
	echo json_encode(array('result' => 'ERR', 'message' =>'INVALID TEST'));
	die();	
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* C H A R G E M E N T   D E S   D R O I T S */
if (($resultat[1]<$val) && (!in_array($_SESSION['Qright'],$Q_right[$resultat[1]]["DOWNGRADABLE"]))) die("UNAUTHORIZED ACCESS");
if (($resultat[1]>$val) && (!in_array($_SESSION['Qright'],$Q_right[$resultat[1]]["UPGRADABLE"]))) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare('UPDATE question SET idStatut=:val WHERE idQuestion=:idQuestion');
	$Query ->execute(array (':idQuestion' => $idQuestion, ':val' => $val)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	echo json_encode(array('result' => 'ERR', 'message' =>$error->getMessage()));
	die();
}
$db_Obj->commit();
$Query->closeCursor();

/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 7, 'Question n°'.$idQuestion.' - statut : '.$resultat[1].'=>'.$val);
/* F I N   L O G   U  T I L I S A T E U R  */

unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

echo json_encode(array('result' => 'QV'.$val, 'message' => ''));
die();

?>