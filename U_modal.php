<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Uright'],$U_right["CONSULTABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idUser		= GetGET('idUser');
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   D R O I T S */
$D_Recordset = Load_BDDParam($db_Obj, 'Droit');
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E   L ' U T I L I S A T E U R  */
$SQL_txt= 'SELECT MailUtilisateur, NomUtilisateur, PrenomUtilisateur, QDroit, TDroit, UDroit FROM utilisateur WHERE idUtilisateur=:idUser';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idUser' => $idUser));
$U_recordset = $Query -> fetch ();

/* C H A R G E M E N T   D E S   S T A T U T S */
$S_Recordset		= Load_BDDParam($db_Obj, 'Statut');
/* F I N   D U   C H A R G E M E N T */
unset($db_Obj);
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="myModalLabel">Fiche Utilisateur #<?php echo $idUser?></h4>
</div>
<div class="modal-body">
	<div class="panel panel-default">
		<div class="panel-heading"><b><?php echo nl2br($U_recordset[0])?></b></div>
		<div class="panel-body">
			<p><?php echo nl2br($U_recordset[1])?>&nbsp;<i><?php echo nl2br($U_recordset[2])?></i></p>
		</div>
		<table class="table">
			<thead>
				<tr><th>Question</th><th>Questionnaire</th><th>Utilisateur</th></tr>
			</thead>
			<tbody>
				<tr>
					<td><span class="label label-<?php echo $droit_array[$U_recordset[3]]?>"><?php echo $D_Recordset[$U_recordset[3]][1] ?></span></td>
					<td><span class="label label-<?php echo $droit_array[$U_recordset[4]]?>"><?php echo $D_Recordset[$U_recordset[4]][1] ?></span></td>
					<td><span class="label label-<?php echo $droit_array[$U_recordset[5]]?>"><?php echo $D_Recordset[$U_recordset[5]][1] ?></span></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="modal-footer">
	<?php if (in_array($_SESSION['Uright'],$U_right["CONSULTABLE"]))   { ?>
		<a href="U_set.php?idUser=<?php echo $idUser?>&profil=1" class="btn btn-danger" Title="Utilisateur de base"><span class="glyphicon glyphicon-resize-full">&nbsp;</span>Utilisateur de base</a>
		<a href="U_set.php?idUser=<?php echo $idUser?>&profil=2" class="btn btn-warning" Title="Gestionnaire"><span class="glyphicon glyphicon-floppy-disk">&nbsp;</span>Gestionnaire</a>
		<a href="U_set.php?idUser=<?php echo $idUser?>&profil=3" class="btn btn-success" Title="Admin"><span class="glyphicon glyphicon-remove">&nbsp;</span>Admin</a>
	<?php } ?>
	<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove">&nbsp;</span>Annuler</button>
</div>