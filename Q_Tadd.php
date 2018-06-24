<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idQuestion	= GetPOST('idquestion');
$idTest		= GetPOST('idtest');
$note		= GetPOST('note');
$notation	= GetPOST('notation');
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* V E R I F I C A T I O N   D E   L ' U N I C I T E   D E   L A   L I A I S O N */
$SQL_txt= 'SELECT idQuestion FROM question_test WHERE idQuestion=:idQuestion AND idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion, ':idTest' => $idTest ));
$resultat = $Query -> fetch ();
if ($resultat!='')   {
	unset($db_Obj);
	echo json_encode(array('result' => 'ERR', 'message' => 'LINK ALREADY EXISTS'));
	die();
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* V E R I F I C A T I O N   D U   S T A T U T   D E   L A   Q U E S T I O N */
$SQL_txt= 'SELECT idQuestion FROM question WHERE idQuestion=:idQuestion AND idStatut!=1';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion));
$resultat = $Query -> fetch ();
if ($resultat!='')   {
	unset($db_Obj);
	echo json_encode(array('result' => 'ERR', 'message' => 'INVALID QUESTION STATUS'));
	die();
}
/* F I N   D E   L A   V E R I F I C A T I O N */

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
//Enregistrement si question déjà présente
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare('INSERT INTO question_test (idQuestion, idTest, noteReponse, notationReponse, ordreQuestion) SELECT  :idQuestion, T.idTest, :note, :notation, coalesce(max(ordreQuestion),0)+1 from test T
 LEFT OUTER JOIN question_test QT on T.idTest=QT.idTest WHERE T.idTest=:idTest and noteReponse<=:note GROUP BY T.idTest');
	$Query ->execute(array (':idQuestion' => $idQuestion, ':idTest' => $idTest, ':note' => $note, ':notation' => $notation)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	echo json_encode(array('result' => 'ERR', 'message' =>$error->getMessage()));
	unset($db_Obj);
	die();
}
//Enregistrement si première question du Test
if ($Query ->rowCount()==0)   {
	try {
		$Query = $db_Obj->prepare('INSERT INTO question_test (idQuestion, idTest, noteReponse, notationReponse, ordreQuestion) SELECT  :idQuestion, T.idTest, :note, :notation, coalesce(max(ordreQuestion),0)+1 from test T
 LEFT OUTER JOIN question_test QT on T.idTest=QT.idTest WHERE T.idTest=:idTest GROUP BY T.idTest');
		$Query ->execute(array (':idQuestion' => $idQuestion, ':idTest' => $idTest, ':note' => $note, ':notation' => $notation)); 
	}
	catch(PDOException $error)   {
		$db_Obj->rollBack();
		echo json_encode(array('result' => 'ERR', 'message' =>$error->getMessage()));
		unset($db_Obj);
		die();
	}
}
try {
	$Query = $db_Obj->prepare('UPDATE question_test SET ordreQuestion=ordreQuestion+1 WHERE idTest=:idTest AND noteReponse>:note');
	$Query ->execute(array (':idTest' => $idTest, ':note' => $note)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	echo json_encode(array('result' => 'ERR', 'message' =>$error->getMessage()));
	unset($db_Obj);
	die();
}
$db_Obj->commit();
$Query->closeCursor();
/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 8, 'Question n°'.$idQuestion.' => Test n°'.$idTest);
/* F I N   L O G   U  T I L I S A T E U R  */
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

echo json_encode(array('result' => 'TAD', 'message' => ''));
die();
?>