<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$lbTest		= GetPOSTorDEFAULT('lbTest', '');
$idNiveau	= GetPOSTorDEFAULT('idNiveau', '');
$dtTest1	= GetPOSTorDEFAULT('dtTest1', '');
$dtTest2	= GetPOSTorDEFAULT('dtTest2', '');
$dt1		= Format_Date($dtTest1, 'JJ/MM/AAAA', '', 'AAAA-MM-JJ');
$dt2		= Format_Date($dtTest2, 'JJ/MM/AAAA', '', 'AAAA-MM-JJ');
/* F I N   D U   C H A R G E M E N T*/

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   N I V E A U X   E T   S T A T U T S */
$N_Recordset = Load_BDDParam($db_Obj, 'Niveau');
$S_Recordset = Load_BDDParam($db_Obj, 'Statut');
/* F I N   D U   C H A R G E M E N T */

/* P R E P A R A T I O N   E T   L A N C E M E N T   D E   L A   R E C H E R C H E */
$sql		= array();
$paramArray	= array();
if(!empty($lbTest)) {
	$sql[]			= 'lbTest like ?';
    $paramArray[]	= '%'.$lbTest.'%';
}
if(!empty($idNiveau)) {
	$sql[]			= 'T.idNiveau=?';
    $paramArray[]	= $idNiveau;
}
if(!empty($dt1)) {
	$sql[]			= 'T.dtTest>=?';
    $paramArray[]	= $dt1;
}
if(!empty($dt2)) {
	$sql[]			= 'T.dtTest<=?';
    $paramArray[]	= $dt2;
}
$SQL_txt	= 'SELECT T.idTest, lbTest, dtTest, T.idStatut, T.idNiveau, count(distinct QT.idQuestion) as NbQ
			FROM test T
			LEFT OUTER JOIN question_test QT on QT.idTest=T.idTest';
$Query 	= $db_Obj -> prepare($SQL_txt.(count($paramArray)>0 ? ' WHERE '.join(' AND ',$sql) : '').' GROUP BY T.idTest, lbTest, dtTest, idStatut, T.idNiveau ORDER BY  3 desc, 2, 1, 4, 5, 6;');
$Query ->execute($paramArray);
$Test_Recordset		= $Query->fetchAll();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D E   L A   R E C H E R C H E */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Recherche d'un questionnaire</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<link href="<?php echo $css_dp_link;?>" rel="stylesheet">
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
	<script src="<?php echo $js_mm_link;?>"></script>
	<script src="<?php echo $js_dp_link;?>"></script>
	<script src="<?php echo $js_dp_lg;?>"></script>
	<script type="text/javascript">
		$(function () {
			$('#datetimepicker1').datetimepicker({
				language: 'fr',
				pickTime: false,
				endDate: new Date(<?php if ($dtTest2!='') echo "'".$dt2."'";?>)
			});
			$('#datetimepicker2').datetimepicker({
				language: 'fr',
				pickTime: false,
				startDate: new Date(<?php if ($dtTest1!='') echo "'".$dt1."'";?>)
			});
			$("#datetimepicker1").on("change.dp",function (e) {
				$('#datetimepicker2').data("DateTimePicker").setStartDate(e.date);
			});
			$("#datetimepicker2").on("change.dp",function (e) {
				$('#datetimepicker1').data("DateTimePicker").setEndDate(e.date);
			});
		});
	</script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<form class="well form-horizontal" method="POST" action="T_search.php">
		<fieldset>
			<legend>Recherche d'un questionnaire</legend>
			<div class="form-group">
				<label class="col-md-4 control-label" for="lbTest">Titre du Questionnaire</label>  
				<div class="col-md-4">
					<input id="lbTest" name="lbTest" placeholder="Titre du questionnaire" class="form-control input-md" type="text" value="<?php echo $lbTest?>" />
					<span class="help-block">Ex : Concours Fédéral F4 saison 2017/2018</span>  
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label">Date de parution : </label>
				<div class="col-md-3">
					<div class='input-group date' id='datetimepicker1'>
						<input type='text' class="form-control" name="dtTest1" data-format="DD/MM/YYYY" readonly placeholder="JJ/MM/AAAA" value="<?php echo $dtTest1?>" />
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
					</div>
					<p class="help-block">Ex : <?php echo date('d/m/Y')?></p>
				</div>
				<div class="col-md-3">
					<div class='input-group date' id='datetimepicker2'>
						<input type='text' class="form-control" name="dtTest2" data-format="DD/MM/YYYY" readonly placeholder="JJ/MM/AAAA" value="<?php echo $dtTest2?>" />
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
					</div>
					<p class="help-block">Ex : <?php echo date('d/m/Y')?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="idNiveau">Niveau : </label>
				<div class="col-md-6">
					<select id="idNiveau" name="idNiveau" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($N_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $idNiveau, "SELECTED").'>'.$Record[0].' - '.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="button1id"></label>
				<div class="col-md-8">
					<button id="button1id" name="button1id" class="btn btn-success" type="submit">Rechercher</button>
					<button id="button2id" name="button2id" class="btn btn-danger" type="reset">Annuler</button>
				</div>
			</div>
		</fieldset>
	</form>				
	<div class="panel panel-default">
		<div class="panel-heading">Résultat de la recherche</div>
		<table class="table table-hover">
			<thead><tr><th># - Statut</th><th>Titre</th><th>Parution</th><th>Niveau</th><th>Nb Question</th><th>Actions</th></tr></thead>
			<tbody>
			<?php foreach ($Test_Recordset as $Record)   {
				//idTest, lbTest, dtTest, idStatut, lbStatut, idNiveau, lbNiveau, NbQ
				$action		='<div class="btn-group">';
				//bouton d'impression
				if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["PRINTABLE"])) {
					$action.='<a href="T_preview.php?flagR=0&idtest='.$Record[0].'" target="_blank" class="btn btn-primary btn-sm" Title="Imprimer sans les réponses"><span class="glyphicon glyphicon-eye-close"></span></a>';
					$action.='<a href="T_preview.php?flagR=1&idtest='.$Record[0].'" target="_blank" class="btn btn-primary btn-sm" Title="Imprimer avec les réponses"><span class="glyphicon glyphicon-eye-open"></span></a>';
				}
				switch ($Record[3])   {
					case 1:
						if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["DOWNGRADABLE"]))	
							$action.=modalConfirmation('InValidation_'.$Record[0], $Record[1].' du '.$Record[2], 'warning', 'Placer en attente de validité', 'Placer ce questionnaire en attente de validité', 'exclamation-sign', 'T_val.php?idtest='.$Record[0].'&val=2', 'Retirer la validation et placer de questionnaire en \'Attente de validité\' signifie qu\'il ne sera plus visible pour tout le monde');
						break;
					case 2:
						if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["UPGRADABLE"]))	
							$action.=modalConfirmation('Validation_'.$Record[0], $Record[1].' du '.$Record[2], 'success', 'Valider', 'Valider définitivement ce questionnaire', 'ok-sign', 'T_val.php?idtest='.$Record[0].'&val=1', 'Valider définitivement ce questionnaire implique qu\'il sera visible pour tous les utilisateurs');
						if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["DOWNGRADABLE"]))	
							$action.=modalConfirmation('Modification'.$Record[0], $Record[1].' du '.$Record[2], 'danger', 'Remettre en modification', 'Remettre en modification ce questionnaire', 'pencil', 'T_val.php?idtest='.$Record[0].'&val=3', 'Remettre en modification ce questionnaire permet de lui ajouter/retirer des questions');
						break;
					case 3:
						if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["UPGRADABLE"]) && ($Record[5]>=$minNbQvalTest))
							$action.=modalConfirmation('Validation_'.$Record[0], $Record[1].' du '.$Record[2], 'warning', 'Placer en attente de validité', 'Placer ce questionnaire en attente de validité', 'floppy-disk', 'T_val.php?idtest='.$Record[0].'&val=2', 'Placer ce questionnaire en attente de validité implique que vous ne pourrez plus lui ajouter/retirer des questions');
						//if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["EDITABLE"]))
						//	$action.='<a href="T_edit.php?idtest='.$Record[0].'" class="btn btn-default btn-sm" Title="Mettre en page le questionnaire n°'.$Record[0].'"><span class="glyphicon glyphicon-pencil"></span></button></a>';
						if (in_array($_SESSION['Tright'],$T_right[$Record[3]]["DELETE"]))
							$action.=modalConfirmation('Suppression'.$Record[0], $Record[1].' du '.$Record[2], 'danger', 'Supprimer', 'Supprimer ce questionnaire', 'remove', 'T_rem.php?idtest='.$Record[0], 'Suppression de ce questionnaire, les questions sont conservées en base');
						break;
				}
				$action.='</div>';
				echo '<tr><td>'.$Record[0].' - <span class="label label-'.$status_array[$Record[3]].'">'.$S_Recordset[$Record[3]][1].'</span></td>
					<td>'.$Record[1].'</td><td>'.$Record[2].'</td><td><span class="label label-'.$niveau_array[$Record[4]].'">'.$N_Recordset[$Record[4]][1].'</span></td><td>'.$Record[5].'</td><td>'.$action.'</td>
				</tr>'.chr(13).chr(9).chr(9);				
			}	?>
			</tbody>
		</table>
	</div>
</div>
</body>
</html>
