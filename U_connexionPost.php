<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');	
require('./lib/db.php');
require('./lib/connexion.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$action		= GetGET('action');
$email		= GetPOST('email');
$password	= GetPOST('password');
if(isset($_POST['autolog'])) $autolog	= 1; else $autolog = 0;

$C 	= BDD_Connection();
$S = "SELECT idUtilisateur, MotdePasseUtilisateur FROM utilisateur WHERE MailUtilisateur='".$email."';";
$Q 	= $C -> prepare($S);
$Q ->execute(array (':email' => $email));
$R= $Q->fetch();
$Q->closeCursor();
$idUtilisateur = $R[0];
$MotdePasse = $R[1];

if (comparePassword($password, $MotdePasse)) $_SESSION['idUtilisateur'] = $idUtilisateur;

$_SESSION['Qright'] = loadRight($C, 'Q');
$_SESSION['Tright'] = loadRight($C, 'T');
$_SESSION['Uright'] = loadRight($C, 'U');

if (isset($_SESSION['idUtilisateur'] ))   {
	User_event($C, 1);
	unset($C);
	header('Location: '.$action.'.php');
	die();
} else  {
	unset($C);
	header('Location: U_connexion.php?error=true&action='.$action);
	die();
}
?>