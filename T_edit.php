<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/db.php');
require('./lib/tools.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idTest		= GetGET('idtest');
/* F I N   D U   C H A R G E M E N T */

$db_Obj 	= BDD_Connection();
/* C H A R G E M E N T   D U   T E S T */
$SQL_txt= 'SELECT lbTest, dtTest, lbNiveau, idStatut FROM test T LEFT OUTER JOIN niveau N on T.idNiveau=N.idNiveau WHERE T.idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$T_recordset = $Query -> fetch ();
$lbTest	= $T_recordset[0];
$dtTest	= mb_strtoupper(strftime("%A %d %B %Y",  strtotime($T_recordset[1])));
$Niveau	= 'Niveau '.$T_recordset[2];
/* F I N   D U   C H A R G E M E N T */

/* V E R I F I C A T I O N   D E S   D R O I T   D ' I M P R E S S I O N */
if (!in_array($_SESSION['Tright'],$T_right[$T_recordset[3]]["EDITABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D E   L A   V E R I F I C A T I O N */
 
/* C H A R G E M E N T   D E   L A   N O T E */
$SQL_txt= 'SELECT sum(noteReponse) FROM question_test WHERE idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$N_recordset= $Query -> fetch ();
$noteTest	= $N_recordset[0];
/* F I N   D U   C H A R G E M E N T */


/* C H A R G E M E N T   D E S   Q U E S T I O N S */
$SQL_txt= 'SELECT Q.idQuestion, lbQuestion, lbReponse, QT.noteReponse, QT.notationReponse FROM question_test QT
LEFT OUTER JOIN question Q on Q.idQuestion=QT.idQuestion
WHERE idTest=:idTest
ORDER BY noteReponse, ordreQuestion';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$Q_recordset = $Query -> fetchAll ();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D U   C H A R G E M E N T */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $appli_name;?> - Modifier un Questionnaire</title>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
	<link href="<?php echo $css_bs_link;?>" rel="stylesheet" type="text/css">
	<link href="<?php echo $css_theme_link;?>" rel="stylesheet">
	<link href="<?php echo $css_perso_link;?>" rel="stylesheet">
	<link href="<?php echo $css_jqueryUI_link;?>" rel="stylesheet">
	<script src="<?php echo $js_jquery_link;?>"></script>
	<script src="<?php echo $js_jqueryUI_link;?>"></script>
	<script src="<?php echo $js_bs_link;?>"></script>
	<script>
        $(function() {
            $( "#list-group" ).sortable({
                cursor: "move",
                cancel: "h2",
                opacity: 0.5,
                revert: true,
                key: "sort",
                update: function( event, ui ) {alert(
                    $( "#list-group" ).sortable( "serialize", { attribute: "id" } )
                )}
            });
            $( "#list-group" ).disableSelection();
        });
  </script>
</head>
<body>
<?php require("./lib/menu.php"); ?>
<div class="container">
    <div class="jumbotron">
        <h2><?php echo $T_recordset[0];?>&nbsp;&nbsp;<small><?php echo $dtTest?></small></h2>
        <p><?php echo $Niveau;?></p>
        <p>Questionnaire sur : <b><?php echo $noteTest;?> points</b></p>
        <p>
        1 - Le terme « Décisions » est systématiquement utilisé au pluriel même lorsque celle-ci est unique.<br>
        2 - Les candidats peuvent utiliser les abréviations définies dans le préambule de la troisième partie du Guide des Lois du Jeu à savoir SRA, HJ, SDB, SRCP loi 8, SRCP loi 13, SDR, CPB, CPC , BAT, CFI, CFD, CPR, AVT, CAS, EXC, RCC lorsque celles-ci ne prêtent à aucune équivoque.<br>
        3 - Toute absence de la mention « Rapport » lorsqu'elle est exigée, sera sanctionnée d'un retrait de 0,25 point à la question concernée.<br>
        4 - Toute absence ou erreur de motif disciplinaire devant être mentionné lors d'un avertissement ou d'une exclusion, sera sanctionnée d\'un retrait de 0,25 point à la question concernée.</p>
    </div>
    <div class="list-group" id="list-group">
    <?php
    /*************** C O N T E N U *****************************/
    $compteur 	= 0;
    $note		= 0;
    echo '<br><br>';
    foreach ($Q_recordset as $Record)   {
        //lbQuestion, lbReponse, noteReponse, notationReponse
        $compteur++;
        $idQuestion			= $Record[0];
        $lbQuestion			= $Record[1];
        $lbReponse			= $Record[2];
    	$noteReponse		= $Record[3];
    	$notationReponse	= $Record[4];
    	if ($noteReponse!=$note)   {
    		$note=$noteReponse;
    		echo '<h2>Questions à '.$noteReponse.' points</h2>';
    	}
	    echo '<div id="id_'.$idQuestion.'" class="panel panel-default">
               <div class="panel-heading"><b>Question n°<span>'.$compteur.'</span> :</b>
                    <h3 class="panel-title">'.$lbQuestion.'</h3></div>
               <div class="panel-body">'.$lbReponse.'</div>
               <div class="panel-footer">Note : '.$noteReponse.' points
                <br> '.$notationReponse.'</div>
            </div>';
	
    };?>
	</div>
</div>
</body>
</html>

