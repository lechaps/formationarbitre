<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   D R O I T S */
$isNew=false;
$new	= GetGETorDefault('isnew', '');
if (trim($new)!='') $isNew=true;
/* F I N   D U   C H A R G E M E N T */;

/* C H A R G E M E N T   D E S   V A R I A B L E S */
if ($isNew) {
	$MailUtilisateur = GetPOST('MailUtilisateur');
	$MailUtilisateur = strtolower($MailUtilisateur);
} else
	$MailUtilisateur ='';

$NomUtilisateur			= GetPOST('NomUtilisateur');
$PrenomUtilisateur		= GetPOST('PrenomUtilisateur');
$MotdePasseUtilisateur1	= GetPOSTorDefault('MotdePasseUtilisateur1', '');
$MotdePasseUtilisateur2 = GetPOSTorDefault('MotdePasseUtilisateur2', '');
/* F I N   D U   C H A R G E M E N T */

$db_Obj = BDD_Connection();
/* V E R I F I C A T I O N   D E   L A   C O N C O R D A N C E   D E S   M O T S   D E   P A S S E */
if ($MotdePasseUtilisateur1!=$MotdePasseUtilisateur2) die("INVALIDE PASSWORD CORRESPONDANCE");
if ($MailUtilisateur!='') {
	$SQL_txt	= 'SELECT idUtilisateur FROM utilisateur  WHERE MailUtilisateur=:email';
	$Query 	= $db_Obj -> prepare($SQL_txt);
	$Query ->execute(array (':email' => $MailUtilisateur));
	$Q_Recordset = $Query->fetchAll();
	$NbU = $Query->rowCount();
	if ($NbU>0)   {
		$Query->closeCursor();
		unset($db_Obj);
		header('Location: U_profil.php?error='.$MailUtilisateur);
		die();
	}
}
/* F I N   D E   L A   V E R I F I C A T I O N */


/* E N R E G I S T R E M E N T   B D D */
$db_Obj->beginTransaction();
if ($isNew)   {
	if (!in_array($_SESSION['Uright'],$U_right["FULLADMIN"])) die("UNAUTHORIZED ACCESS");
	try {
		$Query = $db_Obj->prepare('INSERT INTO utilisateur(MailUtilisateur, NomUtilisateur, PrenomUtilisateur, MotdePasseUtilisateur, QDroit, TDroit, UDroit) 
			values (:MailUtilisateur, :NomUtilisateur, :PrenomUtilisateur, :MotdePasseUtilisateur, :QDroit, :TDroit, :UDroit)');
		$Query ->execute(array (':MailUtilisateur' => $MailUtilisateur, ':NomUtilisateur' => $NomUtilisateur, ':PrenomUtilisateur' => $PrenomUtilisateur, 
			':MotdePasseUtilisateur' => getPasswordHash(getCryptSalt(), $MotdePasseUtilisateur1), ':QDroit' => $default_user['Q'] , ':TDroit' => $default_user['T'], ':UDroit'=> $default_user['U'] ));
	}
	catch(PDOException $error)   {
		$db_Obj->rollBack();
		die("Données non enregistrées<br>TECHNICAL ERROR : ".$error->getMessage());
	}
}
else   {
	try {
		$Query = $db_Obj->prepare('UPDATE utilisateur set NomUtilisateur=:NomUtilisateur, PrenomUtilisateur=:PrenomUtilisateur WHERE idUtilisateur=:idUtilisateur');
		$Query ->execute(array (':NomUtilisateur' => $NomUtilisateur, ':PrenomUtilisateur' => $PrenomUtilisateur, ':idUtilisateur' => $_SESSION['idUtilisateur'])); 
		if ($MotdePasseUtilisateur1!='')   {
			$Query = $db_Obj->prepare('UPDATE utilisateur set MotdePasseUtilisateur=:MotdePasseUtilisateur WHERE idUtilisateur=:idUtilisateur');
			$Query ->execute(array (':MotdePasseUtilisateur' => getPasswordHash(getCryptSalt(), $MotdePasseUtilisateur1), ':idUtilisateur' => $_SESSION['idUtilisateur'])); 
		}
	}
	catch(PDOException $error)   {
		$db_Obj->rollBack();
		die("Données non enregistrées<br>TECHNICAL ERROR : ".$error->getMessage());
	}
}
$db_Obj->commit();
$Query->closeCursor();
unset($db_Obj);
/* F I N   E N R E G I S T R E M E N T */

header('Location: U_connexion.php'); // Redirection sur la page de profil utilisateur
die();
?>