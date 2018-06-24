<?php
setlocale (LC_TIME, 'fr_FR.utf8','fra');
$appli_name			="Formation Arbitre";
$appli_description	="Site dédié à la formation des arbitres de football : devenez un maître des lois du jeux";
$css_bs_link		="./css/bootstrap.min.css";
$css_theme_link		="./css/bootstrap-theme.min.css";
$css_perso_link		="./css/perso.css";
$css_dp_link		="./css/bootstrap-datetimepicker.min.css";
$css_jqueryUI_link	="./css/jquery-ui.min.css";
$js_bs_link			="./js/bootstrap.min.js";
$js_dp_link			="./js/bootstrap-datetimepicker.min.js";
$js_dp_lg			="./js/locales/bootstrap-datetimepicker.fr.js";
$js_mm_link			="./js/moment.min.js";
$js_jquery_link		="./js/jquery1.11.0.min.js";
$js_jqueryUI_link	="./js/jquery-ui-1.9.2.custom.min.js";
$PDFfont 			='Arial';													//Font utilisé dans l'export PDF
$minNbQvalTest		= 1;														//Nb de Question Minimum pour la validation d'un test
$status_array 	=array(0=> "default", 1=> "success", 2 => "warning", 3 => "danger");			//Style Bootstrap Statuts
$niveau_array 	=array(0=> "default", 1=> "success", 2 => "warning", 3 => "danger");			//Style Bootstrap Niveau
$droit_array 	=array(0=> "default", 1=> "success", 2 => "warning", 3 => "danger");//Style Bootstrap Droit Utilisateur
$default_right 	=array('Q' => 0, 'T' => 0, 'U'=>0);								//Droit par défauts
$default_user 	=array('Q' => 3, 'T' => 3, 'U'=>0);								//Droit par défauts pour un utilisateur enregistré
$Tstatus_creation	= 3;														//Statut d'un questionnaire à sa création
$Qstatus_creation	= 2;														//Statut d'une question à sa création
$Qlevel				= 310;														//Nombre de mot maximal d'une réponse : fixe le taux de difficulté
/*Droit des Questionnaires : habilitation des statuts de Test en fonction des profils*/
$T_right = array(
	"1"		=> array("PRINTABLE" => array(0, 1, 2, 3) ,	"UPGRADABLE" => array(),		"EDITABLE" => array(),		"DOWNGRADABLE" => array(1,2),	"DELETE" => array(), 			"MOVEQUESTION" => array()			),
	"2"		=> array("PRINTABLE" => array(1, 2) ,	 		"UPGRADABLE" => array(1,2),		"EDITABLE" => array(),		"DOWNGRADABLE" => array(1,2,3),	"DELETE" => array(), 			"MOVEQUESTION" => array()			),
	"3"		=> array("PRINTABLE" => array(1, 2) , 			"UPGRADABLE" => array(1,2,3),	"EDITABLE" => array(1,2),	"DOWNGRADABLE" => array(), 		"DELETE" => array(1), 			"MOVEQUESTION" => array(1, 2)		),
	"0"		=> array("PRINTABLE" => array() , 			"UPGRADABLE" => array(1,2,3),	"EDITABLE" => array(1,2,3),	"DOWNGRADABLE" => array(),		"DELETE" => array(), 			"MOVEQUESTION" => array()			)
	);
/*Droit des Questions : habilitation des statuts de question en fonction des profils*/
$Q_right = array(
	"1"		=> array("PRINTABLE" => array(0, 1, 2, 3) ,	"UPGRADABLE" => array(),		"DOWNGRADABLE" => array(1,2)	, "DELETE" => array(),	"UPDATEANSWER" => array(1,2)	),
	"2"		=> array("PRINTABLE" => array(1, 2, 3) , 	"UPGRADABLE" => array(1,2),		"DOWNGRADABLE" => array()		, "DELETE" => array(1),	"UPDATEANSWER" => array(1,2)	),
	"3"		=> array("PRINTABLE" => array() , 			"UPGRADABLE" => array(1,2,3),	"DOWNGRADABLE" => array()		, "DELETE" => array(),	"UPDATEANSWER" => array()		),
	"0"		=> array("PRINTABLE" => array() , 			"UPGRADABLE" => array(1,2,3),	"DOWNGRADABLE" => array()		, "DELETE" => array(), 	"UPDATEANSWER" => array()		)
	);
/*Droit sur les Utilisateurs  : habilitation des profils sur la gestion des utilisateurs*/
$U_right = array("CONSULTABLE" => array(1), "UPDATABLE" => array(1), "FULLADMIN" => array(1));
?>