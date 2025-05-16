<?php
// make friendly url using any string
if (!function_exists('friendlyURL')) {
	function friendlyURL($inputString){
		$url = strtolower($inputString);
		$patterns = $replacements = array();
		$patterns[0] = '/(&amp;|&)/i';
		$replacements[0] = '-and-';
		$patterns[1] = '/[^a-zA-Z01-9]/i';
		$replacements[1] = '-';
		$patterns[2] = '/(-+)/i';
		$replacements[2] = '-';
		$patterns[3] = '/(-$|^-)/i';
		$replacements[3] = '';
		$url = preg_replace($patterns, $replacements, $url);
	return $url;
	}
}

if (!function_exists('sanitizedNumber')) {
	function replaceSpecialCharInUrl($str)
	{
		$find     =   array("\t","\n"," ");
		$replace  =   array("","","");
		return str_replace($find,$replace,$str);
	}
}

// sanitized number :  function auto remove unwanted character form given value 
if (!function_exists('sanitizedNumber')) {
	function sanitizedNumber($_input) 
	{ 
		return (float) preg_replace('/[^0-9.]*/','',$_input); 
	}
}

// sanitized filename :  function auto remove unwanted character form given file name
if (!function_exists('sanitizedFilename')) {
	function sanitizedFilename($filename){
		$sanitized = preg_replace('/[^a-zA-Z0-9-_\.]/','', $filename);
		return $sanitized;
	}
}

// check, is file exist in folder or not
if (!function_exists('fileExist')) {
	function fileExist($source='', $file='', $defalut=''){
		if(!$file) return base_url().$source.$defalut;
			
		if(file_exists(FCPATH.$source.$file)):
			return base_url().$source.$file;
		else:
			return base_url().$source.$defalut;
		endif;
	}
}

// check, is file exist in folder or not
if (!function_exists('checkFileExist')) {
	function checkFileExist($source=''){
		if(file_exists(FCPATH.$source)):
			return base_url().$source;
		else:
			return false;
		endif;
	}
}

if (!function_exists('myExplode')) {
	function myExplode($string){
		if($string):
			$array = explode(",",$string);
			return $array;
		else:
			return '';
		endif;
	}
}

/*
 * Show correct image
 */
if (!function_exists('correctImage')) {
	function correctImage($imageurl, $type = '') {
		if($imageurl <> ""):
			if($type=='original'):
				$imageurl = str_replace('/thumb','',$imageurl);
			elseif($type):
				$imageurl = str_replace('thumb',$type,$imageurl);
			endif;
		else:
			$imageurl  = 'assets/admin/images/user-avatar-big-01.jpg';
		endif;
		return trim($imageurl);
	}
}

/*
 * Encription
 */
if (!function_exists('manojEncript')) {
	function manojEncript($text) {
		$text	=	('MANOJ').$text.('KUMAR');
		return	base64_encode($text);
	}
}

/*
 * Decription
 */
if (!function_exists('manojDecript')) {
	function manojDecript($text) {
		$text	=	base64_decode($text);
		$text	=	str_replace(('MANOJ'),'',$text);
		$text	=	str_replace(('KUMAR'),'',$text);
		return $text;
	}
}

/*
 * Encryption
 */
if (!function_exists('manojEncrypt')) {
	function manojEncrypt($text) {
		$text	=	('MANOJ').$text.('KUMAR');
		return	base64_encode($text);
	}
}
/*
 * Decryption
 */
if (!function_exists('manojDecrypt')) {
	function manojDecrypt($text) {
		$text	=	base64_decode($text);
		$text	=	str_replace(('MANOJ'),'',$text);
		$text	=	str_replace(('KUMAR'),'',$text);
		return $text;
	}
}
/*
 * Word Limiter
 */
define("STRING_DELIMITER", " ");
if (!function_exists('wordLimiter')){
	function wordLimiter($str, $limit = 10){
		$str = strip_tags($str); 
		if (stripos($str, STRING_DELIMITER)){
			$ex_str = explode(STRING_DELIMITER, $str);
			if (count($ex_str) > $limit){
				for ($i = 0; $i < $limit; $i++){
					$str_s.=$ex_str[$i].'&nbsp;';
				}
				return $str_s.'...';
			}else{
				return $str;
			}
		}else{
			return $str;
		}
	}
}

if (!function_exists('currentDateTime')) {
	function currentDateTime() {
		return time();
	}
}

if (!function_exists('showDate')) {
	function showDate($time='') {
		if($time):
			return date("Y-m-d",$time);
		else:
			return false;
		endif;
	}
}

if (!function_exists('showTime')) {
	function showTime($time='') {
		if($time):
			return date("H:i:s",$time);
		else:
			return false;
		endif;
	}
}

if (!function_exists('showDateTime')) {
	function showDateTime($time='') {
		if($time):
			return date("d-m-Y H:i:s",$time);
		else:
			return false;
		endif;
	}
}


if (!function_exists('currentIp')) {
	function currentIp() {
		return $_SERVER['REMOTE_ADDR']=='::1'?'192.168.1.100':$_SERVER['REMOTE_ADDR'];
	}
}

if (!function_exists('generateRandomString2')) {
	function generateRandomString2($length = 15, $mode="n") {
		$characters = "";
		if(strpos($mode,"s")!==false){$characters.="abcdefghjklmnpqrstuvwxyz";}
		if(strpos($mode,"l")!==false){$characters.="ABCDEFGHJKLMNPQRSTUVWXYZ";}
		if(strpos($mode,"n")!==false){$characters.="0123456789";}
		$charactersLength = strlen($characters);
		$randomString = '';
		$randomString .= $characters[rand(1, $charactersLength - 1)];
		for ($i = 0; $i < $length - 1; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		if(strlen($randomString) < $length):
			generateRandomString($length,$mode);
		else:
			return $randomString;
		endif;
	}
}
if (!function_exists('generateRandomString')) {
	function generateRandomString() {
		$length = 16;
		$characters = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}
		return $randomString;
	}
}

if (!function_exists('displayPrice')) {
	function displayPrice($price) {
		if(checkFloat($price)):
			return "Rs. ".number_format($price, 2, '.', '');
		else:
			return "Rs. ".number_format($price);
		endif;
	}
}

if (!function_exists('numberFormat')) {
	function numberFormat($price) {
		if(checkFloat($price)):
			return number_format($price, 2, '.', '');
		else:
			return number_format($price, 2, '.', '');
		endif;
	}
}

if (!function_exists('displayPercent')) {
	function displayPercent($price) {
		if(checkFloat($price)):
			return number_format($price, 2, '.', '').'%';
		else:
			return number_format($price).'%';
		endif;
	}
}

if (!function_exists('calculatePercent')) {
	function calculatePercent($prevpr,$curpr) {
		$val = $prevpr-$curpr;
		if($val<=0):
			$amountper = 0;
		else:
			$amountper = (($prevpr-$curpr)/($prevpr))*100;
		endif;
		if($amountper==100):
			$amountper = 0;
		endif;
		if(checkFloat($amountper)):
			return number_format($amountper,2, '.', '');
		else:
			return number_format($amountper);
		endif;
	}
}

if (!function_exists('checkFloat')) {
	function checkFloat($s_value) {
		$regex = '/^\s*[+\-]?(?:\d+(?:\.\d*)?|\.\d+)\s*$/';
		return preg_match($regex, $s_value);
	}
}

/*
 * Get session data
 */
if (!function_exists('sessionData')) {
	function sessionData($text) {
		$CIOBJ = & get_instance();
		return	$CIOBJ->session->userdata($text);
	}
}

/*
 * Get correct link
 */
if (!function_exists('correctLink')) {
	function correctLink($text='',$link='') {
		return	sessionData($text)?sessionData($text):$link;
	}
}

/*
 * Get full url
 */
 if (!function_exists('currentFullUrl')) {
	function currentFullUrl()
	{
		$CI =& get_instance();
		$url = $CI->config->site_url($CI->uri->uri_string());
		return $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;
	}
}

if (!function_exists('generateUniqueId')) {
	function generateUniqueId($currentId = 1) {
		$newId		=	10000000000000+$currentId;
		return $newId;
	}
}

if (!function_exists('generateToken')) {
	function generateToken() {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";	
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 32; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

/*
 * convert time to date
 */
if (!function_exists('convertTimeToDate')) {
	function convertTimeToDate($time='') {
		return $time?date('d-m-Y',$time):'';
	}
}

/*
 * convert date to time
 */
if (!function_exists('convertDateToTime')) {
	function convertDateToTime($date='') {
		return $date?strtotime($date):'';
	}
}

/*
 * Show Current MK Time
 */
if (!function_exists('getCurrentMKTime')) {
	function getCurrentMKTime()
	{  
		return mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));;
	}
}

/*
* Get diff Bet Two Date
*/
if (!function_exists('diffBetTwoDate')) {
	function diffBetTwoDate($firstDate='',$secondDate=''){
		$daysLeft 		= 	abs($firstDate - $secondDate);
		$days 			= 	$daysLeft/(60*60*24);
		return $days;
	}
}

if (!function_exists('diffBetTwoStandardDate')) {
	function diffBetTwoStandardDate($firstDate='',$secondDate=''){
		$daysLeft 		= 	abs($secondDate - $firstDate);
		$days 			= 	$daysLeft/(60*60*24);
		return $days;
	}
}

/*
* Get day range list between two date
*/
if (!function_exists('dateRangeBetweenTwoDate')) {
	function dateRangeBetweenTwoDate($first='',$last='',$step='+1 day',$outputFormat='d-m-Y') {
		$dates		= 	array();
		$current 	= 	$first;
		$last 		= 	$last;
		while($current <= $last):
			$dates[] 	= 	date($outputFormat,$current);
			$current 	= 	strtotime($step,$current);
		endwhile;
		return $dates;
	}
}


if (!function_exists('array_sort_by_column')) {
	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC, $type = SORT_NUMERIC) {
	    $sort_col = array();	
	    foreach ($arr as $key=> $row) {
	        $sort_col[$key] = $row[$col];
	    }
	    $sort_col = array_map('strtolower', $sort_col);
	    array_multisort($sort_col, $dir, $type, $arr);
	}
}

if (!function_exists('showImage')) {
	function showImage($path='') {
	    if($path <> ""):
	    	$image 	=	base_url().$path;
	    endif;
	    return $image;
	}
}
/*
 * convert time to date Format
 */
if (!function_exists('convertTimeToDateFormat')) {
	function convertTimeToDateFormat($time='') {
		return $time?date('Y-m-d',$time):'';
	}
}

/*
 * Change ddmmyy to yymmdd
 */
if (!function_exists('TimetoMMYY')) {
	function TimetoMMYY($date) {
		$datedata			=	explode('-',$date); 
		if(isset($datedata[0]) && isset($datedata[1]) && isset($datedata[2])):
			return $date?date('F Y',strtotime($datedata[2].'-'.$datedata[1].'-'.$datedata[0])):'';
		else:
			return $date?date('F Y',$date):'';
		endif;
	}
}

/*
 * Change ddmmyy to yymmdd
 */
if (!function_exists('DDMMYYtoYYMMDD')) {
	function DDMMYYtoYYMMDD($date) {
		if($date):
			$datedata			=	explode('-',$date);
			$datedata			=	$datedata[2].'-'.$datedata[1].'-'.$datedata[0];
		else:
			$datedata			=	'';
		endif;
		return $datedata;
	}
}

/*
 * Change yymmdd to ddmmyy
 */
if (!function_exists('YYMMDDtoDDMMYY')) {
	function YYMMDDtoDDMMYY($date) {
		if($date && $date != '1970-01-01 00:00:00' && $date != '0000-00-00 00:00:00'):
			$datedata			=	explode(' ',$date);
			$datedata			=	explode('-',$datedata[0]);
			$datedata			=	$datedata[2].'-'.$datedata[1].'-'.$datedata[0];
		else:
			$datedata			=	'';
		endif;
		return $datedata;
	}
}
	
/*
 * show price format
 */
if (!function_exists('showPriceFormat')) {
	function showPriceFormat($price=''){
		return number_format($price,2);//$price>0?$price:'0.00';
	}
}	

/*
 * show correct public profile image
 */
if (!function_exists('showProductPrice')) {
	function showProductPrice($price=''){
		return number_format($price,2);//$price>0?$price:'0.00';
	}
}
/*
 * show correct files
 */
if (!function_exists('showCorrectFile')) {
	function showCorrectFile($fileLink) {
		if($fileLink):
			$fileLinkArray 	=	explode('assets/',$fileLink);
			$fileLink 		=	fileBaseUrl.'assets/'.$fileLinkArray[1];
		endif;
		return $fileLink;
	}
}

/*
 * generate session id
 */
if (!function_exists('generateSessionId')) {
	function generateSessionId(){
		$uniqueId = uniqid(rand(),TRUE);
		return md5($uniqueId);
	}
}

if (!function_exists('generateCODTnxId')) {
	function generateCODTnxId() {
		$characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";	
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 15; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return 'COD'.$randomString;
	}
}

if (!function_exists('generateAffiliateId')) {
	function generateAffiliateId() {
		$characters = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";	
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < 6; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return 'AP'.$randomString;
	}
}

/*
 * pages list
 */
if (!function_exists('pagesListData')) {
	function pagesListData() {
		$pagesArray			=	array('GrandPrizeBanner'	=>	"Grand Prize Banner",
									  'Homeslider'			=>	"Home Slider",
									  'Homebanner'			=>	"Home Banner",
								  	  );
		return $pagesArray;
	}
}

/*
 * pages list for video
 */
if (!function_exists('videoPagesListData')) {
	function videoPagesListData() {
		$pagesArray			=	array("Webvideo"			=>	"Web Video",
									  "Appvideo"			=>	"App Video",
									  
								  	  );
		return $pagesArray;
	}
}

/*
 * country code
 */
if (!function_exists('countryCodeList')) {
	function countryCodeList() {
		$countryCodeArray			=	array('+971' => "United Arab Emirates +971",
											  '+966' => "Saudi Arabia +966",
											  '+93' => "Afghanistan +93",
											  '+355' => "Albania +355",
											  '+213' => "Algeria +213",
											  '+1684' => "AmericanSamoa +1684",
											  '+376' => "Netherlands Antilles +599",
											  '+244' => "Angola +244",
											  '+1264' => "Anguilla +1264",
											  '+672' => "Antarctica +672",
											  '+1268' => "Antigua and Barbuda +1268",
											  '+54' => "Argentina +54",
											  '+374' => "Armenia +374",
											  '+297' => "Aruba +297",
											  '+61' => "Australia +61",
											  '+43' => "Austria +43",
											  '+994' => "Azerbaijan +994",
											  '+1242' => "Bahamas +1242",
											  '+973' => "Bahrain +973",
											  '+880' => "Bangladesh +880",
											  '+1246' => "Barbados +1246",
											  '+375' => "Belarus +375",
											  '+32' => "Belgium +32",
											  '+501' => "Belize +501",
											  '+229' => "Benin +229",
											  '+1441' => "Bermuda +1441",
											  '+975' => "Bhutan +975",
											  '+591' => "Bolivia, Plurinational State of +591",
											  '+387' => "Bosnia and Herzegovina +387",
											  '+267' => "Botswana +267",
											  '+47' => "Bouvet Island +47",
											  '+55' => "Brazil +55",
											  '+246' => "British Indian Ocean Territory +246",
											  '+673' => "Brunei +673",
											  '+359' => "Bulgaria +359",
											  '+226' => "Burkina Faso +226",
											  '+257' => "Burundi +257",
											  '+855' => "Cambodia +855",
											  '+237' => "Cameroon +237",
											  '+1' => "Canada +1",
											  '+238' => "Cape Verde +238",
											  '+345' => "Cayman Islands +345",
											  '+236' => "Central African Republic +236",
											  '+235' => "Chad +235",
											  '+56' => "Chile +56",
											  '+86' => "China +86",
											  '+61' => "Christmas Island +61",
											  '+61' => "Cocos (Keeling) Islands +61",
											  '+57' => "Colombia +57",
											  '+269' => "Comoros +269",
											  '+242' => "Congo - Brazzaville +242",
											  '+243' => "Congo - Kinshasa +243",
											  '+682' => "Cook Islands +682",
											  '+506' => "Costa Rica +506",
											  '+225' => "Cote d'Ivoire +225",
											  '+385' => "Croatia +385",
											  '+53' => "Cuba +53",
											  '+357' => "Cyprus +357",
											  '+420' => "Czech Republic +420",
											  '+45' => "Denmark +45",
											  '+253' => "jibouti +253D",
											  '+1767' => "Dominica +1767",
											  '+1849' => "Dominican Republic +1849",
											  '+593' => "Ecuador +593",
											  '+20' => "Egypt +20",
											  '+503' => "El Salvador +503",
											  '+240' => "Equatorial Guinea +240",
											  '+291' => "Eritrea +291",
											  '+372' => "Estonia +372",
											  '+251' => "Ethiopia +251",
											  '+500' => "Falkland Islands (Malvinas) +500",
											  '+298' => "Faroe Islands +298",
											  '+679' => "Fiji +679",
											  '+358' => "Finland +358",
											  '+33' => "France +33",
											  '+594' => "French Guiana +594",
											  '+689' => "French Polynesia +689",
											  '+241' => "Gabon +241",
											  '+220' => "Gambia +220",
											  '+995' => "Georgia +995",
											  '+49' => "Germany +49",
											  '+233' => "Ghana +233",
											  '+350' => "Gibraltar +350",
											  '+30' => "Greece +30",
											  '+299' => "Greenland +299",
											  '+1473' => "Grenada +1473",
											  '+590' => "Guadeloupe +590",
											  '+1671' => "Guam +1671",
											  '+502' => "Guatemala +502",
											  '+44' => "Guernsey +44",
											  '+224' => "Guinea +224",
											  '+245' => "Guinea-Bissau +245",
											  '+595' => "Guyana +595",
											  '+509' => "Haiti +509",
											  '+379' => "Holy See (Vatican City State) +379",
											  '+504' => "Honduras +504",
											  '+852' => "Hong Kong +852",
											  '+36' => "Hungary +36",
											  '+354' => "Iceland +354",
											  '+91' => "India +91",
											  '+62' => "Indonesia +62",
											  '+98' => "Iran +98",
											  '+964' => "Iraq +964",
											  '+353' => "Ireland +353",
											  '+44' => "Isle of Man +44",
											  '+972' => "Israel +972",
											  '+39' => "Italy +39",
											  '+1876' => "Jamaica +1876",
											  '+81' => "Japan +81",
											  '+44' => "Jersey +44",
											  '+962' => "Jordan +962",
											  '+77' => "Kazakhstan +77",
											  '+254' => "Kenya +254",
											  '+686' => "Kiribati +686",
											  '+850' => "North Korea +850",
											  '+82' => "South Korea +82",
											  '+965' => "Kuwait +965",
											  '+996' => "Kyrgyzstan +996",
											  '+856' => "Laos +856",
											  '+371' => "Latvia +371",
											  '+961' => "Lebanon +961",
											  '+266' => "Lesotho +266",
											  '+231' => "Liberia +231",
											  '+218' => "Libya +218",
											  '+423' => "Liechtenstein +423",
											  '+370' => "Lithuania +370",
											  '+352' => "Luxembourg +352",
											  '+853' => "Macao +853",
											  '+389' => "Macedonia +389",
											  '+261' => "Madagascar +261",
											  '+265' => "Malawi +265",
											  '+60' => "Malaysia +60",
											  '+960' => "Maldives +960",
											  '+223' => "Mali +223",
											  '+356' => "Malta +356",
											  '+692' => "Marshall Islands +692",
											  '+596' => "Martinique +596",
											  '+222' => "Mauritania +222",
											  '+230' => "Mauritius +230",
											  '+262' => "Mayotte +262",
											  '+52' => "Mexico +52",
											  '+691' => "Micronesia +691",
											  '+373' => "Moldova +373",
											  '+377' => "Monaco +377",
											  '+976' => "Mongolia +976",
											  '+382' => "Montenegro +382",
											  '+166' => "Montserrat +166",
											  '+212' => "Morocco +212",
											  '+258' => "Mozambique +258",
											  '+95' => "Myanmar +95",
											  '+264' => "Namibia +264",
											  '+674' => "Nauru +674",
											  '+977' => "Nepal +977",
											  '+31' => "Netherlands +31",
											  '+599' => "Netherlands Antilles +599",
											  '+687' => "New Caledonia +687",
											  '+64' => "New Zealand +64",
											  '+505' => "Nicaragua +505",
											  '+227' => "Niger +227",
											  '+234' => "Nigeria +234",
											  '+683' => "Niue +683",
											  '+672' => "Norfolk Island +672",
											  '+1670' => "Northern Mariana Islands +1670",
											  '+47' => "Norway +47",
											  '+968' => "Oman +968",
											  '+92' => "Pakistan +92",
											  '+680' => "Palau +680",
											  '+970' => " Palestinian Territories +970",
											  '+507' => "Panama +507",
											  '+675' => "Papua New Guinea +675",
											  '+595' => "Paraguay +595",
											  '+51' => "Peru +51",
											  '+63' => "Philippines +63",
											  '+872' => "Pitcairn +872",
											  '+48' => "Poland +48",
											  '+351' => "Portugal +351",
											  '+1939' => "Puerto Rico +1939",
											  '+974' => "Qatar +974",
											  '+40' => "Romania +40",
											  '+7' => "Russia +7",
											  '+250' => "Rwanda +250",
											  '+262' => "Reunion +262",
											  '+590' => "St. Barthelemy +590",
											  '+290' => "St. Helen +290",
											  '+1869' => "St. Kitts and Nevis +1869",
											  '+1758' => "St. Lucia +1758",
											  '+590' => "St. Martin +590",
											  '+508' => "St. Pierre and Miquelon +508",
											  '+1784' => "St. Vincent and Grenadines +1784",
											  '+685' => "Samoa +685",
											  '+378' => "San Marino +378",
											  '+239' => "Sao Tome and Principe +239",
											  '+221' => "Senegal +221",
											  '+381' => "Serbia +381",
											  '+248' => "Seychelles +248",
											  '+232' => "Sierra Leone +232",
											  '+65' => "Singapore +65",
											  '+421' => "Slovakia +421",
											  '+386' => "Slovenia +386",
											  '+677' => "Solomon Islands +677",
											  '+252' => "Somalia +252",
											  '+27' => "South Africa +27",
											  '+211' => "South Sudan +211",
											  '+500' => "South Georgia and the South Sandwich Islands +500",
											  '+34' => "Spain +34",
											  '+94' => "Sri Lanka +94",
											  '+249' => "Sudan +249",
											  '+597' => "Suriname +597",
											  '+47' => "Svalbard and Jan Mayen +47",
											  '+268' => "Swaziland +268",
											  '+46' => "Sweden +46",
											  '+41' => "Switzerland +41",
											  '+963' => "Syrian Arab Republic +963",
											  '+886' => "Taiwan +886",
											  '+992' => "Tajikistan +992",
											  '+255' => "Tanzania +255",
											  '+66' => "Thailand +66",
											  '+670' => "Timor-Leste +670",
											  '+228' => "Togo +228",
											  '+690' => "Tokelau +690",
											  '+676' => "Tonga +676",
											  '+186' => "Trinidad and Tobago +186",
											  '+216' => "Tunisia +216",
											  '+90' => "Turkey +90",
											  '+993' => "Turkmenistan +993",
											  '+164' => "Turks and Caicos Islands +164",
											  '+688' => "Tuvalu +688",
											  '+256' => "Uganda +256",
											  '+380' => "Ukraine +380",
											  '+44' => "United Kingdom +44",
											  '+1' => "United States +1",
											  '+598' => "Uruguay +598",
											  '+998' => "Uzbekistan +998",
											  '+678' => "Vanuatu +678",
											  '+58' => "Venezuela +58",
											  '+84' => "Vietnam +84",
											  '+128' => "Virgin Islands, British +128",
											  '+134' => "Virgin Islands, U.S. +134",
											  '+681' => "Wallis and Futuna +681",
											  '+967' => "Yemen +967",
											  '+260' => "Zambia +260",
											  '+263' => "Zimbabwe +263"
								  	  		  );
		return $countryCodeArray;
	}
}