<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Qright'],$Q_right["0"]["UPGRADABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* C H A R G E M E N T   D E S   L O I S   e t   N I V E A U X */
$Loi_Recordset		= Load_BDDParam($db_Obj, 'Loi');
$TypeQ_Recordset	= Load_BDDParam($db_Obj, 'TypeQ');
unset($db_Obj);
/* F I N   C H A R G E M E N T*/
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $appli_name;?> - Saisie d'une question</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<link href="./css/bootstrapValidator.css" rel="stylesheet"/>
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
	<script src="./js/bootstrapValidator.js"></script>
	<script src="Q_modal.js"></script>
	<script language="javascript">
	$(document).ready(function() {
		$('#QuestionForm').bootstrapValidator({
            message: 'Valeur incorrecte',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                lbQuestion: {
                    message: 'La question est requise',
                    validators: {
                        notEmpty: {
                            message: 'La question ne peut être vide'
                        },
                        stringLength: {
                            min: 10,
                            max: 25000,
                            message: 'La question possède de %s à %s caractères'
                        },
                    }
                },
                'idLoi[]': {
                validators: {
                    choice: {
                        min: 1,
                        message: 'Vous devez choisir au moins une loi'
                    	}
                	}
            	},
            	lbReponse: {
                    message: 'La réponse est requise',
                    validators: {
                        notEmpty: {
                            message: 'La réponse ne peut être vide'
                        },
                        stringLength: {
                            min: 1,
                            max: 25000,
                            message: 'La réponse possède de %s à %s caractères'
                        },
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            e.preventDefault();
            var $form = $(e.target);
			$.ajax({
                url: $form.attr('action'),
                type: $form.attr('method'),
                data: $form.serialize(),
                success: function(html) {
                	var obj = jQuery.parseJSON(html);
                    $('#myModal').modal({remote : 'Q_modal.php?resultat='+obj.result+'&idquestion='+obj.idquestion+'&message='+obj.message});
                    $('#myModal').modal('show');
                }
           	});
        });
    	$('#myModal').on('hide.bs.modal', function (e) {
  			document.location.href='Q_form.php';
  		})
  	});
</script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<form class="well form-horizontal" id="QuestionForm" name="QuestionForm" action="Q_post.php" method="post">
		<fieldset>
			<legend>Saisie d'une question avec sa réponse</legend>
			<div class="form-group">
				<label class="col-md-2 control-label" for="lbQuestion">Libellé de la question</label>  
				<div class="col-md-10">                     
					<textarea class="form-control" id="lbQuestion" name="lbQuestion" rows="10" required placeholder="..."></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="lois">Type : </label>
				<div class="col-md-10">
					<select id="idtypequestion" name="idtypequestion" class="form-control">
						<?php foreach ($TypeQ_Recordset as $Record)	{	echo '<option value="'.$Record[0].'">'.$Record[1].'</option>'."\n";	}	?>>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="lois">Lois du Jeu : </label>
				<?php
				$i=0;
				$compteur=0;
				foreach ($Loi_Recordset as $Record)	{
					echo "\t\t\t\t\t".'<div class="col-md-2"><input type="checkbox" name="idLoi[]" value="'.$Record[0].'"/>&nbsp;'.$Record[1].'</div>'."\n";	
					$i+=1;
					if ($i % 5 == 0) echo "\t\t\t".'</div>'."\n"."\t\t\t".'<div class="form-group"><label class="col-md-2 control-label" for=""></label>'."\n";
					}?>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="lbReponse">Libellé de la réponse</label>  
				<div class="col-md-10">                     
					<textarea class="form-control" id="lbReponse" name="lbReponse" rows="10" required placeholder="..."></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="source">Source</label>  
				<div class="col-md-10">                     
					<input class="form-control" id="source" name="source" size="50" maxlength="100"  placeholder="Textes officiels (Lois du jeu Fifa ou FFF) garantissant l'exactitude de la réponse. Ex : FFF QR L12/§4/Q6 ">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="button1id"></label>
				<div class="col-md-10">
					<button id="button1id" name="button1id" class="btn btn-success" type="submit">Enregistrer</button>
					<button id="button2id" name="button2id" class="btn btn-danger" type="reset">Annuler</button>
				</div>
			</div>
		</fieldset>
	</form>
	<div id="myModal" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg"><div class="modal-content"></div></div>
	</div>
</div>
</body>
</html>
