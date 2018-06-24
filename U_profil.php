<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');


/* C H A R G E M E N T  */
if (!isset($_SESSION['idUtilisateur'])) {
	$isNew=true;
}
else {
	$isNew=false;
}
$error	= GetGETorDefault('error', '');
$new	= GetGETorDefault('new', '');
if ( (trim($new)!='') && in_array($_SESSION['Uright'],$U_right["FULLADMIN"]) )
	$isNew=true;
	

/* F I N   D U   C H A R G E M E N T */

if ($isNew)   { 
	$MailUtilisateur	= 'Saisie d\'un nouveau compte';
	if ($error!='')  $MailUtilisateur	= 'ATTENTION, LE MAIL '.$error.' EST DEJA CONNU';
	$NomUtilisateur		= '';
	$PrenomUtilisateur	= '';
}
else {
	$db_Obj = BDD_Connection();
	/* C H A R G E M E N T   D U   P R O F I L */
	$SQL_txt= 'SELECT idUtilisateur, MailUtilisateur, NomUtilisateur, PrenomUtilisateur, QDroit, TDroit, UDroit FROM utilisateur WHERE idUtilisateur=:idUtilisateur';
	$Query 	= $db_Obj -> prepare($SQL_txt);
	$Query ->execute(array (':idUtilisateur' => $_SESSION['idUtilisateur']));
	$U_recordset 		= $Query -> fetch ();
	$MailUtilisateur	= trim($U_recordset[1]);
	$NomUtilisateur		= trim($U_recordset[2]);
	$PrenomUtilisateur	= trim($U_recordset[3]);
	$QDroit				= trim($U_recordset[4]);
	$TDroit				= trim($U_recordset[5]);
	$UDroit				= trim($U_recordset[6]);
	
	/* C H A R G E M E N T   D E S   S T A T U T S */
	$Droit_Recordset = Load_BDDParam($db_Obj, 'Droit');
	/* F I N   D U   C H A R G E M E N T */
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Profil Utilisateur</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
	<script type="text/javascript">
	 $(document).ready(function() {
            $('#Uform').submit(function () {
                var form = $(this);
                if ($('#MotdePasseUtilisateur1').val() != $('#MotdePasseUtilisateur2').val()) {
						$('#passwordStrength').removeClass().addClass('label label-danger').html('Mot de passe différent');
                    return false;
                }
                if (!$('#CharteUtilisation').is(':checked')) {
						alert("Merci d'accepter la charte d'utilisation");
                    return false;
                }
                if ($('#MotdePasseUtilisateur1').val() == '') {
						$('#passwordStrength').removeClass().addClass('label label-danger').html('Mot de passe vide');
                    return false;
                }
				<?php if ($isNew)   { ?>
					var reg = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
					if(!reg.test($('#MailUtilisateur').val()))   {
						$('#emailvalide').removeClass().addClass('label label-danger').html('Email Invalide');
						return false;
					}
				<?php   } ?>
			});
	});
	</script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<form id="Uform" name="Uform" class="well form-horizontal" method="POST" action="U_post.php?isnew=<?php echo $isNew?>">
		<fieldset>
			<legend><?php echo $MailUtilisateur ?></legend>
			<?php if ($isNew)   { ?>
				<div class="form-group">
					<label class="col-md-4 control-label" for="MailUtilisateur">Email</label>  
					<div class="col-md-4"><input id="MailUtilisateur" name="MailUtilisateur" placeholder="email@mail.com" class="form-control input-md" type="mail" required /></div>
					<span class="label label-primary" id="emailvalide">Saisissez deux fois votre nouveau mot de passe</span>
				</div>			
			<?php   } ?>			
			<div class="form-group">
				<label class="col-md-4 control-label" for="NomUtilisateur">Nom</label>  
				<div class="col-md-4"><input id="NomUtilisateur" name="NomUtilisateur" placeholder="Nom de l'utilisateur" class="form-control input-md" type="text" required value="<?php echo $NomUtilisateur?>"/></div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="PrenomUtilisateur">Prénom</label>  
				<div class="col-md-4"><input id="PrenomUtilisateur" name="PrenomUtilisateur" placeholder="Prénom de l'utilisateur" class="form-control input-md" type="text" required value="<?php echo $PrenomUtilisateur?>"/></div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="MotdePasseUtilisateur1">Mot de passe</label>  
				<div class="col-md-4"><input id="MotdePasseUtilisateur1" name="MotdePasseUtilisateur1" class="form-control input-md" type="password" required /></div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="MotdePasseUtilisateur2">Mot de passe (Confirmation)</label>  
				<div class="col-md-4"><input id="MotdePasseUtilisateur2" name="MotdePasseUtilisateur2" class="form-control input-md" type="password" required /></div>
				<span class="label label-primary" id="passwordStrength">Saisissez deux fois votre nouveau mot de passe</span>
			</div>
			<div class="form-group">
				<label class="col-md-6 control-label" for="CharteUtilisation">J'ai pris connaissance <a data-toggle="modal"  href="U_cgu.html" data-target="#myModal" rel="Charte d'utilisation" Title="Charte d'utilisation"> de la Charte d'utilisation</a></label>  
				<div class="col-md-2"><input id="CharteUtilisation" name="CharteUtilisation" class="form-control input-md" type="checkbox" required /></div>
			</div>
			<?php if (!$isNew)   { ?>
				<div class="form-group">
					<label class="col-md-4 control-label" for="QDroit">Administration des Questions</label>  
					<div class="col-md-4"><span class="label label-<?php echo $droit_array[$QDroit]?>"><?php echo $Droit_Recordset[$QDroit][1]?></span></div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label" for="TDroit">administrationn des Questionnaires</label>  
					<div class="col-md-4"><span class="label label-<?php echo $droit_array[$TDroit]?>"><?php echo $Droit_Recordset[$TDroit][1]?></span></div>
				</div>
				<div class="form-group">
					<label class="col-md-4 control-label" for="UDroit">Administration des Utilisateurs</label>  
					<div class="col-md-4"><span class="label label-<?php echo $droit_array[$UDroit]?>"><?php echo $Droit_Recordset[$UDroit][1]?></span></div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="col-md-4 control-label" for="button1id"></label>
				<div class="col-md-8">
					<button id="button1id" name="button1id" class="btn btn-success" type="submit">Enregistrer</button>
					<button id="button2id" name="button2id" class="btn btn-danger" type="reset">Annuler</button>
				</div>
			</div>
		</fieldset>
	</form>
</div>
    <div id="myModal" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg"><div class="modal-content"></div></div>
	</div>
<script>
	$('#myModal').on('hide.bs.modal', function () {
		$(this).removeData('bs.modal');
	});
</script>
</body>
</html>