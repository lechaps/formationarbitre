<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

$db_Obj = BDD_Connection();
/*Liste des événements*/
$SQL_txt	= 'SELECT UE.idUE, UE.idUtilisateur, U.NomUtilisateur, U.PrenomUtilisateur, E.lbEvenement, UE.lbUE, UE.dtUE
			FROM utilisateur_evenement UE
			LEFT OUTER JOIN utilisateur U on UE.idUtilisateur=U.idUtilisateur
			LEFT OUTER JOIN evenement E on UE.idEvenement=E.idEvenement
			ORDER BY 1 desc';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute();
$UE_Recordset = $Query->fetchAll();
$Query->closeCursor();
/*Nombre d'utilisateur connecté dans la dernière semaine*/
$dateMin	= date("Y-m-d H:i:s", strtotime("-1 week"));
$SQL_txt	= 'SELECT COUNT( DISTINCT IPadress ) FROM utilisateur_evenement WHERE idEvenement=3 AND dtUE>:dateMin';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':dateMin' => $dateMin));
$NbUE = $Query->fetchColumn();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D E   L A   R E C H E R C H E */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Evenement Utilisateur</title>
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
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">Evènement du site (<strong><?php echo $NbUE ?></strong> connexions sur 7j glissant)</div>
		<table class="table table-hover">
			<thead><tr><th>#User</th><th>Nom</th><th>Prénom</th><th>Evenement</th><th>Information</th><th>Date</th></tr></thead>
			<tbody>
			<?php foreach ($UE_Recordset as $Record)   {
				//UE.idUtilisateur, U.NomUtilisateur, U.PrenomUtilisateur, E.lbEvenement, UE.lbUE, UE.dtUE
				echo '<tr><td>'.$Record[1].'<td>'.$Record[2].'</td><td>'.$Record[3].'</td><td>'.$Record[4].'</td><td>'.$Record[5].'</td><td>'.$Record[6].'</td></tr>'.chr(13).chr(9).chr(9);				
			}	?>
			</tbody>
		</table>
	</div>
</div>
</body>
</html>
