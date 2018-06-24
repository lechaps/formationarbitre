<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/db.php');
require('./lib/connexion.php');
require('./lib/tools.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Tright'],$T_right["0"]["UPGRADABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$action	= GetGET('action');
$idTest	= GetGET('idtest');
switch ($action)   {
	case "OK" : 
		$message	= 'Le questionnaire est enregistré  (Id #'.$idTest.'), voici le récaputilatif des informations saisies';
		$style		= 'success';
		break;
	case "DBL" : 
		$message	= 'Le questionnaire N\'EST PAS enregistré, il est considéré doublon du questionnaire (Id #'.$idTest.') dont voici les informations';
		$style		= 'danger';
		break;
}
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   N I V E A U X */
$N_Recordset = Load_BDDParam($db_Obj, 'Niveau');
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D U   T E S T */
$SQL_txt= 'SELECT lbTest, dtTest, idNiveau FROM test T WHERE T.idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$T_recordset = $Query -> fetch ();
$Query->closeCursor();
unset($db_Obj);
/* F I N   C H A R G E M E N T*/;
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Information d'un questionnaire</title>
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
	<div class="alert alert-<?php echo $style?>">
		<strong><?php echo $message ?></strong>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo nl2br($T_recordset[0])?></div>
		<div class="panel-body"></div>
		<table class="table">
			<thead>
				<tr><th>Date d'échéance</th><th>Niveau</th></tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $T_recordset[1]?></td>
					<td><span class="label label-<?php echo $niveau_array[$T_recordset[2]]?>"><?php echo $N_Recordset[$T_recordset[2]][1] ?></span></td>
				</tr>
			</tbody>
			
		</table>
	</div>
</div>
</body>
</html>