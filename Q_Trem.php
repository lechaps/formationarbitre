<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idQuestion	= GetPOST('idquestion');
$idTest		= GetPOST('idtest');
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();

/* V E R I F I C A T I O N   D U   T E S T */
$SQL_txt= 'SELECT idTest, idStatut FROM test WHERE idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest ));
$resultat = $Query -> fetch ();
if ($resultat=='')   {
	unset($db_Obj);
	echo json_encode(array('result' => 'ERR', 'message' => 'INVALID TEST'));
	die();	
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Tright'],$T_right[$resultat[1]]["MOVEQUESTION"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare('UPDATE question_test QT SET ordreQuestion=ordreQuestion-1 WHERE idTest=:idTest AND ordreQuestion>= (SELECT * FROM(SELECT ordreQuestion FROM question_test Q WHERE Q.idQuestion=:idQuestion AND Q.idTest=:idTest) as SSQ) ');
	$Query ->execute(array (':idTest' => $idTest, ':idQuestion' => $idQuestion)); 
	$Query = $db_Obj->prepare('DELETE FROM question_test WHERE idQuestion=:idQuestion AND idTest=:idTest');
	$Query ->execute(array (':idQuestion' => $idQuestion, ':idTest' => $idTest)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	echo json_encode(array('result' => 'ERR', 'message' =>$error->getMessage()));
	die();
}
$db_Obj->commit();
$Query->closeCursor();
/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 9, 'Question n°'.$idQuestion.' => Test n°'.$idTest);
/* F I N   L O G   U  T I L I S A T E U R  */
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

echo json_encode(array('result' => 'TRE', 'message' => ''));
die();
?>