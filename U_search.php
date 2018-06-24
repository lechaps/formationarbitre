<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$MailUtilisateur	= GetPOSTorDEFAULT('MailUtilisateur', '');
$NomUtilisateur 	= GetPOSTorDEFAULT('NomUtilisateur', '');
$PrenomUtilisateur	= GetPOSTorDEFAULT('PrenomUtilisateur', '');
$QDroit				= GetPOSTorDEFAULT('QDroit', '');
$TDroit 			= GetPOSTorDEFAULT('TDroit', '');
$UDroit 			= GetPOSTorDEFAULT('UDroit', '');
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Uright'],$U_right["CONSULTABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   D R O I T S */
$D_Recordset = Load_BDDParam($db_Obj, 'Droit');
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   U T I L I S A T E U R S */
$SQL_txt			= 'SELECT idUtilisateur, MailUtilisateur, NomUtilisateur, PrenomUtilisateur, QDroit, TDroit, UDroit FROM utilisateur';
$Query 				= $db_Obj->query($SQL_txt);
$T_Recordset		= $Query->fetchAll();
/* F I N   D U   C H A R G E M E N T */

/* P R E P A R A T I O N   E T   L A N C E M E N T   D E   L A   R E C H E R C H E */
$sql		= array();
$paramArray	= array();
if(!empty($MailUtilisateur)) {
	$sql[]			= 'MailUtilisateur like ?';
    $paramArray[]	= '%'.$MailUtilisateur.'%';
}
if(!empty($NomUtilisateur )) {
	$sql[]			= 'NomUtilisateur like ?';
    $paramArray[]	= '%'.$NomUtilisateur.'%';
}
if(!empty($PrenomUtilisateur)) {
	$sql[]			= 'PrenomUtilisateur like ?';
    $paramArray[]	= '%'.$PrenomUtilisateur.'%';
}
if($QDroit!='') {
	$sql[]			= 'QDroit= ?';
	$paramArray[]	= $QDroit;
}

if($TDroit!='') {
	$sql[]			= 'TDroit= ?';
	$paramArray[]	= $TDroit;
}
if($UDroit!='') {
	$sql[]			= 'UDroit= ?';
	$paramArray[]	= $UDroit;
}
$SQL_txt	= 'SELECT idUtilisateur, MailUtilisateur, NomUtilisateur, PrenomUtilisateur, QDroit, TDroit, UDroit FROM utilisateur';
$Query 	= $db_Obj -> prepare($SQL_txt.(count($paramArray)>0 ? ' WHERE '.join(' AND ',$sql) : '').' ORDER BY 1, 2, 3, 4, 5, 6');
$Query ->execute($paramArray);
$U_Recordset	= $Query->fetchAll();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D E   L A   R E C H E R C H E */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Recherche d'un utilisateur</title>
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
	<form class="well form-horizontal" method="POST" action="U_search.php">
		<fieldset>
			<legend>Recherche d'un utilisateur</legend>
			<div class="form-group">
				<label class="col-md-4 control-label" for="MailUtilisateur">Email</label>  
				<div class="col-md-6">                     
					<input class="form-control" id="MailUtilisateur" name="MailUtilisateur" rows="6" placeholder="nom_prenom@bidulle.com" value="<?php echo $MailUtilisateur?>" maxlength="255">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="NomUtilisateur">Nom</label>  
				<div class="col-md-6">                     
					<input class="form-control" id="NomUtilisateur" name="NomUtilisateur" rows="6" placeholder="Nom" value="<?php echo $NomUtilisateur?>" maxlength="50">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="PrenomUtilisateur">Prénom</label>  
				<div class="col-md-6">                     
					<input class="form-control" id="PrenomUtilisateur" name="PrenomUtilisateur" rows="6" placeholder="Prénom" maxlength="50">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="QDroit">Droits Question : </label>
				<div class="col-md-6">
					<select id="QDroit" name="QDroit" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($D_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $QDroit, "SELECTED").'>'.$Record[0].' - '.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="TDroit">Droits Questionnaire : </label>
				<div class="col-md-6">
					<select id="TDroit" name="TDroit" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($D_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $TDroit, "SELECTED").'>'.$Record[0].' - '.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="UDroit">Droits Utilisateur : </label>
				<div class="col-md-6">
					<select id="UDroit" name="UDroit" class="form-control">
						<option value=''>Tous</option>
						<?php foreach ($D_Recordset as $Record)	{	echo '<option value="'.$Record[0].'" '.ifEncapsulator($Record[0], $UDroit, "SELECTED").'>'.$Record[0].' - '.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="button1id"></label>
				<div class="col-md-8">
					<button id="button1id" name="button1id" class="btn btn-success" type="submit">Rechercher</button>
					<button id="button2id" name="button2id" class="btn btn-danger" type="reset">Annuler</button>
					<a class="btn btn-primary" href="U_profil.php?new=go" role="button">Créer un utilisateur</a>
				</div>
			</div>
		</fieldset>
	</form>
	<div class="panel panel-default">
		<div class="panel-heading">Résultat de la recherche</div>
		<table class="table table-hover">
			<thead><tr><th>#</th><th>Mail</th><th>Nom</th><th>Prénom</th><th>Droit Q</th><th>Droit T</th><th>Droit U</th></tr></thead>
		<tbody>
		<?php foreach ($U_Recordset as $Record)   {
			//idUtilisateur, MailUtilisateur, NomUtilisateur, PrenomUtilisateur, QDroit, Q.lbStatut, TDroit, T.lbStatut, UDroit, U.lbStatut
			$action='<button type="button" class="btn btn-primary btn-sm" data-toggle="modal"  href="U_modal.php?idUser='.$Record[0].'" data-target=".bs-modal-lg'.$Record[0].'" Title="Détail utilisateur">
				<span class="glyphicon glyphicon-eye-open"></span></button>
				<div class="modal fade bs-modal-lg'.$Record[0].'" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>';
			echo '<tr><td>'.$Record[0].'</td><td>'.$Record[1].'</td><td>'.$Record[2].'</td><td>'.$Record[3].'</td><td><span class="label label-'.$droit_array[$Record[4]].'">'.$D_Recordset[$Record[4]][1].'</span></td>
			<td><span class="label label-'.$droit_array[$Record[5]].'">'.$D_Recordset[$Record[5]][1].'</span></td><td><span class="label label-'.$droit_array[$Record[6]].'">'.$D_Recordset[$Record[6]][1].'</span></td><td>'.$action.'</td></tr>';
		}	?>
		</tbody>
		</table>
	</div>
</div>
</body>
</html>
