<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/db.php');
require('./lib/tools.php');
require('./lib/connexion.php');
/*********************************************************************************************************/
/* P A G E   Q U I   R E C A L C U L E   T O U S   L E S   L I B E L L E S   U N I Q U E S   E N   B D D */

/* Q U E S T I O N S */
$db_Obj = BDD_Connection();
$SQL_txt= 'SELECT idQuestion, lbQuestion FROM question ORDER BY 1';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute();
$Q_Recordset= $Query->fetchAll();
$Query->closeCursor();
foreach ($Q_Recordset as $Record)   {
	$idQuestion = trim($Record[0]);;
	$lbQuestion = trim($Record[1]);
	$lbQuestion = cleanString($lbQuestion);
	$strictLbQuestion = formatUniqueString($lbQuestion);
	$Query = $db_Obj->prepare('UPDATE question set strictLbQuestion=:strictLbQuestion where idQuestion=:idQuestion');
	$Query ->execute(array (':idQuestion' => $idQuestion, ':strictLbQuestion' => $strictLbQuestion )); 
}

$Query = $db_Obj->prepare('UPDATE question set cdLbQuestion=soundex(strictLbQuestion), nbMotReponse=(length(lbReponse) - length(replace(lbReponse," ",\'\'))+1)');
$Query ->execute(); 

/* T E S T */
$db_Obj = BDD_Connection();
$SQL_txt= 'SELECT idTest, lbTest FROM test ORDER BY 1';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute();
$Q_Recordset= $Query->fetchAll();
$Query->closeCursor();
foreach ($Q_Recordset as $Record)   {	
	$idTest = trim($Record[0]);;
	$lbTest = trim($Record[1]);
	$lbTest = cleanString($lbTest);
	$strictLbTest = formatUniqueString($lbTest);
	$Query = $db_Obj->prepare('UPDATE test set strictLbTest=:strictLbTest where idTest=:idTest');
	$Query ->execute(array (':idTest' => $idTest, ':strictLbTest' => $strictLbTest )); 
}
$Query->closeCursor();

/* A B B R E V I A T I O N */
//UPDATE `question` set lbReponse=REPLACE (lbReponse, 'surface de réparation', 'SDR')
unset($db_Obj);
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Mise à jour BDD</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<div class="alert alert-success">Mise à jour de la BDD effectuée avec succés!</div>
</div>
</body>
</html>