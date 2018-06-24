<?php
header('Content-Type: text/html; charset=UTF-8');
require('./lib/param.php');
require('./lib/tools.php');
require('./lib/db.php');
require('./lib/connexion.php');
require('./lib/fpdf181/fpdf.php');

/* C H A R G E M E N T   D E S   V A R I A B L E S */
$flagReponse= GetGETorDefault('flagR', 0);
$idTest		= GetGET('idtest');
$flagNotation= GetGETorDefault('flagN', 1);
/* F I N   D U   C H A R G E M E N T */

$db_Obj 	= BDD_Connection();
/* L O G   U  T I L I S A T E U R  */
if ($flagReponse)
	User_event($db_Obj, 4, 'Questionnaire n°'.$idTest);
else
	User_event($db_Obj, 6, 'Questionnaire n°'.$idTest);
/* F I N   L O G   U  T I L I S A T E U R 
 */
/* C H A R G E M E N T   D U   T E S T */
$SQL_txt= 'SELECT lbTest, dtTest, lbNiveau, idStatut FROM test T LEFT OUTER JOIN niveau N on T.idNiveau=N.idNiveau WHERE T.idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$T_recordset = $Query -> fetch ();
$lbTest	= utf8_decode($T_recordset[0]);
$dtTest	= ucwords(utf8_decode(strftime("%A %d %B %Y",  strtotime($T_recordset[1]))));
$Niveau	= utf8_decode('Niveau '.$T_recordset[2]);
/* F I N   D U   C H A R G E M E N T */

/* V E R I F I C A T I O N   D E S   D R O I T   D ' I M P R E S S I O N */
if (!in_array($_SESSION['Tright'],$T_right[$T_recordset[3]]["PRINTABLE"])) die("UNAUTHORIZED ACCESS");
/* F I N   D E   L A   V E R I F I C A T I O N */

/* C H A R G E M E N T   D E   L A   N O T E */
$SQL_txt= 'SELECT sum(noteReponse) FROM question_test WHERE idTest=:idTest';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$N_recordset= $Query -> fetch ();
$noteTest	= $N_recordset[0];
/* F I N   D U   C H A R G E M E N T */

/* C H A R G E M E N T   D E S   Q U E S T I O N S */
$SQL_txt= 'SELECT lbQuestion, lbReponse, QT.noteReponse, QT.notationReponse, Q.idStatut, Q.source, TQ.lbtypequestion FROM question_test QT
LEFT OUTER JOIN question Q on Q.idQuestion=QT.idQuestion
LEFT OUTER JOIN typequestion TQ on TQ.idtypequestion=Q.typequestion
WHERE idTest=:idTest
ORDER BY noteReponse, ordreQuestion';
$Query 	= $db_Obj -> prepare($SQL_txt);
$Query ->execute(array (':idTest' => $idTest));
$Q_recordset = $Query -> fetchAll ();
$Query->closeCursor();
unset($db_Obj);
/* F I N   D U   C H A R G E M E N T */

/* E C R I T U R E   D U   P D F */
class PDF extends FPDF   {
	public $font;
	public $titre;
	public $baspage;
	public $dateparution;
	public $niveau;
	public $note;
	//Entête
	function Header()   {
		//	$this->SetFont($this->font,'I',10);							// Police italique 8
		$this->SetY(10);
		$this->SetTextColor(0, 0, 0);			// Police : Couleur noire
		$this->SetFont($this->font,'I',10);
		$this->Cell(0,0,$this->titre,0,2,'L');	
		$this->Ln(10);	
		
	//	$this->Cell(0,10,utf8_decode($this->titre);					// Numéro de page aligné à gauche
	}
	// Pied de page
	function Footer()   {
		$this->SetTextColor(0, 0, 0);								// Police : Couleur noire
		$this->SetY(-15);											// Positionnement à 1,5 cm du bas
		$this->SetFont($this->font,'I',6);							// Police : italique 8
		$this->Cell(0,10,$this->baspage,0,0,'L');		// Texte bas de page
		$this->SetFont($this->font,'I',12);							// Police : italique 8
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');  // Numéro de page aligné à droite
	}
	// Couverture du questionnaire
	function Couverture() {
		//$pdf->Image('logo.png',10,6,30);				// Logo
		$this->SetTextColor(0, 0, 0);					// Police : Couleur noire
		$this->Ln(10);									// Saut de ligne
		$this->SetFont($this->font,'B',30);				// Police :  gras 30
		$this->Cell(70);								// Décalage à droite
		$this->Cell(30,10,$this->titre,0,0,'C');		// Titre
		$this->Ln(20);									// Saut de ligne
		$this->SetFont($this->font,'B',18);				// Police : gras 18
		$this->Cell(0 ,10,$this->dateparution,0,1,'C');	// Date de parution
		$this->Ln(10);									// Saut de ligne
		$this->Cell(0 ,10,$this->niveau,0,1,'C');		// Niveau
		$this->Ln(20);									// Saut de ligne
		$this->Cell(0 ,10,utf8_decode($this->note),0,1,'C');	// Note
		$this->Ln(30);
		$this->Cell(0 ,10, 'Nom du candidat : ________________',0,1,'C');// Nom
		$this->Ln(10);
		$this->Cell(0 ,10, 'Note obtenue : ________________',0,1,'C');// Not
		$this->Ln(20);
		$this->SetFont($this->font,'B',12);		// Police Times gras 12
		$this->Ln(10);						// Saut de ligne
		$this->Cell(0 ,10, 'Consignes :',0,1,'L');// Note
		$this->SetFont($this->font,'',10);		// Police Times12
		$this->MultiCell(0, 8, utf8_decode('
		1 - Le terme « Décisions » est systématiquement utilisé au pluriel même lorsque celle-ci est unique.
		2 - Les candidats peuvent utiliser les abréviations suivantes SRA, HJ, SDB, SRP loi 8, SRP loi 13, SDR, RT, CPB, BAT, CFI, CFD, Py, AVT, CAS, EXC, RCC lorsque celles-ci ne prêtent à aucune équivoque.
		3 - Toute absence de la mention « Rapport » lorsqu\'elle est exigée, sera sanctionnée d\'un retrait de 0,25 point à la question concernée.
		4 - Toute absence ou erreur de motif disciplinaire devant être mentionné lors d\'un avertissement ou d\'une exclusion, sera sanctionnée d\'un retrait de 0,25 point à la question concernée.'), 0, 'J');
	}
	//Ajout d'une page pour un nouveau niveaut de notation de question
	function QuestionTitle($questiontitle)  {
		$this->AddPage();
		$this->SetTextColor(0, 0, 0);			// Police : Couleur noire
		$this->SetFont($this->font,'B',20);		// Police : gras 20
		$this->Cell(0,0,$questiontitle,0,0,'C');				
		$this->Ln(10);
	}
	//Ajout d'une question
	function Question($nb, $lb)  {
		$this->SetTextColor(0, 0, 0);		// Police : Couleur noire
		$this->SetFont($this->font,'B',10);	// Police : gras 10	
		$this->Write(10, $nb);
		$this->Ln(10);
		$this->MultiCell(0, 8, $lb, 0, 'J');
		$this->Ln(5);
	}
	//Ajout d'une réponse
	function Reponse($lb) {
		$this->SetTextColor(0, 0, 255);		// Police : Couleur bleue
		$this->SetFont($this->font,'',10);	// Police : gras 10
		$this->MultiCell(0, 8, $lb, 0, 'J');
		$this->Ln(4);
	}
	//Ajout Notation d'une réponse
	function Notation($lb) {
		$this->SetTextColor(100, 0, 0);		// Police : Couleur rouge foncée
		$this->SetFont($this->font,'I',10);	// Police : 10
		$this->Write(6, $lb);
		$this->Ln(4);
	}
	//Ajout Source d'une réponse
	function Source($lb) {
		$this->SetTextColor(0, 100, 0);		// Police : Couleur verte
		$this->SetFont($this->font,'', 8);	// Police : 8
		$this->Write(6, $lb);
		$this->Ln();
	}
	//Ajout d'un texte mettant en garde car la question n'est pas validée
	function NoValidation($lb) {
		$this->SetTextColor(255, 0, 0);		// Police : Couleur rouge vif
		$this->SetFont($this->font,'IB',8);	// Police : Gras Italique 8
		$this->Write(4, $lb);
	}
	//Ajout Grille de notation
	function GrilleNotation($title, $contenu, $resume) {
		$this->AddPage();
		$this->SetFont($this->font,'B',30);			// Police gras 30
		$this->Cell(80,10,$title,0,0,'C');	
		$this->Ln(15);
		$this->SetFont($this->font,'',12);			// Police 12
		$this->MultiCell(0, 8, $contenu, 0, 'J');
		$this->SetFont($this->font,'B',12);			// Police gras 12
		$this->MultiCell(0, 8, $resume, 0, 'J');
	}
}

//*****************************************************************************
//*****************************DEMARRAGE***************************************
//*****************************************************************************
$pdf = new PDF();
$pdf->SetTitle($T_recordset[0], 1);
$pdf->SetSubject($T_recordset[0], 1);
$pdf->SetAuthor($appli_name	, 1);
$pdf->SetCreator($appli_name, 1);
$pdf->SetAutoPageBreak(1, 20);
$pdf->AliasNbPages();
$pdf->font			= $PDFfont;
$pdf->AddPage();
$pdf->SetMargins(10, 20, 10);
$pdf->titre			= $lbTest;
$pdf->baspage		= utf8_decode('édité le '.date('d/m/Y').' sur www.formationarbitre.com');
$pdf->dateparution	= $dtTest;
$pdf->niveau		= $Niveau;
$pdf->note			= 'Note maximale : '.$noteTest.' points';
//*****************************Couverture***************************************
$pdf->Couverture();
//*****************************Questions***************************************
$compteur 	= 0;
$note		= 0;
$grille		= '';
foreach ($Q_recordset as $Record)   {
    $compteur++;
	$lbQuestion			= utf8_decode($Record[0]);
	$lbReponse			= utf8_decode($Record[1]);
	$noteReponse		= utf8_decode($Record[2]);
	$notationReponse	= utf8_decode($Record[3]);
	$idstatut			= utf8_decode($Record[4]);
	$source				= utf8_decode($Record[5]);
	$typequestion		= utf8_decode($Record[6]);
	$grille.=$typequestion.' : Q'.$compteur.' => ___/ '.$noteReponse.chr(10);
	// Saut de page si changement de note
	if ($noteReponse!=$note)   {
        $note=$noteReponse;
        $pdf->QuestionTitle(utf8_decode('Questions à '.$noteReponse.' points'));
	}
	// Saut de page si pas de place pour question suivante
	if ($pdf->getY()>210) $pdf->AddPage();	
	$pdf->Question(utf8_decode('Question n°'.$compteur), $lbQuestion);
	//Si le questionnaire possède les réponses
	if ($flagReponse)   {		
		$pdf->Reponse($lbReponse);
		//Si le questionnaire possède la notation
		if ($flagNotation) {  
			$pdf->Notation($notationReponse);
		}
		if ($source!=null) $pdf->Source('Source : '.$source);
		if ($idstatut!=1)  $pdf->NoValidation(utf8_decode('La réponse à cette question est en cours de validation'));
		$pdf->Ln(20);
	}
	else
	    $pdf->Ln(70);
}
//*****************************Grille de notation***************************************
if (!$flagReponse)   {	
	$pdf->GrilleNotation('Grille de notation', $grille, 'Total => ____/ '.$noteTest);
}

$pdf->Output();