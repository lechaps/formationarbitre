<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$resultat	= GetGETorDEFAULT('resultat', '');
$idQuestion	= GetGET('idquestion');
$message	= GetGETorDEFAULT('message', '');
//echo $resultat.'/'.$idQuestion;
switch ($resultat) {
		case "" : 
			break;
		case "SAV":
			$message="Question enregistrée";
			$style	="success";
			break;
		case "TAD":
			$message="Question ajoutée au questionnaire";
			$style	="success";
			break;
		case "TRE":
			$message="Question retirée du questionnaire";
			$style	="warning";
			break;
		case "DBL":
			$message="Question non-enregistrée car considérée comme trop similaire à la question suivante : ";
			$style	="danger";
			break;
		case "QV1":
			$message="Question validée, elle est désormais visible de tout le monde";
			$style	="success";
			break;
		case "QV2":
			$message="Question placée en attente de validité, elle n'est plus visible par les utilisateurs anonymes";
			$style	="warning";
			break;
		case "ERR":
			$style	="danger";
			break;
		case "UPD";
			$message = "Réponse modifiée";
			$style = "success";
			break;
		default:
			$message = "UNKNOWN RESULT : ".$resultat;
			break;
	}
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();

/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 5, 'Question n°'.$idQuestion);
/* F I N   L O G   U  T I L I S A T E U R  */

/* C H A R G E M E N T   D E   L A   Q U E S T I O N   A V E C   S A   R E P O N S E */
$SQL_txt= 'SELECT lbQuestion, lbReponse, nbMotReponse, idStatut, dtReponseMaj, source, lbtypequestion FROM question Q LEFT OUTER JOIN typequestion TQ on Q.typequestion=TQ.idtypequestion WHERE Q.idQuestion=:idQuestion';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion));
$Q_recordset = $Query -> fetch ();
if (!in_array($_SESSION['Qright'],$Q_right[$Q_recordset[3]]["PRINTABLE"])) die ("UNAUTHORIZED QUESTION");
if (trim($Q_recordset[5])==null) $source='(Aucune renseignée)'; else $source=$Q_recordset[5];
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   L O I S */
$SQL_txt= 'SELECT QL.idLoi, L.lbLoi FROM question_loi QL LEFT OUTER JOIN loi L on QL.idLoi=L.idLoi WHERE idQuestion=:idQuestion';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion));
$QL_Recordset = $Query->fetchAll();
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   T E S T  C O N T E N A N T   L A   Q U E S T I O N */
$SQL_txt	= 'SELECT detail.idTest, lbTest, dtTest, idNiveau, lbNiveau, NbQ, idStatut, noteReponse, notationReponse FROM (
	SELECT T.idTest, lbTest, dtTest, N.idNiveau, N.lbNiveau, T.idStatut, QT.notationReponse, QT.noteReponse
	FROM question_test QT
	LEFT OUTER JOIN test T ON T.idTest = QT.idTest
	LEFT OUTER JOIN niveau N ON N.idNiveau = T.idNiveau
	WHERE QT.idQuestion =:idQuestion) AS detail
INNER JOIN (
	SELECT idTest, COUNT(*) AS NbQ FROM question_test WHERE idTest IN (SELECT idTest FROM question_test QT WHERE QT.idQuestion =:idQuestion) GROUP BY idTest) AS compt 
ON compt.idTest=detail.idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion));
$QT_Recordset	= $Query->fetchAll();
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   T E S T S  A U T O R I S E   A   C O N T E N I R   L A   Q U E S T I O N */
$authorizedAddQstatus=array();
foreach ($T_right as $Record) if (in_array($_SESSION['Tright'],$Record["MOVEQUESTION"])) array_push($authorizedAddQstatus, array_search($Record, $T_right));
$SQL_txt	= 'SELECT T.idTest, lbTest, dtTest, N.idNiveau, N.lbNiveau, count(distinct QT.idQuestion) as NbQ
			FROM test T
			LEFT OUTER JOIN question_test QT on QT.idTest=T.idTest
			LEFT OUTER JOIN niveau N on N.idNiveau=T.idNiveau
			WHERE T.idStatut=:statut and not exists(SELECT 1 FROM question_test QT where QT.idTest=T.idTest and QT.idQuestion=:idQuestion)
			GROUP BY T.idTest, lbTest, dtTest, idStatut, N.idNiveau, N.lbNiveau 
			ORDER BY 1, 2, 3, 4, 5, 6;';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idQuestion' => $idQuestion, ':statut' => implode(",", $authorizedAddQstatus)));
$T_Recordset	= $Query->fetchAll();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D U   C H A R G E M E N T */
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="myModalLabel">Visualisation de la question #<?php echo $idQuestion;?></h4>
</div>
<div class="modal-body">
	<div class="panel panel-default">
		<div class="panel-heading"><b><?php echo nl2br($Q_recordset[0])?></b></div>
		<div class="panel-body">
			<p id="Q_libel"><?php echo nl2br($Q_recordset[1])?></p>
		</div>
		<table class="table">
			<thead>
				<tr><th>Type</th><th>Lois du jeu</th><th>Difficulté</th><th>Dernière mise à jour de la réponse</th><th>Source</th></tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo nl2br($Q_recordset[6]);?></td>
					<td><?php foreach ($QL_Recordset as $Record)	{	echo $Record[1].', &nbsp';	}	?></td>
					<td><?php echo strval(getDifficult($Q_recordset[2], $Qlevel)) ?>%</td>
					<td><?php echo Format_Date($Q_recordset[4] , 'AAAA-MM-JJ HH:mm:ss', '', 'JJ/MM/AAAA HH:mm');?></td>
					<td><div id="Q_source"><?php echo nl2br($source);?></div></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php
	$note		=2;
	$notation	='';
	if (count($QT_Recordset)>0)   {?>
		<div class="panel panel-default">
			<div class="panel-heading">Liste des questionnaires qui contiennent cette question </div>
			<table class="table table-hover">
				<thead><tr><th># - Niveau</th><th>Questionnaire</th><th>Nb Question</th><th>Note</th><th>Notation</th><th></th></tr></thead>
				<tbody>
				<?php foreach ($QT_Recordset as $Record)
					{	//T.idTest, lbTest, dtTest, N.idNiveau, N.lbNiveau, NbQ
					echo '<tr><td>'.$Record[0].' - <span class="label label-'.$niveau_array[$Record[3]].'">'.$Record[4].'</span></td><td>'.$Record[1].'<small> du '.$Record[2].'</small></td>
					<td>'.$Record[5].'</td><td><small>'.$Record[7].'</small></td><td><small>'.$Record[8].'</small></td><td>';
					if (in_array($_SESSION['Tright'],$T_right[$Record[6]]["MOVEQUESTION"]))   {
						echo '<button type="button" class="btn btn-warning btn-sm" onclick="RemoveQuestionToTest('.$idQuestion.', '.$Record[0].');" Title="Retirer du questionnaire n°'.$Record[0].'">
						<span class="glyphicon glyphicon-remove">
						</button>';
					}
					$note		=$Record[7];
					$notation	=$Record[8];
					echo '</td></tr>'.chr(13).chr(9).chr(9);			
				}	?>
				</tbody>
			</table>
		</div>
	<?php }
	else   {?>
		<div class="panel panel-default">
			<div class="panel-heading">Cette question n'appartient à aucun questionnaire</div>
		</div>
	<?php }
	if (count($T_Recordset)>0 )   {
		if ($Q_recordset[3]==1)   {?>
			<div class="panel panel-default">
				<div class="panel-heading">Ajouter cette question au questionnaire suivant : </div>
				<table class="table table-hover">
					<thead><tr><th># - Niveau</th><th>Questionnaire</th><th>Nb Question</th><th>Note</th><th></th></tr></thead>
					<tbody>
					<?php foreach ($T_Recordset as $Record)   {
						//T.idTest, lbTest, dtTest, N.idNiveau, N.lbNiveau, NbQ
						echo '<tr><td>'.$Record[0].' - <span class="label label-'.$niveau_array[$Record[3]].'">'.$Record[4].'</span></td><td>'.$Record[1].'<small> du '.$Record[2].'</small></td><td>'.$Record[5].'</td>
						<td><select id="note_'.$Record[0].'" name="note_'.$Record[0].'" class="form-control" onchange="changeNotation(\''.$Record[0].'\')";>
							<option value="1" '.ifEncapsulator("1", $note, "SELECTED").'>1</option>
							<option value="2" '.ifEncapsulator("2", $note, "SELECTED").'>2</option>
							<option value="3" '.ifEncapsulator("3", $note, "SELECTED").'>3</option>
							<option value="4" '.ifEncapsulator("4", $note, "SELECTED").'>4</option>
							<option value="5" '.ifEncapsulator("5", $note, "SELECTED").'>5</option>
						</select><textarea class="form-control" id="notation_'.$Record[0].'" name="notation_'.$Record[0].'" rows="3" placeholder="Notation">'.$notation.'</textarea></td>
						<td><button type="button" class="btn btn-success btn-sm" onclick="AddQuestionToTest('.$idQuestion.', '.$Record[0].');" Title="Ajouter au questionnaire n°'.$Record[0].'">
						<span class="glyphicon glyphicon-save">
						</button>
						</td></tr>'.chr(13).chr(9).chr(9);			
					}	?>
					</tbody>
				</table>
			</div>
		<?php } 
		else   {?>
			<div class="panel panel-default">
				<div class="panel-heading">Attendez que cette question soit validée pour l'ajouter à vos questionnaires</div>
			</div>
	<?php }
	} ?>
</div>
<div class="modal-footer">
	<?php
	$action='';
	switch ($Q_recordset[3])   {
		case 1:
			if (in_array($_SESSION['Qright'],$Q_right[$Q_recordset[3]]["DOWNGRADABLE"]) /* && count($QT_Recordset)==0*/ )
				$action.='<button type="button" class="btn btn-warning" onclick="SetValidatorQuestion('.$idQuestion.', 2);" Title="Signaler la question">
						<span class="glyphicon glyphicon-exclamation-sign">&nbsp;</span>Signaler la question
						</button>';
			break;
		case 2:	
		case 3:
			if (in_array($_SESSION['Qright'],$Q_right[$Q_recordset[3]]["UPGRADABLE"]))
				$action.='<button type="button" class="btn btn-success" onclick="SetValidatorQuestion('.$idQuestion.', 1);" Title="Valider cette question">
						<span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>Valider
						</button>';
			if (in_array($_SESSION['Qright'],$Q_right[$Q_recordset[3]]["DELETE"]))
				$action.='<button type="button" class="btn btn-danger" onclick="DeleteQuestion('.$idQuestion.');" Title="Supprimer définitivement cette question">
						<span class="glyphicon glyphicon-remove">&nbsp;</span>Supprimer définitivement cette question
						</button>';
			break;
	}
	echo $action;?>	
	<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove">&nbsp;</span>Fermer</button>
</div>
<?php /* A F F I C H A G E   D U   B A N D E A U   C O N F I R M A N T   L A   B O N N E   E X E C U T I O N   D E   L ' O P E R A T I O N */
if ($resultat<>'')   {   ?>
<div class="alert alert-<?php echo $style?> alert-<?php echo $style?>" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<?php echo $message;?>
</div>
<?php } 
/* C H A R G E M E N T   D E S   D R O I T S pour modification de la question*/
if (in_array($_SESSION['Qright'],$Q_right[$Q_recordset[3]]["UPDATEANSWER"]))   {?>
	<script language="javascript">
	$('#Q_libel').click(function(){ // Au click 
		if(!$(this).children("textarea").length) { // Si il n'y a pas de textarea
			$(this).html(function(){ // Un remplacement du contenue de ma div par
				var text = $(this).html().replace(/(<br>\n)/g, '\n').replace(/(<br>)/g, '\n');
				return '<textarea cols="130" rows="15" >' + text + '</textarea>'; // Un textarea qui aura par défaut le contenue de ma div (ex: du texte)
			});
		$(this).children("textarea").focus(); // Et ensuite met mon curseur dans le textarea pour que je puisse le modifier directement
		}
	});
   
	$('#Q_libel').focusout(null,function(){ // Maintenant, quand je clic à l'exterieur 
		$(this).html(function(){ // Remplace moi le textarea
			UpdateQuestion(<?php echo $idQuestion?> , $(this).children('textarea').val(), 'lbreponse');
			//return $(this).children('textarea').val().replace(/\n/g,"<br>"); // Par uniquement son contenu sans textarea, inutile par la modal est MAJ par l'update appellé à la ligne précédente
		});
	});
	$('#Q_source').click(function(){ // fait sur le model précédent
		if(!$(this).children("textarea").length) { 
			$(this).html(function(){ 
				var text = $(this).html().replace(/(<br>\n)/g, '\n').replace(/(<br>)/g, '\n');
				return '<textarea cols="30" rows="2" >' + text + '</textarea>'; 
			});
		$(this).children("textarea").focus();
		}
	});
   
	$('#Q_source').focusout(null,function(){
		$(this).html(function(){
			UpdateQuestion(<?php echo $idQuestion?> , $(this).children('textarea').val(), 'source');
		});
	});
	</script>
<?php } ?>





