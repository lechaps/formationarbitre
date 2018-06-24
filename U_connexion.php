<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');
require('./lib/connexion.php');

$action	= GetGETorDEFAULT('action', 'index');
$error	= GetGETorDEFAULT('error', '');
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Accueil</title>
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
            });
	});
	</script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<?php if ($error!="")   {?>
		<div class="alert alert-danger">
			<strong>Erreur d'Email ou de Mot de passe</strong>
		</div>
	<?php } ?>
	<form class="well form-horizontal"  method="POST" action="U_connexionPost.php?action=<?php echo $action?>">
		<fieldset>
			<legend>Veuillez vous identifier...</legend>
			<div class="form-group">
				<label class="col-md-3 control-label" for="email"></label> 
				<label class="col-md-6 control-label"><input type="email" name="email" class="form-control" placeholder="Adresse email" required autofocus></label>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label" for="password"></label> 
				<label class="col-md-6 control-label"><input type="password" name="password" class="form-control" placeholder="Mot de passe" required></label>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="password"></label>
				<label class="col-md-4 control-label"><button class="btn btn-success btn-block" type="submit"><span class="glyphicon glyphicon-home">&nbsp;</span>Se connecter</button></label>
			</div>
		</fieldset>
	</form>
</div>
</body>
</html>

