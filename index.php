<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/db.php');
require('./lib/connexion.php');
$db_Obj 	= BDD_Connection();
$_SESSION['Qright'] = loadRight($db_Obj, 'Q');
$_SESSION['Tright'] = loadRight($db_Obj, 'T');
$_SESSION['Uright'] = loadRight($db_Obj, 'U');
/*Nombre de Question*/
$SQL_txt	= 'SELECT count(*) from question WHERE idStatut=1';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute();
$NbQ = $Query->fetchColumn();
/*Nombre de Test*/
$SQL_txt	= 'SELECT count(*) from test WHERE idStatut=1';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute();
$NbT = $Query->fetchColumn();
/*Nombre d'utilisateur enregistrés*/
$SQL_txt	= 'SELECT COUNT(*) FROM utilisateur';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute();
$NbU = $Query->fetchColumn();
$Query->closeCursor();
/* L O G   U  T I L I S A T E U R  */
User_event($db_Obj, 3, null);
/* F I N  L O G   U  T I L I S A T E U R  */
unset($db_Obj);
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Accueil</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<meta name="description" content="<?php echo $appli_description;?>" />
	<meta name="robots" content="all" />
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
 <div class="jumbotron">
      <div class="container">
        <h1>Bienvenue</h1>
        <p>Vous êtes sur le <?php echo strtolower($appli_description) ?>, il contient une base de données de questions et de questionnaires actualisées dès la publication des mises à jours des lois du jeu par l'IFAB et la FFF</p>
    </div>
    </div>
    <div class="container">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-4">
          <h2><?php echo $NbQ ?> Questions</h2>
          <p>La base de données de questions s'adapte à tous les niveaux. Vous réviez de pouvoir rechercher une question par un mot, une loi, un bout phrase, une note, voir tout à la fois...
          <p><a class="btn btn-primary" href="Q_search.php" role="button">C'est ici &raquo;</a></p>
        </div>
        <div class="col-md-4">
          <h2><?php echo $NbT ?> Questionnaires</h2>
          <p>La base de données de questionnaires contient les examens et pré-examens fournis par les utilisateurs ainsi que les Question/Réponses de la section lois du jeu de la DTA. Vous chercher un questionnaire à votre niveau... </p>
          <p><a class="btn btn-primary" href="T_search.php" role="button">C'est ici &raquo;</a></p>
       </div>
        <div class="col-md-4">
          <h2>Une équipe de <?php echo $NbU ?> modérateurs</h2>
          <p>Les modérateurs contribuent à l'alimentation de la base de données dès la publications des mises à jours des lois du jeu par la F.F.F ou la FIFA</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <h2>Guide des lois du jeu FIFA</h2>
          <p><strong>La bible</strong> unique du règlement du football.</p>
          <p><a class="btn btn-default" href="http://resources.fifa.com/mm/document/footballdevelopment/refereeing/02/90/11/67/082236_220517_lotg_17_18_fr_single_page_150dpi_french.pdf" target="_blank" role="button">Détails &raquo;</a></p>
        </div>
        <div class="col-md-4">
          <h2>L'arbitre et la règlementation</h2>
          <p>Le support relatif aux particularités règlementaires que l'arbitre doit connaître à tout niveau de compétition</p>
          <p><a class="btn btn-default" href="https://www.fff.fr/static/uploads/media/cms_pdf/0003/47/d0d41333db47b7cb92e8e18d240a9cd8768dc9bf.pdf" target="_blank" role="button">Détails &raquo;</a></p>
        </div>
        <div class="col-md-4">
          <h2>Guides des lois du jeu F.F.F</h2>
          <p>Les consignes de la <strong>DTA</strong> et les ajustements locaux des lois du jeu</p>
          <p><a class="btn btn-default" href="https://www.fff.fr/articles/arbitrer/textes/arbitrage-lois-du-jeu/details-articles/2407-270168-les-questions-reponses-2016" target="_blank" role="button">Détails &raquo;</a></p>
        </div>
      </div>
      <hr>

      <footer>
         <p><a data-toggle="modal"  href="U_cgu.html" data-target="#myModal" rel="Charte d'utilisation" Title="Charte d'utilisation">Charte d'utilisation</a></p>
         <p>© 2017 Copyright (Version MerryXmas. ed 17.12.12)&nbsp;/&nbsp;<a target="_blanck" href="https://www.facebook.com/TheChaps">Conçu et maintenu par Romain C.</a></p>
      </footer>
    </div> <!-- /container -->
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
