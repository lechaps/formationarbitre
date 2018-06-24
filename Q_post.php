<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Qright'],$Q_right["0"]["UPGRADABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* A U T O - V A L I D A T I O N   D E S   Q U E S T I O N S   P O U R   L E S   A D M I N S  */
if (in_array($_SESSION['Qright'],$Q_right["2"]["UPGRADABLE"])) $Qstatus_creation=1;
/* F I N */

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$lbQuestion		= GetPOST('lbQuestion');
$lbReponse		= GetPOST('lbReponse');
$source			= GetPOST('source');
$idtypequestion	= GetPOST('idtypequestion');
$lbQuestion 	= cleanString($lbQuestion);
$lbReponse 	    = cleanString($lbReponse);
$source 	    = cleanString($source);
$strictLbQuestion = formatUniqueString($lbQuestion);
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* V E R I F I C A T I O N   D E   L ' U N I C I T E   D E   L A   Q U E S T I O N */
$SQL_txt= 'SELECT soundex(:strictLbQuestion)';			//extraction de la sonorité
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':strictLbQuestion' => $strictLbQuestion));
$resultat = $Query -> fetch ();
$cdLbQuestion = substr(trim($resultat[0]), 1);			//retrait de la première lettre (utile pour déterminer le rapprochement lors de portion de phrase
$SQL_txt= 'SELECT idQuestion FROM question WHERE cdLbQuestion like :cdLbQuestion and typequestion=:idtypequestion'; //recherche de proximité de phrase
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':cdLbQuestion' => '%'.$cdLbQuestion.'%', ':idtypequestion' => $idtypequestion));
$resultat = $Query -> fetch ();
if ($resultat!='')   {
	unset($db_Obj);
	echo json_encode(array('result' => 'DBL', 'idquestion' =>$resultat[0], 'message' =>'' ));
	die();	
}
/* F I N   D E   L A   V E R I F I C A T I O N */

/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare('INSERT INTO question (lbQuestion, strictLbQuestion, cdLbQuestion, idStatut, lbReponse, nbMotReponse, dtReponseMaj, source, typequestion) 
	VALUES (:lbQuestion, :strictLbQuestion, soundex(strictLbQuestion), :idStatut, :lbReponse, (length(lbReponse) - length(replace(lbReponse," ",\'\'))+1) , :dtReponseMaj, :source, :idtypequestion)');
	$Query ->execute(array (':lbQuestion' => $lbQuestion, ':strictLbQuestion' => $strictLbQuestion, ':idStatut' => $Qstatus_creation, ':lbReponse' => $lbReponse, ':dtReponseMaj' => date("Y-m-d H:i:s"), ':source' => $source, ':idtypequestion' => $idtypequestion )); 
	$idQuestion = $db_Obj->lastInsertId();
	
	$Query = $db_Obj->prepare('INSERT INTO question_loi (idQuestion, idLoi) VALUES (:idQuestion, :idLoi)');
	if(isset($_POST['idLoi']))
		foreach($_POST['idLoi'] as $idLoi)
			$Query ->execute(array (':idQuestion' => $idQuestion, ':idLoi' => $idLoi)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	$Query->closeCursor();
	echo json_encode(array('result' => 'ERR', 'idquestion'=> "0", 'message' =>$error->getMessage() ));
	die();
}
$db_Obj->commit();
$Query->closeCursor();
/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 11, 'Question n°'.$idQuestion);
/* F I N   L O G   U  T I L I S A T E U R  */
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

echo json_encode(array('result' => 'SAV', 'idquestion' =>$idQuestion, 'message' => '' ));
die();
?>