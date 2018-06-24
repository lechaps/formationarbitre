<?php

/* C A L C U L  L A   D I F F I C U L T E   D ' U N E   Q U E S T I O N */
function getDifficult($nb, $level)  {
	return ceil($nb/$level*100); 
}

/* E N C A P S U L A T I O N   D ' U N E   C O N D I T I O N */
function ifEncapsulator($arg1, $arg2, $result )   {
	if ($arg1==$arg2)
		return $result;
}

/* R E C U P E R A T I O N   D ' U N E   V A R I A B L E   P O S T */
function GetPOST($field)   {
	if (isset($_POST[$field]))
		$var = trim($_POST[$field]);
	else
		die("Manque le POST de la variable ".$field);
	return $var;
}

/* R E C U P E R A T I O N   D ' U N E   V A R I A B L E   P O S T   A V E C   V A L E U R   P A R   D E F A U T */
function GetPOSTorDEFAULT($field, $default)   {
	if (isset($_POST[$field]))
		if (is_array($_POST[$field]))
			$var = $_POST[$field];
		else
			$var = trim($_POST[$field]);
	else
		$var = $default;
	return $var;
}

/* R E C U P E R A T I O N   D ' U N E   V A R I A B L E   G E T */
function GetGET($field)   {
	if (isset($_GET[$field]))
		$var = trim($_GET[$field]);
	else
		die("Manque le GET de la variable ".$field);
	return $var;
}

/* R E C U P E R A T I O N   D ' U N E   V A R I A B L E   G E T   A V E C   V A L E U R   P A R   D E F A U T */
function GetGETorDEFAULT($field, $default)   {
	if (isset($_GET[$field]))
		if (is_array($_GET[$field]))
			$var = $_GET[$field];
		else
			$var = trim($_GET[$field]);
	else
		$var = $default;
	return $var;
}

/* R E M P L A C E M E N T   D E S   C A R A C T E R E S   A C C E N T U E S */
function Suppr_accents($str)   {
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
  return str_replace($a, $b, $str);
}

/* F O R M A T A G E   D ' U N E   C H A I N E   E N   R E T I R A N T   L E S   C A R A C T E R E S   S U P E R F L U X */
function  cleanString($string)   {
	$string = str_replace("’", "'", $string); 					//Suppression des ’
	$string = str_replace("–", "-", $string);					//Suppression des –
	return $string;
}

/* F O R M A T A G E   D ' U N E   C H A I N E   P O U R   D E T E C T I O N   U N I C I T E */
function formatUniqueString($string)   {
	$string = preg_replace("/(\r\n|\n|\r)/", " ", $string); 	//Suppression des sauts de lignes
	$string = Suppr_accents($string);							//Suppression des accents
	$string = preg_replace("/[^a-z0-9]+/i", " ", $string);		//Suppression de la ponctuation
	$string = mb_strtoupper($string);
	$words	= array(' A ', ' AU ', ' B ', ' CAR ', ' CE ', ' CELA ', ' CES ', ' CEUX ', ' CI ', ' D ',' DE ', ' DES ', ' DONC ', ' DU ', ' EN ', ' ET ', ' L ', ' LA ', ' LE ', ' LES ', ' LEURS ', 
					' MA ', ' MAIS ', ' MES ', ' MON ', ' NI ', ' NOTRE ', ' NOUS ', ' OR ', ' OU ', ' POUR ', ' SA ', ' SES ', ' SIEN ', ' SON ', ' TA ', ' TELS ', 
					' TES ', ' TON ', ' TOUS ', ' TOUT ', ' TU ', ' UN ', ' UNE ', ' VOTRE ', ' VOUS '); 
	$string = str_replace($words, " ", ' '.$string.' '); 		//Suppression des mots inutiles
	return trim($string);
}

/* F O R M A T A G E   D E   D A T E */
function Format_Date($input, $formatinput, $end, $formatouput)   {
	if (str_replace(" ", "" ,$input)=="")
		return "";
	if ($end!="")   {
		$posEnd		= strpos($input, $end);
		$int_date 	= substr($input, 0, $posEnd);
	}
	else {
		$int_date	= $input;
	}

	switch ($formatinput)
	{
		case "AAAAMMJJ":
			$year	= substr($int_date, 0, 4);
			$month	= substr($int_date, 4, 2);
			$day	= substr($int_date, 6, 2);
			break;
		case "AAAA-MM-JJ":
			list ($year, $month, $day) = explode ('-', $int_date);
			break;
		case "JJ/MM/AAAA":
			list ($day, $month, $year) = explode ('/', $int_date);
			break;
		case "MM/JJ/AAAA":
			list ($month, $day, $year) = explode ('/', $int_date);
			break;
		case "AAAAMMJJHHmmss":	
			$year	= substr($int_date, 0, 4);
			$month	= substr($int_date, 4, 2);
			$day	= substr($int_date, 6, 2);
			$hour	= substr($int_date, 8, 2);
			$minute	= substr($int_date, 10, 2);
			$second	= substr($int_date, 12, 2);
			break;
		case "AAAA-MM-JJ HH:mm:ss":
			list($date, $time)			= explode(" ", $int_date);			
			list($year, $month, $day) 	= explode ("-", $date);
			list($hour, $minute, $second)= explode (":", $time);	
			break;
		case "AAAA-MM-JJ HH:mm":
			list($date, $time)			= explode(" ", $int_date);			
			list($year, $month, $day) 	= explode ("-", $date);
			list($hour, $minute)		= explode (":", $time);	
			$second = "00";
			break;
		case "JJ/MM/AAAA HH:mm":
			list($date, $time)			= explode(" ", $int_date);			
			list($day, $month, $year ) 	= explode ("/", $date);
			list($hour, $minute)		= explode (":", $time);	
			$second = "00";
			break;
		default:
			$output = "le format Entrée n'est pas pris en charge par le système";
			break;
	}
	
	switch ($formatouput) {
		case "JJ/MM/AAAA":
			$output	= $day."/".$month."/".$year;
			break;
		case "AAAAMMJJ":
			$output	= $year.$month.$day;
			break;
		case "MM/JJ/AAAA":
			$output	= $month."/".$day."/".$year;
			break;
		case "MM/JJ/AAAA HH:mm":
			$output	= $month."/".$day."/".$year." ".$hour.":".$minute;
			break;
		case "AAAA-MM-JJ":
			$output	= $year."-".$month."-".$day;
			break;
		case "JJ/MM/AAAA HH:mm":
			$output	= $day."/".$month."/".$year." ".$hour.":".$minute;
			break;
		case "JJ/MM/AAAA HH:mm:ss":
			$output	= $day."/".$month."/".$year." ".$hour.":".$minute.":".$second;
			break;
		case "AAAA-MM-JJ HH:mm:ss":
			$output	= $year."-".$month."-".$day." ".$hour.":".$minute.":".$second;
			break;
		case "AAAA-MM-JJ HH:mm":
			$output	= $year."-".$month."-".$day." ".$hour.":".$minute;
			break;
		case "HH:mm:ss":
			$output	= $hour.":".$minute.":".$second;
			break;
		case "HH:mm":
			$output	= $hour.":".$minute;
			break;
		default:
			$output ="le format Sortie n'est pas pris en charge par le système";
			break;
	}
	return $output;
}

/* G E S T I O N   B O U T O N   +   M O D A L   B O X   +   A C T I O N */
function modalConfirmation ($MBid, $MBlibelle, $BTNclass, $BTNlibelle, $BTNtitle, $BTNicone, $link, $explication)   {
	$html='	<button type="button" class="btn btn-'.$BTNclass.' btn-sm" data-toggle="modal" data-target=".bs-modal-lg'.$MBid.'" Title="'.$BTNtitle.'"><span class="glyphicon glyphicon-'.$BTNicone.'"></span></button>
			<div class="modal fade bs-modal-lg'.$MBid.'" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="myModalLabel">'.$MBlibelle.'</h4>
						</div>
						<div class="modal-body">'.$explication.'</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove">&nbsp;</span>Annuler</button>
							<a href="'.$link.'" class="btn btn-'.$BTNclass.'" Title="'.$BTNtitle.'"><span class="glyphicon glyphicon-'.$BTNicone.'">&nbsp;</span>'.$BTNlibelle.'</a>
						</div>
					</div>
				</div>
			</div>';
	return $html;
}
?>