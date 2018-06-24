<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$lbQuestion	= GetPOSTorDEFAULT('lbQuestion', '');
//$idNiveau	= GetPOSTorDEFAULT('idNiveau', '');  en ATTENTE DE CONSTRUCTION fonctionnalité difficulté
$noteReponse= GetPOSTorDEFAULT('noteReponse', '');
$idLoi		= GetPOSTorDEFAULT('idLoi', array());
$idTest		= GetPOSTorDEFAULT('idTest', '');
$idStatut	= GetPOSTorDEFAULT('idStatut', '');
$idtypeq	= GetPOSTorDEFAULT('idtypeq', '');
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   L O I S   e t   N I V E A U X */
$Loi_Recordset 		= Load_BDDParam($db_Obj, 'Loi');
$Statut_Recordset	= Load_BDDParam($db_Obj, 'Statut');
$TypeQ_Recordset	= Load_BDDParam($db_Obj, 'TypeQ');
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   T E S T S */
$SQL_txt			= 'SELECT idTest, lbTest FROM test where idStatut=1 ORDER BY lbtest';
$Query 				= $db_Obj->query($SQL_txt);
$T_Recordset		= $Query->fetchAll();
/* F I N   D U   C H A R G E M E N T */

/* P R E P A R A T I O N   E T   L A N C E M E N T   D E   L A   R E C H E R C H E */
$sql		= array();
$paramArray	= array();
if(!empty($lbQuestion)) {
	$sql[]			= 'strictLbQuestion like ?';
	$cdlbQuestion	= formatUniqueString(cleanString($lbQuestion));
    $paramArray[]	= '%'.$cdlbQuestion.'%';
}
/*if(!empty($idNiveau)) {  en ATTENTE DE CONSTRUCTION fonctionnalité difficulté
	$sql[]			= 'nbMotReponse=?';
    $paramArray[]	= $idNiveau;
}*/
if(!empty($idStatut)) {
	$sql[]			= 'idStatut=?';
    $paramArray[]	= $idStatut;
}
if(!empty($noteReponse)) {
	if ($noteReponse=='NOT')
		$sql[]		= ' NOT EXISTS(SELECT 1 FROM question_test QTT WHERE Q.idQuestion=QTT.idQuestion /* ? */)';
	else
		$sql[]		= ' EXISTS(SELECT 1 FROM question_test QTT WHERE Q.idQuestion=QTT.idQuestion and QTT.noteReponse=?)';
    $paramArray[]	= $noteReponse;
}
if(!empty($idLoi)) {
	foreach($idLoi as $Loi)   {
		$sql[]			= ' EXISTS(SELECT 1 FROM question_loi QL WHERE QL.idQuestion=Q.idQuestion and QL.idLoi in (?))';
		$paramArray[]	= $Loi;
	}
}
if(!empty($idTest)) {
	$sql[]			= ' EXISTS(SELECT 1 FROM question_test QTT WHERE Q.idQuestion=QTT.idQuestion and QTT.idTest=?)';
	$paramArray[]	= $idTest;
}
if(!empty($idtypeq)) {
	$sql[]			= ' typequestion=?';
	$paramArray[]	= $idtypeq;
}

$SQL_txt	= 'SELECT Q.idQuestion, Q.lbQuestion, Q.idStatut, Q.nbMotReponse, count(distinct QT.idTest) FROM question Q 
LEFT OUTER JOIN question_test QT on QT.idQuestion=Q.idQuestion';
$SQL_txt	=$SQL_txt.(count($paramArray)>0 ? ' WHERE '.join(' AND ',$sql) : '').' GROUP BY Q.idQuestion, Q.lbQuestion, Q.idStatut, Q.nbMotReponse ORDER BY 1, 2, 3, 4, 5';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute($paramArray);
$Q_Recordset = $Query->fetchAll();
$NbQ = $Query->rowCount();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D E   L A   R E C H E R C H E */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Recherche d'une question</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
	<script src="Q_modal.js"></script>
	<script language="javascript">
	var CssStatut_tab = <?php echo json_encode($status_array) ?>;
	var LbStatut_tab  = <?php echo json_encode($Statut_Recordset) ?>;	
	</script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<form class="well form-horizontal" method="POST" action="Q_search.php">
		<fieldset>
			<legend>Recherche d'une question</legend>
			<div class="form-group">
				<label class="col-md-2 control-label" for="lbQuestion">Libellé de la question</label>  
				<div class="col-md-10">                     
					<textarea class="form-control" id="lbQuestion" name="lbQuestion" rows="4" placeholder="..."><?php echo $lbQuestion?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="idtypeq">Type de question : </label>
				<div class="col-md-10">
					<select id="idtypeq" name="idtypeq" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($TypeQ_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $idtypeq, "SELECTED").'>'.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="lois">Lois du Jeu : </label>
				<?php
				$i=0;
				$compteur=0;
				foreach ($Loi_Recordset as $Record)	{
					echo "\t\t\t\t\t".'<div class="col-md-2"><input type="checkbox" name="idLoi[]" value="'.$Record[0].'"';	
					if (count($idLoi)>$compteur && $idLoi[$compteur]==$Record[0])   {
						$compteur++;
						echo "CHECKED";
					}
					echo '/>&nbsp;'.$Record[1].'</div>'."\n";	
					$i+=1;
					if ($i % 5 == 0) echo "\t\t\t".'</div>'."\n"."\t\t\t".'<div class="form-group"><label class="col-md-2 control-label" for=""></label>'."\n";
					}?>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="idTest">Questionnaire : </label>
				<div class="col-md-10">
					<select id="idTest" name="idTest" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($T_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $idTest, "SELECTED").'>'.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="id_level">Difficulté : </label>
				<div class="col-md-10">
					<select id="id_level" name="id_level" class="form-control">
						<option value=''>Tous</option>
						<option value=''>Fonctionnalité en cours de développement</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="noteReponse">Note : </label>
				<div class="col-md-10">
					<select id="noteReponse" name="noteReponse" class="form-control">
						<option value="">Choisissez la note de base</option>
						<option value="NOT" <?php echo ifEncapsulator("NOT", $noteReponse, "SELECTED")?>>0 - Non encore attribuée dans un questionnaire</option>
						<option value="1" <?php echo ifEncapsulator("1", $noteReponse, "SELECTED")?>>1</option>
						<option value="2" <?php echo ifEncapsulator("2", $noteReponse, "SELECTED")?>>2</option>
						<option value="3" <?php echo ifEncapsulator("3", $noteReponse, "SELECTED")?>>3</option>
						<option value="4" <?php echo ifEncapsulator("4", $noteReponse, "SELECTED")?>>4</option>
						<option value="5" <?php echo ifEncapsulator("5", $noteReponse, "SELECTED")?>>5</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="idStatut">Statut : </label>
				<div class="col-md-10">
					<select id="idStatut" name="idStatut" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($Statut_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $idStatut, "SELECTED").'>'.$Record[0].' - '.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-5 control-label" for="button1id"></label>
				<div class="col-md-7">
					<button id="button1id" name="button1id" class="btn btn-success" type="submit">Rechercher</button>
					<button id="button2id" name="button2id" class="btn btn-danger" type="reset">Annuler</button>
				</div>
			</div>
		</fieldset>
	</form>
	<div class="panel panel-default">
		<div class="panel-heading">Résultat de la recherche : <b><?php echo $NbQ ?></b> Questions</div>
		<table class="table table-hover">
			<thead><tr><th>#</th><th></th><th>Libellé</th><th>Difficulté</th><th>Test</th><th>Actions</th></tr></thead>
		<tbody>
		<?php foreach ($Q_Recordset as $Record)   {
			//Q.idQuestion, lbQuestion, idStatut, lbStatut,Nb
			$action		='<div class="btn-group">';
			if (in_array($_SESSION['Qright'],$Q_right[$Record[2]]["PRINTABLE"])) {
				$action.='<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"  href="Q_modal.php?idquestion='.$Record[0].'" data-target="#myModal"
				rel="'.$Record[0].'" Title="Détail de la question"><span class="glyphicon glyphicon-eye-open"></span></button>';
			}
			$action.='</div>';
			echo '<tr id="Q'.$Record[0].'_Line"><td>'.$Record[0].'</td><td><span class="label label-'.$status_array[$Record[2]].'" id="Q'.$Record[0].'_Status">'.$Statut_Recordset[$Record[2]][1].'</span></td><td>'.$Record[1].'</td>
			<td><small>'.strval(getDifficult($Record[3], $Qlevel)).'%</small></td>
			<td><span class="badge"><div id="nbTest'.$Record[0].'" name="nbTest'.$Record[0].'">'.$Record[4].'</div></span></td><td>'.$action.'</td></tr>';
		}
		?>
		</tbody>
		</table>
	</div>
	<div id="myModal" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg"><div class="modal-content"></div></div>
	</div>
	<script>
	 $('#myModal').on('hide.bs.modal', function () {
            $(this).removeData('bs.modal');
        });
    </script>
</div>
</body>
</html>
