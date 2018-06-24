<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
if (!in_array($_SESSION['Uright'],$U_right["UPDATABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$idUser		= GetGET('idUser');
$profil		= GetGET('profil');
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* S E L E C T I O N   D U   P R O F I L  */
switch ($profil)   {
	case 1:
		$SQL_txt='UPDATE utilisateur SET QDroit=3, TDroit=0, UDroit=0 WHERE idUtilisateur=:idUtilisateur';
		break;
	case 2:
		$SQL_txt='UPDATE utilisateur SET QDroit=2, TDroit=2, UDroit=0 WHERE idUtilisateur=:idUtilisateur';
		break;
	case 3:
		$SQL_txt='UPDATE utilisateur SET QDroit=1, TDroit=1, UDroit=1 WHERE idUtilisateur=:idUtilisateur';
		break;
	default : 
		$SQL_txt='UPDATE utilisateur SET QDroit=0, TDroit=0, UDroit=0 WHERE idUtilisateur=:idUtilisateur';
		
}	

/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
try {
	$Query = $db_Obj->prepare($SQL_txt);
	$Query ->execute(array (':idUtilisateur' => $idUser)); 
}
catch(PDOException $error)   {
	$db_Obj->rollBack();
	die("Données non enregistrées<br>TECHNICAL ERROR : ".$error->getMessage());
}
$db_Obj->commit();
$Query->closeCursor();
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

header('Location: U_search.php'); // Redirection sur la page d'information des traitements
die();
?>