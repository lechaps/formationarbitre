<?php
ini_set("session.gc_maxlifetime", 12 * 60 * 60);
session_start();
ini_set('display_errors','on');
error_reporting(E_ALL);

/**********************************************************************/
/* D O N N E   L E S   D R O I T S   D ' U N   U T I L I S A T E U R  */
function loadRight($C, $B)   {
	if (isset($_SESSION['idUtilisateur'] ))   {
		$idUtilisateur = $_SESSION['idUtilisateur'];
		$S = "SELECT ".$B."Droit FROM utilisateur WHERE idUtilisateur=".$idUtilisateur.";";
		$Q 	= $C -> prepare($S);
		$Q ->execute(array (':idUtilisateur' => $idUtilisateur));
		$R= $Q->fetch();
		$Q->closeCursor();
		return $R[0];
	}
	else  {
		global $default_right;
		return $default_right[$B];
	}
}

/***********************************************************************/
/* O B T E N I R   U N   S E L   = >   C h a i n e   d e   8   c a r . */
function getCryptSalt()   {
    return substr(str_pad(dechex(mt_rand()), 8, '0', STR_PAD_LEFT ), -8);
}

/***************************************************************************************/
/* C A L C U L   U N   H A S H   D E P U I S   U N   S E L   E T   U N E   C H A I N E */
function getPasswordHash( $salt, $password )   {
	return $salt.(hash('whirlpool', $salt.$password));
}				//// exemple d'utilisation : $hash = getPasswordHash(getCryptSalt(), $password);

/*****************************************************************************************/
/* C O M P A R E   U N E   C H A I N E   E T   H A S H   A V E C   S E L   I N T E G R E */ 
function comparePassword( $password, $hash )   {
    $salt = substr($hash, 0, 8);
    return $hash == getPasswordHash( $salt, $password );
}
?>