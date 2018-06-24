<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Tright'],$T_right["0"]["UPGRADABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   N I V E A U X */
$Niveau_Recordset = Load_BDDParam($db_Obj, 'Niveau');
unset($db_Obj)
/* F I N   C H A R G E M E N T */;
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Saisie d'un questionnaire</title>
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
				pickTime: false
			});
		});
	</script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<form class="well form-horizontal" method="POST" action="T_post.php">
		<fieldset>
			<legend>Saisie d'un questionnaire</legend>
			<div class="form-group">
				<label class="col-md-4 control-label" for="lbTest">Titre du Questionnaire</label>  
				<div class="col-md-4">
					<input id="lbTest" name="lbTest" placeholder="Titre du questionnaire" class="form-control input-md" type="text" required />
					<span class="help-block">Ex : Concours Fédéral F4 saison <?php echo date('Y').'/'.(date('Y')+1)?></span>  
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="dtTest">Date de parution : </label>
				<div class="col-md-4">
					<div class='input-group date' id='datetimepicker1'>
						<input type='text' class="form-control" name="dtTest" data-format="DD/MM/YYYY" readonly value="<?php echo date('d/m/Y')?>" required />
						<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
					</div>
					<p class="help-block">Ex : <?php echo date('d/m/Y')?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label" for="idNiveau">Niveau : </label>
				<div class="col-md-6">
					<select id="idNiveau" name="idNiveau" class="form-control" required>
						<option value="">Choisissez le niveau</option>
						<?php foreach ($Niveau_Recordset as $Record)	{	echo '<option value="'.$Record[0].'">'.$Record[0].' - '.$Record[1].'</option>'."\n";	}	?>
					</select>
				</div>
			</div>
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
</body>
</html>